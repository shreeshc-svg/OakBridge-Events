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

        $request->merge(['buyer_gstin' => strtoupper(trim((string) $request->input('buyer_gstin'))) ?: null]);

        $data = $request->validate(array_merge([
            'name' => 'required|string|max:100',
            'email' => 'required|string|email|max:100',
            'phone' => 'required|digits:10',
            'company' => 'nullable|string|max:100',
            'designation' => 'nullable|string|max:100',
            'event' => 'required|string|max:255',
        ], InvoiceController::billingRules()), InvoiceController::billingMessages());

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
            // a paid order gets a GST invoice, which needs a billing address
            if (! $request->input('billing_state') && ($fromGstin = \App\Support\GstStates::stateFromGstin($data['buyer_gstin'] ?? null))) {
                $request->merge(['billing_state' => $fromGstin]);
                $data['billing_state'] = $fromGstin;
            }

            $extra = $request->validate([
                'billing_address' => 'required|string|max:500',
                'billing_state' => ['required', \Illuminate\Validation\Rule::in(\App\Support\GstStates::names())],
                'billing_pin' => 'required|digits:6',
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
                'billing_address.required' => 'Please enter your billing address – it goes on your GST invoice.',
                'billing_state.required' => 'Please choose your state.',
                'billing_pin.required' => 'Please enter your PIN code.',
                'billing_pin.digits' => 'The PIN code is 6 digits.',
                'pass_type_id.required' => 'Please choose a pass.',
                'pass_type_id.exists' => 'Please choose a pass from the list.',
                'quantity.max' => 'Please contact us for more than :max passes.',
            ]);

            $gstinState = \App\Support\GstStates::stateFromGstin($data['buyer_gstin'] ?? null);
            if ($gstinState && $gstinState !== $extra['billing_state']) {
                return back()->withInput()->withErrors([
                    'billing_state' => 'Your GSTIN is registered in ' . $gstinState . ' – choose that state, or check the GSTIN.',
                ]);
            }
            $data['billing_address'] = $extra['billing_address'];
            $data['billing_state'] = $extra['billing_state'];
            $data['billing_pin'] = $extra['billing_pin'];

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
        // already registered = holds a pass, or sits in an order that is paid or still awaiting payment
        $taken = Booking::where('service_id', $chosenEvent->id)
            ->pluck('email')->filter()->map(fn ($e) => strtolower($e))->all();
        $heldInOrders = \App\Models\Order::where('service_id', $chosenEvent->id)
            ->whereIn('status', ['pending', 'paid'])
            ->pluck('attendees')
            ->flatMap(fn ($list) => collect((array) $list)->pluck('email'))
            ->filter()->map(fn ($e) => strtolower($e))->all();
        $taken = array_unique(array_merge($taken, $heldInOrders));
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

        // Free event: register straight away, exactly as before.
        if (! $quote) {
            $booking = Booking::create([
                'booking_id' => \App\Support\BookingNumber::next(),
                'attendee_no' => 1,
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'company' => ($data['company'] ?? null) ?: null,
                'designation' => ($data['designation'] ?? null) ?: null,
                'date' => $data['date'],
                'service_id' => $chosenEvent->id,
                'event' => $chosenEvent->title,
            ]);

            $this->notifyFreeBooking($data, $chosenEvent, $booking);
            $request->session()->flash('eventName', $chosenEvent->title);

            return redirect()->route('thanks');
        }

        // Paid event: hold the attendees on the order; they become passes once it is paid.
        $attendeeList = [1 => ['name' => $data['name'], 'email' => $data['email']]];
        foreach ($attendees as $number => $attendee) {
            $attendeeList[$number] = $attendee;
        }

        $order = \App\Models\Order::create([
            'order_no' => \App\Models\Order::newOrderNumber(),
            'service_id' => $chosenEvent->id,
            'pass_type_id' => $passType->id,
            'event' => $chosenEvent->title,
            'pass_name' => $passType->name,
            'buyer_name' => $data['name'],
            'buyer_email' => $data['email'],
            'buyer_phone' => $data['phone'],
            'buyer_company' => ($data['company'] ?? null) ?: null,
            'buyer_designation' => ($data['designation'] ?? null) ?: null,
            'buyer_gstin' => $data['buyer_gstin'] ?? null,
            'billing_address' => trim((string) ($data['billing_address'] ?? '')) ?: null,
            'billing_state' => ($data['billing_state'] ?? null) ?: \App\Support\GstStates::stateFromGstin($data['buyer_gstin'] ?? null),
            'billing_pin' => ($data['billing_pin'] ?? null) ?: null,
            'quantity' => $quantity,
            'attendees' => $attendeeList,
            'unit_price' => $quote['unit'],
            'discount_total' => $quote['discount'],
            'discount_label' => $quote['discount_label'],
            'tax_percent' => $quote['tax_percent'],
            'tax_total' => $quote['tax'],
            'total' => $quote['total'],
            'status' => 'pending',
        ]);

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
