<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\FrontController;
use App\Http\Controllers\TestimonialController;
use App\Http\Controllers\ScategoryController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\SummerNoteController;
use App\Http\Controllers\VideoController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\VidhiSammanController;
use App\Http\Controllers\HeroBannerController;
use App\Http\Controllers\MarketingStripController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\TicketingController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\PageContentController;
use App\Http\Controllers\SponsorController;
use App\Http\Controllers\CompetitionController;
use App\Http\Controllers\SeoController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\ScheduleController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });


Auth::routes();


Route::group(['prefix' => 'admin', 'middleware' => ['auth']], function () {
    Route::get('/dashboard', [App\Http\Controllers\HomeController::class, 'index'])->name('dashboard');
    Route::get('settings', [SettingController::class, 'index'])->name('setting');
    Route::post('settings', [SettingController::class, 'update'])->name('setting.update');
    // homepage hero banner
    Route::get('hero-banner', [HeroBannerController::class, 'edit'])->name('hero.edit');
    Route::post('hero-banner', [HeroBannerController::class, 'update'])->name('hero.update');
    // scrolling marketing strip under the hero banner
    Route::get('marketing-strip', [MarketingStripController::class, 'edit'])->name('strip.edit');
    Route::post('marketing-strip', [MarketingStripController::class, 'update'])->name('strip.update');
    // open / close event registration
    Route::get('registration', [RegistrationController::class, 'edit'])->name('registration.edit');
    Route::post('registration', [RegistrationController::class, 'update'])->name('registration.update');

    // website content
    Route::get('page-content', [PageContentController::class, 'index'])->name('page-content.index');
    Route::get('page-content/{key}', [PageContentController::class, 'edit'])->name('page-content.edit');
    Route::post('page-content/{key}', [PageContentController::class, 'update'])->name('page-content.update');
    Route::post('page-content/{key}/reset', [PageContentController::class, 'reset'])->name('page-content.reset');
    Route::post('page-content/{key}/visibility', [PageContentController::class, 'visibility'])->name('page-content.visibility');

    Route::get('sponsors', [SponsorController::class, 'index'])->name('sponsors.index');
    Route::post('sponsors/groups', [SponsorController::class, 'storeGroup'])->name('sponsors.groups.store');
    Route::post('sponsors/groups/{group}', [SponsorController::class, 'updateGroup'])->name('sponsors.groups.update');
    Route::delete('sponsors/groups/{group}', [SponsorController::class, 'destroyGroup'])->name('sponsors.groups.destroy');
    Route::post('sponsors/reorder', [SponsorController::class, 'reorder'])->name('sponsors.reorder');
    Route::post('sponsors/logos', [SponsorController::class, 'storeSponsor'])->name('sponsors.logos.store');
    Route::post('sponsors/logos/{sponsor}', [SponsorController::class, 'updateSponsor'])->name('sponsors.logos.update');
    Route::delete('sponsors/logos/{sponsor}', [SponsorController::class, 'destroySponsor'])->name('sponsors.logos.destroy');

    Route::resource('competitions', CompetitionController::class)->except('show');

    Route::get('seo', [SeoController::class, 'index'])->name('seo.index');
    Route::post('seo', [SeoController::class, 'update'])->name('seo.update');

    Route::get('schedules', [ScheduleController::class, 'index'])->name('schedules.index');
    Route::get('schedules/{service}', [ScheduleController::class, 'edit'])->name('schedules.edit');
    Route::post('schedules/{service}', [ScheduleController::class, 'update'])->name('schedules.update');

    Route::get('menus', [MenuController::class, 'index'])->name('menus.index');
    Route::post('menus', [MenuController::class, 'store'])->name('menus.store');
    Route::post('menus/{item}', [MenuController::class, 'update'])->name('menus.update');
    Route::delete('menus/{item}', [MenuController::class, 'destroy'])->name('menus.destroy');
    Route::post('menus/{item}/toggle', [MenuController::class, 'toggle'])->name('menus.toggle');

    // ticket prices, bulk discounts and orders
    Route::get('ticketing', [TicketingController::class, 'index'])->name('ticketing.index');
    Route::post('ticketing/passes', [TicketingController::class, 'storePass'])->name('ticketing.pass.store');
    Route::post('ticketing/passes/{pass}', [TicketingController::class, 'updatePass'])->name('ticketing.pass.update');
    Route::delete('ticketing/passes/{pass}', [TicketingController::class, 'destroyPass'])->name('ticketing.pass.destroy');
    Route::post('ticketing/passes/{pass}/bundles', [TicketingController::class, 'updateBundles'])->name('ticketing.pass.bundles');
    Route::post('ticketing/tiers', [TicketingController::class, 'storeTier'])->name('ticketing.tier.store');
    Route::post('ticketing/tiers/{tier}', [TicketingController::class, 'updateTier'])->name('ticketing.tier.update');
    Route::delete('ticketing/tiers/{tier}', [TicketingController::class, 'destroyTier'])->name('ticketing.tier.destroy');
    Route::post('ticketing/settings', [TicketingController::class, 'updateSettings'])->name('ticketing.settings');

    Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('orders/export', [OrderController::class, 'export'])->name('orders.export');
    Route::get('orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::post('orders/{order}', [OrderController::class, 'update'])->name('orders.update');
    Route::delete('orders/{order}', [OrderController::class, 'destroy'])->name('orders.destroy');
    Route::post('orders-bulk-delete', [OrderController::class, 'bulkDestroy'])->name('orders.bulk-delete');
    Route::post('booking-bulk-delete', [\App\Http\Controllers\BookingController::class, 'bulkDestroy'])->name('booking.bulk-delete');
    // profile
    Route::get('profile', [ProfileController::class, 'profile'])->name('profile');
    //password update
    Route::post('password-update', [ProfileController::class, 'password_update'])->name('password.update');
    Route::post('/update-profile', [ProfileController::class, 'profile_update'])->name('profile.update');
    //posts
    Route::resource('post', PostController::class);

    Route::get('post-trash', [PostController::class, 'trashView'])->name('post.trash');
    // restore
    Route::get('post-restore/{id}', [PostController::class, 'restore'])->name('post.restore');
    //deleted permanently
    Route::delete('post-delete/{id}', [PostController::class, 'force_delete'])->name('post.force.delete');


    // Services
    Route::resource('service', ServiceController::class);
    // Route::get('service-trash', [ServiceController::class, 'trashView'])->name('service.trash');
    // Route::get('service-restore/{id}', [ServiceController::class, 'restore'])->name('service.restore');
    // //deleted permanently
    // Route::delete('service-delete/{id}', [ServiceController::class, 'force_delete'])->name('service.force.delete');

    // upload image via ckeditor
    Route::post('/upload', [PostController::class, 'ckeditor'])->name('ckeditor.upload');

    // Post Category
    Route::resource('category', CategoryController::class);

    // serviceCategorty
    Route::resource('scategory', ScategoryController::class);

    // Post Tags
    Route::resource('tag', TagController::class);

    //testimonials
    Route::resource('testimonial', TestimonialController::class);

    //gallery
    Route::get('gallery', [GalleryController::class, 'create'])->name('gallery.create');
    Route::post('gallery', [GalleryController::class, 'store'])->name('gallery.store');
    Route::get('gallery-edit', [GalleryController::class, 'edit'])->name('gallery.edit');
    Route::post('gallery-edit', [GalleryController::class, 'update'])->name('gallery.update');

    // faq
    Route::resource('faq', FaqController::class);

    Route::resource('team', TeamController::class);
    Route::resource('video', VideoController::class);
    Route::resource('booking', BookingController::class);
    // Route::resource('vidhi', VidhiSammanController::class);
    Route::get('vidhi', [VidhiSammanController::class,'index'])->name('vidhi.index');
    Route::post('vidhi', [VidhiSammanController::class,'store'])->name('vidhi.store');
    Route::get('vidhi/edit/{id}', [VidhiSammanController::class,'edit'])->name('vidhi.edit');
    Route::post('vidhi/edit/{id}', [VidhiSammanController::class,'update'])->name('vidhi.update');
    Route::delete('vidhi/destroy/{id}', [VidhiSammanController::class,'destroy'])->name('vidhi.destroy');


    //summernote image
    Route::post('summernote',[SummerNoteController::class,'summerUpload'])->name('summer.upload.image');
    Route::post('summernote/delete',[SummerNoteController::class,'summerDelete'])->name('summer.delete.image');
});






// Frontend

// Send Email
Route::post('contact-send', [FrontController::class, 'contactMail'])->name('contact.send');
// Route::post('service-booking', [FrontController::class, 'serviceBooking'])->name('service.booking'); // disabled: serviceBooking() does not exist
Route::post('newsletter', [FrontController::class, 'newsLetter'])->name('newsletter');
// disclaimer
// Route::get('disclaimer', [FrontController::class, 'disclaimer'])->name('disclaimer'); // disabled: view frontend.disclaimer does not exist
//return policy
// Route::get('return-policy',[FrontController::class,'returnPolicy'])->name('return.policy');
//privacy policy
Route::get('privacy-policy', [FrontController::class, 'privacyPolicy'])->name('privacy.policy');
// cookies
// Route::get('cookies', [FrontController::class, 'cookies'])->name('cookies'); // disabled: view frontend.cookies does not exist
// term & conditino
// Route::get('terms-conditions', [FrontController::class, 'termCondition'])->name('term.condition'); // disabled: view frontend.term-condition does not exist

// Route::get('/', [FrontController::class, 'ticket'])->name('home');
Route::get('/', [FrontController::class, 'index'])->name('home');
Route::get('/about', [FrontController::class, 'about'])->name('about');

// Route::get('/products/{category?}', [FrontController::class, 'service'])->name('service'); // disabled: service() does not exist
Route::get('/event/{slug}', [FrontController::class, 'serviceDetail'])->name('service.detail');
Route::get('/blog', [FrontController::class, 'blog'])->name('blog');

Route::get('/post/{slug}', [FrontController::class, 'blogDetail'])->name('blog.detail');

Route::get('/contact', [FrontController::class, 'contact'])->name('contact');
Route::get('/events/{date?}', [FrontController::class, 'events'])->name('events');


Route::get('/testimonials',[FrontController::class, 'testimonial'])->name('testimonial');
Route::get('/faq', [FrontController::class, 'faq'])->name('faq');
Route::get('/image-gallery', [FrontController::class, 'gallery'])->name('image.gallery');
Route::get('/videos', [FrontController::class, 'videos'])->name('videos');
// Route::get('/vidhi-samman', [FrontController::class, 'vidhiSamman'])->name('vidhi.samman');


Route::get('/thanks', [FrontController::class, 'thanks'])->name('thanks');

// online payment (Razorpay)
Route::get('/pay/{orderNo}', [PaymentController::class, 'pay'])->name('order.pay');
Route::post('/pay/{orderNo}/verify', [PaymentController::class, 'verify'])->name('order.verify');
Route::get('/pay/{orderNo}/cancelled', [PaymentController::class, 'cancelled'])->name('order.cancelled');
Route::get('/pay/{orderNo}/done', [PaymentController::class, 'done'])->name('order.done');
Route::post('/webhooks/razorpay', [PaymentController::class, 'webhook'])->name('razorpay.webhook')
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
Route::get('/thank-you', [FrontController::class, 'contactThanks'])->name('contact.thanks');
// Route::get('/benefits',[FrontController::class,'benefits'])->name('benefits');
// Route::get('/subscription',[FrontController::class,'subscription'])->name('subscription');
Route::get('/ticket',[FrontController::class,'ticket'])->name('ticket');
Route::get('/advisors',[FrontController::class,'advisors'])->name('advisors');
Route::get('/speakers',[FrontController::class, 'speakers'])->name('speakers');
Route::get('/speaker/{id}',[FrontController::class, 'speakerDetail'])->name('speaker.detail');
Route::get('/gallery', [FrontController::class, 'gallery'])->name('gallery');

// disabled: grievence(), grievenceForm() and jobNotification() do not exist in FrontController
// Route::get('/grievence', [FrontController::class, 'grievence'])->name('grievence');
// Route::post('/grievence', [FrontController::class, 'grievenceForm'])->name('grievence.form');
// Route::post('/job-notification', [FrontController::class, 'jobNotification'])->name('job.notification');

Route::post('book-ticket',[FrontController::class,'bookTicket'])->name('book.ticket');
Route::get('legathon',[FrontController::class,'legathan'])->name('legathan');



// Route::get('/generate-sitemap', function() {
//     $exitCode = Artisan::call('sitemap:generate');
//     return "sitemap generated";
// });
