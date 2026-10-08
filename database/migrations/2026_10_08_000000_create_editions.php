<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Year-wise editions: speakers, sponsors, gallery images and videos belong to
 * a year, and the header shows a flyout of the years that have content.
 * Everything that already exists is tagged 2025.
 */
return new class extends Migration
{
    private const TAGGED = ['teams', 'sponsors', 'galleries', 'videos'];

    public function up(): void
    {
        if (! Schema::hasTable('editions')) {
            Schema::create('editions', function (Blueprint $table) {
                $table->id();
                $table->unsignedSmallInteger('year')->unique();
                $table->string('title')->nullable();
                $table->unsignedBigInteger('schedule_service_id')->nullable();
                $table->boolean('is_visible')->default(true);
                $table->timestamps();
            });
        }

        foreach (self::TAGGED as $table) {
            if (! Schema::hasColumn($table, 'edition_id')) {
                Schema::table($table, function (Blueprint $t) {
                    $t->unsignedBigInteger('edition_id')->nullable()->index();
                });
            }
        }

        if (! Schema::hasColumn('menu_items', 'source')) {
            Schema::table('menu_items', fn (Blueprint $t) => $t->string('source', 20)->nullable()->after('url'));
        }

        $this->seed();
    }

    private function seed(): void
    {
        $now = now();

        // one year per published event, plus 2025 for the existing content
        $events = DB::table('services')->where('published', '1')->whereNull('deleted_at')->whereNotNull('date')
            ->orderBy('date')->get(['id', 'title', 'date']);
        $years = [2025 => null];
        foreach ($events as $event) {
            $years[(int) substr($event->date, 0, 4)] = $event; // the latest event of a year wins
        }

        foreach ($years as $year => $event) {
            if (! DB::table('editions')->where('year', $year)->exists()) {
                DB::table('editions')->insert([
                    'year' => $year,
                    'title' => $event?->title,
                    'schedule_service_id' => $event?->id,
                    'is_visible' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }

        // 2025's schedule: an unpublished 2025 event if there is one (never one another year already uses)
        $edition2025 = DB::table('editions')->where('year', 2025)->first();
        if (! $edition2025->schedule_service_id) {
            $event2025 = DB::table('services')->whereNull('deleted_at')->whereYear('date', 2025)
                ->whereNotIn('id', DB::table('editions')->whereNotNull('schedule_service_id')->pluck('schedule_service_id'))
                ->orderByDesc('date')->first(['id', 'title']);
            if ($event2025) {
                DB::table('editions')->where('id', $edition2025->id)
                    ->update(['schedule_service_id' => $event2025->id, 'title' => $edition2025->title ?: $event2025->title]);
            }
        }

        foreach (self::TAGGED as $table) {
            DB::table($table)->whereNull('edition_id')->update(['edition_id' => $edition2025->id]);
        }

        $this->seedMenu();
    }

    /** Header tabs become year flyouts; Exhibitors folds into Sponsors. */
    private function seedMenu(): void
    {
        $header = fn () => DB::table('menu_items')->where('location', 'header')->whereNull('parent_id');
        $tabs = [
            'speakers' => ['Speakers', '/speakers'],
            'schedule' => ['Schedule', '/schedule'],
            'sponsors' => ['Sponsors', '/sponsors'],
            'gallery' => ['Gallery', '/gallery'],
        ];

        foreach ($tabs as $source => [$label, $url]) {
            $item = $header()->where('label', $label)->whereNull('source')->first();
            if ($item) {
                DB::table('menu_items')->where('id', $item->id)->update(['source' => $source, 'url' => $url]);
                // the year flyout replaces hand-made sub-links (kept, just hidden)
                DB::table('menu_items')->where('parent_id', $item->id)->update(['is_active' => false]);
            }
        }

        $header()->where('label', 'Exhibitors')->update(['is_active' => false]);

        DB::table('menu_items')->where('location', 'footer')->where('label', 'Schedule')->update(['url' => '/schedule']);
        DB::table('menu_items')->where('location', 'footer')->where('label', 'Sponsors')->update(['url' => '/sponsors']);
    }

    public function down(): void
    {
        if (Schema::hasColumn('menu_items', 'source')) {
            $parents = DB::table('menu_items')->whereNotNull('source')->pluck('id');
            DB::table('menu_items')->whereIn('parent_id', $parents)->update(['is_active' => true]);
            DB::table('menu_items')->where('location', 'header')->where('label', 'Exhibitors')->update(['is_active' => true]);
            DB::table('menu_items')->where('source', 'gallery')->update(['url' => '#']);
            DB::table('menu_items')->where('source', 'sponsors')->update(['url' => '/#sponsors']);
            DB::table('menu_items')->where('url', '/schedule')->update(['url' => '/event/india-law-ai-tech-summit-2025']);
            DB::table('menu_items')->where('location', 'footer')->where('url', '/sponsors')->update(['url' => '/#sponsors']);
            Schema::table('menu_items', fn (Blueprint $t) => $t->dropColumn('source'));
        }

        foreach (self::TAGGED as $table) {
            if (Schema::hasColumn($table, 'edition_id')) {
                Schema::table($table, function (Blueprint $t) use ($table) {
                    $t->dropIndex($table . '_edition_id_index');
                    $t->dropColumn('edition_id');
                });
            }
        }

        Schema::dropIfExists('editions');
    }
};
