<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;
use App\Models\Setting;
use App\Models\Testimonial;
use App\Models\Post;
use App\Models\Category;
use App\Models\Tag;
use App\Models\Service;
use App\Models\Gallery;
use App\Models\Team;
use App\Models\Scategory;
use App\Models\Video;
use App\Models\Booking;
use App\Models\Faq;
use App\Mail\ContactMail;
use App\Notifications\GrievenceNotification;
use App\Notifications\ServiceNotification;
use App\Notifications\JobAlertNotification;
use App\Notifications\NewsletterNotification;
use Illuminate\Support\Facades\Notification;
use App\Models\VidhiSamman;

use App\Events\BookingCreated;

use Illuminate\Support\Facades\Http;


use Mail;
use View;

class FrontController extends Controller
{
    public function __construct()
    {
        // $setting = Setting::where('id', 1)->first();

        $featured_products = Service::wherePublished('1')->where('featured', '1')->latest()->take(6)->get();
        $service_categories = Scategory::whereHas('services')->withCount('services')->get();
        $footer_posts = Post::latest()->take(2)->get();
        $services_title = Service::latest()->take(5)->get();
        //dd($service_categories->toArray());

        // upcoming events
       // upcoming and ongoing: an event stays on the form until its day is over
       $upcomingEvents = Service::wherePublished('1')
                         ->whereDate('date', '>=', today())
                         ->orderBy('date')
                         ->get();

        // view::share('setting', $setting);
        view::share('featured_products', $featured_products);
        view::share('service_categories', $service_categories);
        view::share('footer_posts', $footer_posts);

        view::share('services_title', $services_title);

        view::share('upcomingEvents', $upcomingEvents);

        //dd($upcomingEvents);
    }

    public function index()
    {


        $services = Service::wherePublished('1')->latest()->paginate(12);
        // the newest year that has speakers (Admin > Years)
        $speakerYear = \App\Support\Editions::latest('speakers');
        $speakers = Team::where('year', 'Speaker')
            ->when($speakerYear, fn ($q) => $q->where('edition_id', $speakerYear->id))
            ->take(8)->get();
        $testimonials = Testimonial::all();
        $teams = Team::all();

        return view('frontend.index', compact('services', 'testimonials', 'speakers', 'teams'));
    }

        public function legathan()
    {
        return view('frontend.legathan');
    }


    public function about()
    {
        $testimonials = Testimonial::all();
        $teams = Team::all();

        return view('frontend.about', compact('testimonials', 'teams'));
    }

    public function events(Request $request, $date = null)
    {
        // Base query for all services
        $services = Service::wherePublished('1');

        // Apply date filter if the date is provided
        if ($date) {
            $services->whereDate('date', $date);
        }

        // Paginate the results
        $services = $services->latest()->paginate(12);

        return view('frontend.events', compact('services', 'date'));
    }




    public function serviceDetail(Request $request)
    {
        $service = Service::whereSlug($request->slug)->firstOrFail();

        $service->increment('views');

        $categories = Scategory::withCount('services')->having('services_count', '>', '0')->latest()->get();

        $recently = Service::wherePublished('1')->where('id', '!=', $service->id)->latest()->take(3)->get();

        return view('frontend.service-detail', compact('service', 'categories', 'recently'));
    }

    public function speakers($year = null)
    {
        $edition = $this->editionOr404('speakers', $year);
        $speakers = Team::where('year', 'Speaker')
            ->when($edition, fn ($q) => $q->where('edition_id', $edition->id))
            ->get();

        return view('frontend.speakers', compact('speakers', 'edition'));
    }

    /** Sponsors and exhibitors of one year. */
    public function sponsors($year = null)
    {
        $edition = $this->editionOr404('sponsors', $year);

        return view('frontend.sponsors', compact('edition'));
    }

    /** A year's schedule is its event page. */
    public function schedule($year = null)
    {
        $edition = \App\Support\Editions::pick('schedule', $year);
        $service = $edition?->scheduleService;

        if (! $service) {
            abort_if($year !== null, 404);

            return redirect()->route('events');
        }

        return redirect()->route('service.detail', $service->slug);
    }

    /** The year asked for (404 if it has nothing for this section), or the newest one. */
    private function editionOr404(string $section, $year): ?\App\Models\Edition
    {
        $edition = \App\Support\Editions::pick($section, $year);
        abort_if($year !== null && ! $edition, 404);

        return $edition;
    }

    public function speakerDetail(Request $request)
    {
        $speaker = Team::whereId($request->id)->firstOrFail();
        return view('frontend.speaker-detail',compact('speaker'));
    }

