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
       $upcomingEvents = Service::wherePublished('1')
                         ->where('date', '>', now())
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
        $speakers = Team::where('year','Speaker')->take(8)->get();
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

    public function speakers()
    {
        $speakers = Team::where('year','Speaker')->get();
        return view('frontend.speakers',compact('speakers'));
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

    public function gallery()
    {
        $images = Gallery::all();
        return view('frontend.gallery',compact('images'));
    }

    public function videos()
    {
        $videos = Video::all();
        return view('frontend.videos',compact('videos'));
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



    public function ticket()
    {
        return view('frontend.ticket');
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

        $data = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|string|email|max:100',
            'phone' => 'required|digits:10',
            'company' => 'required|string|max:100',
            'designation' => 'required|string|max:100',
            'event' => 'required|string|max:255',
        ]);

        // Use the event the visitor actually picked (and its real date) - the
        // hidden date field in the form is not reliable.
        $chosenEvent = Service::wherePublished('1')
            ->where('title', $data['event'])
            ->where('date', '>', now())
            ->orderBy('date')
            ->first();
        if (! $chosenEvent) {
            return back()->withInput()->withErrors(['event' => 'Please choose an event from the list.']);
        }
        $data['date'] = $chosenEvent->date->format('Y-m-d H:i:s');

        $paid = \App\Support\Pricing::isPaid($chosenEvent);
        $passType = null;
        $quantity = 1;
        $attendees = [];

        if ($paid) {
            $extra = $request->validate([
                'pass_type_id' => [
                    'required',
                    \Illuminate\Validation\Rule::exists('pass_types', 'id')
                        ->where('service_id', $chosenEvent->id)->where('is_active', 1),
                ],
                'quantity' => 'required|integer|min:1|max:' . \App\Support\Pricing::maxPasses(),
                'attendees' => 'array',
                'attendees.*.name' => 'nullable|string|max:100',
                'attendees.*.email' => 'nullable|string|email|max:100',
            ], [
                'pass_type_id.required' => 'Please choose a pass.',
                'pass_type_id.exists' => 'Please choose a pass from the list.',
                'quantity.max' => 'Please contact us for more than :max passes.',
            ]);

            $passType = \App\Models\PassType::find($extra['pass_type_id']);
            $quantity = (int) $extra['quantity'];

            // pass 1 is the buyer; passes 2+ need their own attendee
            for ($number = 2; $number <= $quantity; $number++) {
                $attendee = $extra['attendees'][$number] ?? [];
                $name = trim((string) ($attendee['name'] ?? ''));
                $email = trim((string) ($attendee['email'] ?? ''));
                if ($name === '' || $email === '') {
                    return back()->withInput()->withErrors([
                        "attendees.{$number}.name" => 'Enter the name and email for pass ' . $number . '.',
                    ]);
                }
                $attendees[$number] = ['name' => $name, 'email' => $email];
            }
        }

        // one email can hold only one pass for the same event
        $emails = [1 => strtolower($data['email'])];
        foreach ($attendees as $number => $attendee) {
            $emails[$number] = strtolower($attendee['email']);
        }
        if (count(array_unique($emails)) !== count($emails)) {
            return back()->withInput()->withErrors(['email' => 'Each pass needs a different email address.']);
        }
        $taken = Booking::where('service_id', $chosenEvent->id)
            ->whereIn(\Illuminate\Support\Facades\DB::raw('LOWER(email)'), $emails)
            ->pluck('email')->map(fn ($e) => strtolower($e))->all();
        foreach ($emails as $number => $email) {
            if (in_array($email, $taken, true)) {
                $field = $number === 1 ? 'email' : "attendees.{$number}.email";

                return back()->withInput()->withErrors([
                    $field => $email . ' is already registered for this event.',
                ]);
            }
        }

        $quote = $passType
            ? \App\Support\Pricing::quote($passType, $quantity, $chosenEvent)
            : null;

        $order = null;
        $bookings = [];

        \Illuminate\Support\Facades\DB::transaction(function () use (&$order, &$bookings, $data, $chosenEvent, $passType, $quantity, $attendees, $quote) {
            if ($quote) {
                $order = \App\Models\Order::create([
                    'order_no' => 'TMP',
                    'service_id' => $chosenEvent->id,
                    'pass_type_id' => $passType->id,
                    'event' => $chosenEvent->title,
                    'pass_name' => $passType->name,
                    'buyer_name' => $data['name'],
                    'buyer_email' => $data['email'],
                    'buyer_phone' => $data['phone'],
                    'buyer_company' => $data['company'],
                    'buyer_designation' => $data['designation'],
                    'quantity' => $quantity,
                    'unit_price' => $quote['unit'],
                    'discount_total' => $quote['discount'],
                    'discount_label' => $quote['discount_label'],
                    'tax_percent' => $quote['tax_percent'],
                    'tax_total' => $quote['tax'],
                    'total' => $quote['total'],
                    'status' => 'pending',
                ]);
                $order->update(['order_no' => 'OB' . now()->format('ymd') . '-' . str_pad((string) $order->id, 4, '0', STR_PAD_LEFT)]);
            }

            for ($number = 1; $number <= $quantity; $number++) {
                $person = $number === 1
                    ? ['name' => $data['name'], 'email' => $data['email']]
                    : $attendees[$number];

                $bookings[] = Booking::create([
                    'order_id' => $order?->id,
                    'booking_id' => \App\Support\BookingNumber::next(),
                    'attendee_no' => $number,
                    'name' => $person['name'],
                    'email' => $person['email'],
                    'phone' => $data['phone'],
                    'company' => $data['company'],
                    'designation' => $number === 1 ? $data['designation'] : null,
                    'amount' => $quote ? $quote['per_pass'] : null,
                    'date' => $data['date'],
                    'service_id' => $chosenEvent->id,
                    'event' => $chosenEvent->title,
                ]);
            }
        });

        $this->notifyBooking($data, $chosenEvent, $order, $bookings, $quote);
        $this->sendBookingWhatsApp($data);

        $request->session()->flash('eventName', $chosenEvent->title);
        if ($order) {
            $request->session()->flash('orderNo', $order->order_no);
            $request->session()->flash('orderTotal', \App\Support\Pricing::money((float) $order->total));
            $request->session()->flash('orderPasses', $order->quantity);
        }

        return redirect()->route('thanks');
    }

    /** Confirmation email to the buyer (and the admin copy). */
    private function notifyBooking(array $data, Service $event, $order, array $bookings, ?array $quote): void
    {
        $payload = $data + [
            'event' => $event->title,
            'event_date' => $event->date,
            'booking_id' => $bookings[0]->booking_id ?? null,
            'order' => $order,
            'quote' => $quote,
            'attendees' => collect($bookings)->map(fn ($b) => ['name' => $b->name, 'email' => $b->email, 'booking_id' => $b->booking_id])->all(),
            'payment_instructions' => Setting::find(1)->payment_instructions,
            'ip' => request()->ip(),
        ];

        try {
            event(new \App\Events\BookingCreated($payload));
        } catch (\Throwable $e) {
            report($e);
        }
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
