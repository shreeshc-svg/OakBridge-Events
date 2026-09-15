<?php

namespace App\Support;

use App\Models\Booking;
use App\Models\Competition;
use App\Models\Gallery;
use App\Models\PageSeo;
use App\Models\Post;
use App\Models\Service;
use App\Models\Setting;
use App\Models\Sponsor;
use App\Models\SponsorGroup;
use App\Models\Team;
use App\Models\Testimonial;
use App\Models\Video;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/** Numbers for the admin dashboard. Everything is read-only. */
class DashboardMetrics
{
    public function all(): array
    {
        $now = now();
        $setting = Setting::find(1);

        $nextEvent = Service::wherePublished('1')->where('date', '>', $now)->orderBy('date')->first();
        $focusEvent = $nextEvent ?: Service::wherePublished('1')->whereNotNull('date')->orderByDesc('date')->first();

        return [
            'now' => $now,
            'setting' => $setting,
            'nextEvent' => $nextEvent,
            'focusEvent' => $focusEvent,
            'kpis' => $this->kpis($now, $focusEvent),
            'daily' => $this->daily($now, 30),
            'byEvent' => $this->byEvent(),
            'topCompanies' => $this->top('company'),
            'topDesignations' => $this->top('designation'),
            'latest' => Booking::latest('id')->take(10)->get(['id', 'name', 'company', 'designation', 'event', 'created_at']),
            'checks' => $this->checks($setting, $nextEvent),
            'content' => $this->content(),
            'popularEvents' => Service::wherePublished('1')->orderByDesc('views')->take(5)->get(['id', 'title', 'slug', 'views']),
            'popularPosts' => Post::wherePublished('1')->orderByDesc('views')->take(5)->get(['id', 'title', 'slug', 'views']),
            'logins' => $this->logins($now),
        ];
    }

    private function kpis(Carbon $now, ?Service $event): array
    {
        $todayStart = $now->copy()->startOfDay();
        $last7 = Booking::where('created_at', '>=', $now->copy()->subDays(7))->count();
        $prev7 = Booking::whereBetween('created_at', [$now->copy()->subDays(14), $now->copy()->subDays(7)])->count();

        $eventCount = null;
        if ($event) {
            $eventCount = Booking::where('service_id', $event->id)->count();
        }

        return [
            'total' => Booking::count(),
            'today' => Booking::where('created_at', '>=', $todayStart)->count(),
            'last7' => $last7,
            'prev7' => $prev7,
            'delta7' => $prev7 > 0 ? round(($last7 - $prev7) / $prev7 * 100) : null,
            'event' => $eventCount,
            'untagged' => Booking::whereNull('service_id')->count(),
            'daysToEvent' => $event && $event->date->isFuture() ? (int) ceil($now->floatDiffInDays($event->date)) : null,
        ];
    }

    /** Registrations per day for the last $days days (oldest first). */
    private function daily(Carbon $now, int $days): array
    {
        $from = $now->copy()->subDays($days - 1)->startOfDay();
        $counts = Booking::where('created_at', '>=', $from)
            ->select(DB::raw('DATE(created_at) as day'), DB::raw('COUNT(*) as total'))
            ->groupBy('day')
            ->pluck('total', 'day');

        $series = [];
        for ($d = $from->copy(); $d->lte($now); $d->addDay()) {
            $key = $d->format('Y-m-d');
            $series[] = ['date' => $d->copy(), 'count' => (int) ($counts[$key] ?? 0)];
        }

        return $series;
    }

    private function byEvent(): array
    {
        return Service::wherePublished('1')
            ->withCount('bookings')
            ->orderByDesc('date')
            ->take(6)
            ->get()
            ->filter(fn ($e) => $e->bookings_count > 0 || ($e->date && $e->date->isFuture()))
            ->values()
            ->all();
    }

    /** Most common values of a free-text booking column, ignoring case and spaces. */
    private function top(string $column, int $limit = 8): array
    {
        return Booking::whereNotNull($column)
            ->whereRaw("TRIM($column) <> ''")
            ->select(DB::raw("MIN(TRIM($column)) as label"), DB::raw('COUNT(*) as total'))
            ->groupBy(DB::raw("LOWER(TRIM($column))"))
            ->orderByDesc('total')
            ->orderBy('label')
            ->take($limit)
            ->get()
            ->map(fn ($r) => ['label' => $r->label, 'total' => (int) $r->total])
            ->all();
    }