    public function blog(Request $request)
    {
        $search = $request['search'] ?? '';
        $category = $request['category'] ?? '';
        $tag = $request['tag'] ?? '';

        $posts = Post::wherePublished('1')->latest()->paginate(9);


        if ($search) {
            $posts = Post::wherePublished('1')->where('title', 'LIKE', '%' . $search . '%')->latest()->paginate(12);
        }

        if ($category) {
            $posts = Post::wherePublished('1')->whereHas('categories', function ($query) use ($category) {
                $query->where('slug', $category);
            })->latest()->paginate(12);
        }

        if ($tag) {
            $posts = Post::wherePublished('1')->whereHas('tags', function ($query) use ($tag) {
                $query->where('slug', $tag);
            })->latest()->get();
        }
        $categories = Category::withCount('posts')
            ->whereHas('posts', function ($query) {
                $query->where('Published', '1'); // Assuming you have a status field for posts
            })
            ->latest()
            ->get();


        $tags = Tag::withCount('posts')->having('posts_count', '>', '0')->latest()->get();

        $recently = Post::wherePublished('1')->latest()->take(4)->get();

        return view('frontend.blog', compact('posts', 'categories', 'tags', 'recently', 'search', 'category', 'tag'));
    }

    public function blogDetail(Request $request)
    {
        $post = Post::whereSlug($request->slug)->with('tags')->firstOrFail();
        $post->increment('views');
        $search = $request['search'] ?? '';
        //dd($post->toArray());

        if ($search) {
            $posts = Post::wherePublished('1')->where('title', 'LIKE', '%' . $search . '%')->latest()->paginate(12);
        }

        $categories = Category::withCount('posts')->latest()->get();

        $recently = Post::wherePublished('1')->where('id', '!=', $post)->latest()->take(6)->get();

        $tags = Tag::withCount('posts')->having('posts_count', '>', '0')->latest()->get();

        return view('frontend.blog-detail', compact('post', 'categories', 'recently', 'tags', 'search'));
    }

    public function testimonial()
    {
        $testimonials = Testimonial::latest()->get();
        //dd($testimonials->toArray());

        return view('frontend.testimonial', compact('testimonials'));
    }

    public function contact()
    {
        return view('frontend.contact');
    }

    public function faq()
    {
        $faqs = Faq::all();

        return view('frontend.faq', compact('faqs'));
    }

