<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;
use App\Models\Testimonial;
use App\Models\Post;
use App\Models\Category;
use App\Models\Tag;
use App\Models\Service;
use App\Models\Team;
use App\Models\Scategory;
use App\Models\Faq;
use App\Mail\ContactMail;
use App\Notifications\GrievenceNotification;
use App\Notifications\ServiceNotification;
use App\Notifications\JobAlertNotification;
use App\Notifications\NewsletterNotification;
use Illuminate\Support\Facades\Notification;

use Mail;
use View;

class FrontController extends Controller
{
    public function __construct()
    {
        $setting = Setting::first();

        $featured_products = Service::wherePublished('1')->where('featured', '1')->latest()->take(6)->get();
        $service_categories = Scategory::whereHas('services')->withCount('services')->get();
        $footer_posts = Post::latest()->take(2)->get();
        $services_title = Service::latest()->take(5)->get();
        //dd($service_categories->toArray());

        view::share('setting', $setting);
        view::share('featured_products', $featured_products);
        view::share('service_categories', $service_categories);
        view::share('footer_posts', $footer_posts);

        view::share('services_title', $services_title);
    }

    public function index()
    {
        $services = Service::wherePublished('1')->whereFeatured('1')->latest()->take(6)->get();

        $testimonials = Testimonial::all();
        $faqs = faq::all();
        $teams = Team::all();
        $posts = Post::wherePublished('1')->whereFeatured('1')->latest()->get();

        return view('frontend.index', compact('services', 'testimonials', 'posts', 'faqs', 'teams'));
    }

    public function about()
    {
        $testimonials = Testimonial::all();
        $teams = Team::all();

        return view('frontend.about', compact('testimonials', 'teams'));
    }

    public function service(Request $request)
    {
        $category = $request['category'] ?? '';

        $search = $request['search'] ?? '';

        $services = Service::wherePublished('1')->latest()->paginate(12);

        $categories = Scategory::all();

        if ($search) {
            $services = Service::wherePublished('1')->where('title', 'LIKE', '%' . $search . '%')->latest()->paginate(12);
        }

        if ($category) {
            $services = Service::wherePublished('1')->whereHas('scategories', function ($query) use ($category) {
                $query->where('slug', $category);
            })->latest()->paginate(12);
        }


        return view('frontend.service', compact('services', 'search', 'category', 'categories'));
    }




    public function serviceDetail(Request $request)
    {
        $service = Service::whereSlug($request->slug)->firstOrFail();

        $service->increment('views');

        $categories = Scategory::withCount('services')->having('services_count', '>', '0')->latest()->get();

        $recently = Service::wherePublished('1')->where('id', '!=', $service->id)->latest()->take(3)->get();

        return view('frontend.service-detail', compact('service', 'categories', 'recently'));
    }

    public function pricing()
    {
        return view('frontend.pricing');
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

        return redirect()->route('thanks')->with('message', 'We have received your message. One of our colleagues will get back in touch with you soon!
        Have a great day!');
    }

    public function thanks()
    {
        session(['thanksPageVisited' => true]);
        return view('frontend.thanks');
    }

    public function benefits()
    {
        return view('frontend.benefits');
    }

    public function subscription()
    {
        return view('frontend.subscription');
    }

    public function jobNotification(Request $request)
    {

        $data = $request->validate([
            'name' => 'required|string|max:50',
            'email'    => 'required|string|email|max:50',
            'phone' => 'required|string|max:20',
            'message'    => 'required|string|max:255',
        ]);

        $ip = \Request::ip();
        $data['ip'] = $ip;
        $data['url'] = url()->previous();


        $setting = Setting::first();

        Notification::route('mail',  $setting->email)->notify(new JobAlertNotification($data));

        return redirect()->route('thanks')->with('message', 'We have received your message. One of our colleagues will get back in touch with you soon!
        Have a great day!');
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
        return view('frontend.gallery');
    }
    public function grievence()
    {
        return view('frontend.grievence');
    }


    public function grievenceForm(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:75',
            'email' => 'required|string|email|max:75',
            'phone' => 'required|string|max:30',
            'employer' => 'required|string|max:100',
            'country' => 'required|string|max:50',
            'city' => 'required|string|max:50',
            'date' => 'required|date|before_or_equal:today|max:75',
            'subject' => 'required|string|max:75',
            'message' => 'required|string|max:255',
        ]);

        $setting = Setting::first();
        Notification::route('mail', $setting->email)->notify(new GrievenceNotification($data));

        return redirect()->route('thanks')->with('message', 'We have received your message. One of our colleagues will get back in touch with you soon!
        Have a great day!');
    }
}