    /** Site health: [status, label, detail, link] with status good|warning|critical|info */
    private function checks(?Setting $setting, ?Service $nextEvent): array
    {
        $checks = [];

        if (config('app.debug')) {
            $checks[] = ['critical', 'Debug mode is on', 'Visitors can see error details. Set APP_DEBUG=false in .env.', null];
        }

        if ($nextEvent) {
            $checks[] = ['good', 'Upcoming event: ' . $nextEvent->title, $nextEvent->date->format('D, d M Y'), route('schedules.edit', $nextEvent)];
            if (! count($nextEvent->sessions())) {
                $checks[] = ['warning', 'The upcoming event has no sessions', 'Its schedule page says "to be announced".', route('schedules.edit', $nextEvent)];
            }
        } else {
            $checks[] = ['critical', 'No upcoming event', 'The registration form has no event to choose, so nobody can register.', route('schedules.index')];
        }

        $open = (bool) ($setting->registration_enabled ?? true);
        $checks[] = [$open ? 'good' : 'info', 'Registration is ' . ($open ? 'open' : 'closed'), null, route('registration.edit')];
        $checks[] = ['info', 'Hero banner ' . (($setting->hero_enabled ?? true) ? 'shown' : 'hidden'), null, route('hero.edit')];
        $checks[] = ['info', 'Marketing strip ' . (($setting->strip_enabled ?? false) ? 'on' : 'off'), null, route('strip.edit')];

        if (empty($setting?->email)) {
            $checks[] = ['warning', 'No site email in Settings', 'Contact form and newsletter emails have nowhere to go.', route('setting')];
        }

        if (Schema::hasTable('sponsors')) {
            $hidden = Sponsor::where('is_active', false)->count() + SponsorGroup::where('is_active', false)->count();
            if ($hidden) {
                $checks[] = ['info', $hidden . ' hidden sponsor logo(s) or group(s)', null, route('sponsors.index')];
            }
            $seo = PageSeo::count();
            $checks[] = [$seo ? 'good' : 'info', 'Custom SEO on ' . $seo . ' of ' . count(Seo::PAGES) . ' pages', null, route('seo.index')];
        }

        return $checks;
    }

    private function content(): array
    {
        $items = [
            ['Events', Service::wherePublished('1')->count(), 'fas fa-calendar-alt', route('schedules.index')],
            ['Speakers', Team::where('year', 'Speaker')->count(), 'fas fa-microphone', route('team.index')],
            ['Advisors', Team::where('year', 'Advisor')->count(), 'fas fa-user-tie', route('team.index')],
            ['Organizer team', Team::where('year', 'Organizer')->count(), 'fas fa-users', route('team.index')],
        ];
        if (Schema::hasTable('sponsors')) {
            $items[] = ['Sponsor logos', Sponsor::where('is_active', true)->count(), 'fas fa-handshake', route('sponsors.index')];
            $items[] = ['Legathon competitions', Competition::where('is_active', true)->count(), 'fas fa-trophy', route('competitions.index')];
        }
        $items[] = ['Gallery images', Gallery::count(), 'far fa-images', route('gallery.edit')];
        $items[] = ['Videos', Video::count(), 'fas fa-video', route('video.index')];
        $items[] = ['Testimonials', Testimonial::count(), 'fas fa-star', route('testimonial.index')];
        $items[] = ['Blog posts', Post::wherePublished('1')->count(), 'fas fa-book', route('post.index')];

        return $items;
    }

    private function logins(Carbon $now): array
    {
        $table = config('authentication-log.table_name', 'authentication_log');
        if (! Schema::hasTable($table)) {
            return ['recent' => collect(), 'failed7' => 0];
        }

        return [
            'recent' => DB::table($table)->whereNotNull('login_at')->orderByDesc('login_at')->take(6)
                ->get(['ip_address', 'login_at', 'login_successful']),
            'failed7' => DB::table($table)->where('login_successful', false)->where('login_at', '>=', $now->copy()->subDays(7))->count(),
        ];
    }
}