    public function contactMail(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:75',
            'email' => 'required|email:rfc,dns|max:75',
            'phone' => 'required|string|max:20',
            'message' => 'nullable|string|max:300',
        ]);

        $ip = \Request::ip();
        $details = $request->only('name', 'email', 'phone', 'message');
        $details['ip'] = $ip;
        $details['url'] = url()->previous();

        $contactMail = new ContactMail($details);

        Mail::to(Setting::findOrFail(1)->email)->send($contactMail);

        return redirect()->route('contact.thanks')->with('message', 'We have received your message. One of our colleagues will get back in touch with you soon!
        Have a great day!');
    }


    public function contactThanks()
    {
        return view('frontend.contact-thanks');
    }

    public function thanks()
    {
        session(['thanksPageVisited' => true]);
        return view('frontend.thanks');
    }







    public function newsLetter(Request $request)
    {
        $data = $request->validate([
            'newsletter_email' => 'required|email:rcf,dns|max:50'
        ]);
        $setting = Setting::first();

        Notification::route('mail', $setting->email)->notify(new NewsletterNotification($data));
        return redirect()->route('thanks')->with('message', "Thanks for subscribing for our newsletters!");
    }

    public function disclaimer()
    {
        return view('frontend.disclaimer');
    }

    // public function returnPolicy()
    // {
    //     return view('frontend.return-policy');
    // }

    public function privacyPolicy()
    {
        return view('frontend.privacy');
    }

    public function cookies()
    {
        return view('frontend.cookies');
    }

    public function termCondition()
    {
        return view('frontend.term-condition');
    }

    public function gallery($year = null)
    {
        $edition = $this->editionOr404('images', $year);
        $images = Gallery::when($edition, fn ($q) => $q->where('edition_id', $edition->id))->orderBy('id')->get();

        return view('frontend.gallery', compact('images', 'edition'));
    }

    public function videos($year = null)
    {
        $edition = $this->editionOr404('videos', $year);
        $videos = Video::when($edition, fn ($q) => $q->where('edition_id', $edition->id))->orderBy('id')->get();

        return view('frontend.videos', compact('videos', 'edition'));
    }

    public function vidhiSamman()
    {
        $sarvoch2024 = VidhiSamman::where('year', '2024')->where('category', 'Sarvoch Vidhi Samman')->get();
        $vishist2024 = VidhiSamman::where('year', '2024')->where('category', 'Vishist Vidhi Samman')->get();
        $vidhi2024 = VidhiSamman::where('year', '2024')->where('category', 'Vidhi Samman')->get();

        $sarvoch2025 = VidhiSamman::where('year', '2025')->where('category', 'Sarvoch Vidhi Samman')->get();
        $vishist2025 = VidhiSamman::where('year', '2025')->where('category', 'Vishist Vidhi Samman')->get();
        $vidhi2025 = VidhiSamman::where('year', '2025')->where('category', 'Vidhi Samman')->get();

        return view('frontend.vidhi-samman', compact(
            'sarvoch2024',
            'vishist2024',
            'vidhi2024',
            'sarvoch2025',
            'vishist2025',
            'vidhi2025'
        ));
    }



    /** The registration form on its own page, for direct links from emails, ads and social posts. */
    public function registerPage(Request $request)
    {
        $page = \App\Support\PageContent::get('register');

        // ?event=<slug> (or the exact title) pre-selects that event
        $preselect = null;
        if ($request->filled('event')) {
            $wanted = (string) $request->input('event');
            $preselect = Service::wherePublished('1')
                ->where(fn ($q) => $q->where('slug', $wanted)->orWhere('title', $wanted))
                ->value('title');
        }

        return view('frontend.register', compact('page', 'preselect'));
    }

    public function advisors()
    {
        $advisors = Team::where('year','Advisor')->get();
        return view('frontend.advisors',compact('advisors'));
    }
    
    
    public function bookTicket(Request $request)
    {
        // Admin > Registration can close bookings
        $registration = \App\Http\Controllers\RegistrationController::viewData(Setting::find(1));
        if (! $registration['registrationOpen']) {
            return back()->withErrors(['registration_closed' => $registration['registrationClosedMessage']]);
        }

        // same checks and order creation as Admin > Orders > New registration
        $built = \App\Support\OrderBuilder::fromRequest($request);
        $chosenEvent = $built['event'];
        $data = $built['data'];

        // Free event: registered straight away, exactly as before.
        if (isset($built['booking'])) {
            $this->notifyFreeBooking($data, $chosenEvent, $built['booking']);
            $request->session()->flash('eventName', $chosenEvent->title);

            return redirect()->route('thanks');
        }

        $order = $built['order'];

        // Online payment on: straight to checkout. Otherwise the bank details email.
        if (\App\Support\Razorpay::enabled()) {
            return redirect()->route('order.pay', $order->order_no);
        }

        \App\Support\OrderFulfiller::notify($order, true);
        $request->session()->flash('eventName', $chosenEvent->title);
        $request->session()->flash('orderNo', $order->order_no);
        $request->session()->flash('orderTotal', \App\Support\Pricing::money((float) $order->total));
        $request->session()->flash('orderPasses', $order->quantity);

        return redirect()->route('thanks');
    }

    /** Free registration: the old confirmation email. */
    private function notifyFreeBooking(array $data, Service $event, $booking): void
    {
        $payload = $data + [
            'event' => $event->title,
            'event_date' => $event->date,
            'booking_id' => $booking->booking_id,
            'order' => null,
            'quote' => null,
            'attendees' => [['name' => $booking->name, 'email' => $booking->email, 'booking_id' => $booking->booking_id]],
            'payment_instructions' => null,
            'ip' => request()->ip(),
        ];

        try {
            event(new \App\Events\BookingCreated($payload));
        } catch (\Throwable $e) {
            report($e);
        }

        $this->sendBookingWhatsApp($data);
    }

    /** WhatsApp confirmation through Interakt; never blocks the registration. */
    private function sendBookingWhatsApp(array $data): void
    {
        $token = config('services.interakt.token');
        if (! $token) {
            return;
        }

        try {
            Http::withHeaders([
                'Authorization' => 'Basic ' . $token,
                'Content-Type' => 'application/json',
            ])->timeout(10)->post('https://api.interakt.ai/v1/public/message/', [
                'countryCode' => '+91',
                'phoneNumber' => $data['phone'],
                'callbackData' => 'registration',
                'type' => 'Template',
                'template' => [
                    'name' => config('services.interakt.template', 'new_reg_tba_events'),
                    'languageCode' => 'en',
                    'bodyValues' => [$data['name']],
                ],
            ]);
        } catch (\Throwable $e) {
            report($e);
        }
    }
}
