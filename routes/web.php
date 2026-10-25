<?php

use Illuminate\Support\Facades\Auth;
use App\Events\Category1Notification;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\Admin\NewController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\AboutController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\EventController;
use App\Http\Controllers\Admin\CourseController;
use App\Http\Controllers\Admin\TeacherController;
use App\Http\Controllers\Admin\CategoryController;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/









//


Auth::routes();

Route::get('/',[SiteController::class, 'index'])->name('site.index');
Route::get('/lang/{locale}', function ($locale) {
    abort_unless(in_array($locale, ['ar', 'en']), 404);
    session(['locale' => $locale]);
    return back();
})->name('lang.switch');
Route::group(['middleware' => ['auth']], function(){
 Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
 Route::get('/about',[SiteController::class, 'about'])->name('site.about');
Route::get('/courses',[SiteController::class, 'courses'])->name('site.courses');
Route::post('/courses/{courseId}/enroll', [CourseController::class, 'enroll'])->name('courses.enroll');
Route::delete('/courses/{course}/remove-enrollment', [CourseController::class, 'removeEnrollment'])->name('courses.removeEnrollment');
Route::get('/teachers',[SiteController::class, 'teachers'])->name('site.teachers');
Route::get('/event',[SiteController::class, 'event'])->name('site.event');
Route::get('/contact', [SiteController::class, 'contact'])->name('contact');
Route::post('/contact', [SiteController::class, 'contact_data'])->name('contact_data');

});


Route::prefix(LaravelLocalization::setLocale())->group(function(){
    Route::prefix('admin')->name('admin.')->middleware('auth')->group(function() {
        Route::get('/', [AdminController::class, 'index'])->name('index');
        Route::resource('categories', CategoryController::class)->middleware('ability:all_categories');
        Route::resource('courses', CourseController::class);//->middleware('ability:all_courses');
        Route::resource('teachers',  TeacherController::class)->middleware('ability:all_teachers');
      Route::post('courses/assignTeacher', [CourseController::class, 'assignTeacher'])->name('courses.assignTeacher');

        Route::resource('abouts',  AboutController::class)->middleware('ability:all_abouts');
        Route::resource('events', EventController::class)->middleware('ability:all_events');
        Route::resource('news',  NewController::class)->middleware('ability:all_newss');
        Route::get('users', [UserController::class, 'index'])->name('users.index')->middleware('ability:all_users');
        Route::get('users/create', [UserController::class, 'create'])->name('users.create')->middleware('ability:add_user');
        Route::post('users/store', [UserController::class, 'store'])->name('users.store')->middleware('ability:add_user');
        Route::put('users/{user}', [UserController::class, 'update'])->name('users.update')->middleware('ability:edit_user');
        Route::delete('users/{id}', [UserController::class, 'destroy'])->name('users.destroy')->middleware('ability:delete_user');

        Route::resource('roles', RoleController::class)->middleware('ability:all_users');

    });

    });


    Route::view('not_allowed', 'not_allowed');




   //Route::get('/home',function(){

   // if(Auth::check()){
   //    if(Auth::user()->type =='admin'){
       //    return view('admin.index');

      //  }else{

      //      return redirect()->route('site.index');
      // }
   // }
//});


Route::get('/send',function(){
    event(new Category1Notification('bouthaina'));
});
