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
    Route::get('service-trash', [ServiceController::class, 'trashView'])->name('service.trash');
    Route::get('service-restore/{id}', [ServiceController::class, 'restore'])->name('service.restore');
    //deleted permanently
    Route::delete('service-delete/{id}', [ServiceController::class, 'force_delete'])->name('service.force.delete');

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
});






// Frontend

// Send Email
Route::post('contact-send', [FrontController::class, 'contactMail'])->name('contact.send');
Route::post('service-booking', [FrontController::class, 'serviceBooking'])->name('service.booking');
Route::post('newsletter', [FrontController::class, 'newsLetter'])->name('newsletter');
// disclaimer
Route::get('disclaimer', [FrontController::class, 'disclaimer'])->name('disclaimer');
//return policy
// Route::get('return-policy',[FrontController::class,'returnPolicy'])->name('return.policy');
//privacy policy
Route::get('privacy-policy', [FrontController::class, 'privacyPolicy'])->name('privacy.policy');
// cookies
Route::get('cookies', [FrontController::class, 'cookies'])->name('cookies');
// term & conditino
Route::get('terms-conditions', [FrontController::class, 'termCondition'])->name('term.condition');

Route::get('/', [FrontController::class, 'index'])->name('home');
Route::get('/about', [FrontController::class, 'about'])->name('about');

Route::get('/Jobs/{category?}', [FrontController::class, 'service'])->name('service');
Route::get('/job/{slug}', [FrontController::class, 'serviceDetail'])->name('service.detail');
Route::get('/blog', [FrontController::class, 'blog'])->name('blog');

Route::get('/post/{slug}', [FrontController::class, 'blogDetail'])->name('blog.detail');

Route::get('/contact', [FrontController::class, 'contact'])->name('contact');

// Route::get('/testimonial',[FrontController::class, 'testimonial'])->name('testimonial');
Route::get('/faq', [FrontController::class, 'faq'])->name('faq');


Route::get('/thanks', [FrontController::class, 'thanks'])->name('thanks');
// Route::get('/benefits',[FrontController::class,'benefits'])->name('benefits');
// Route::get('/subscription',[FrontController::class,'subscription'])->name('subscription');
// Route::get('/subscription',[FrontController::class,'subscription'])->name('subscription');
// Route::get('/pricing',[FrontController::class, 'pricing'])->name('pricing');
Route::get('/gallery', [FrontController::class, 'gallery'])->name('gallery');

Route::get('/grievence', [FrontController::class, 'grievence'])->name('grievence');
Route::post('/grievence', [FrontController::class, 'grievenceForm'])->name('grievence.form');
Route::post('/job-notification', [FrontController::class, 'jobNotification'])->name('job.notification');



// Route::get('/generate-sitemap', function() {
//     $exitCode = Artisan::call('sitemap:generate');
//     return "sitemap generated";
// });
