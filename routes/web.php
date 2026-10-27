<?php

use Illuminate\Support\Facades\Auth;
use App\Events\Category1Notification;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\StudentCourseController;
use App\Http\Controllers\Teach;
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
Route::get('/courses/{id}',[SiteController::class, 'course'])->whereNumber('id')->name('site.course');
Route::post('/courses/{courseId}/enroll', [CourseController::class, 'enroll'])->name('courses.enroll');
Route::delete('/courses/{course}/remove-enrollment', [CourseController::class, 'removeEnrollment'])->name('courses.removeEnrollment');
Route::get('/teachers',[SiteController::class, 'teachers'])->name('site.teachers');
Route::get('/my', [StudentController::class, 'dashboard'])->name('student.dashboard');
Route::get('/my/profile', [StudentController::class, 'profile'])->name('student.profile');
Route::put('/my/profile', [StudentController::class, 'updateProfile'])->name('student.profile.update');
Route::put('/my/password', [StudentController::class, 'updatePassword'])->name('student.password.update');
Route::get('/event',[SiteController::class, 'event'])->name('site.event');
Route::get('/contact', [SiteController::class, 'contact'])->name('contact');
Route::post('/contact', [SiteController::class, 'contact_data'])->name('contact_data');
Route::get('/my/courses/{course}', [StudentCourseController::class, 'show'])->name('student.course');
Route::post('/my/lessons/{lesson}/done', [StudentCourseController::class, 'toggleLesson'])->name('student.lesson.done');
Route::get('/my/lessons/{lesson}/file', [StudentCourseController::class, 'lessonFile'])->name('student.lesson.file');
Route::post('/my/assignments/{assignment}/submit', [StudentCourseController::class, 'submit'])->name('student.assignment.submit');

Route::prefix('teach')->name('teach.')->middleware('teacher')->group(function () {
    Route::get('/', [Teach\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/courses/{course}', [Teach\CourseController::class, 'show'])->name('course');
    Route::post('/courses/{course}/units', [Teach\CourseController::class, 'storeUnit'])->name('units.store');
    Route::delete('/units/{unit}', [Teach\CourseController::class, 'destroyUnit'])->name('units.destroy');
    Route::post('/courses/{course}/lessons', [Teach\CourseController::class, 'storeLesson'])->name('lessons.store');
    Route::patch('/lessons/{lesson}/publish', [Teach\CourseController::class, 'togglePublish'])->name('lessons.publish');
    Route::delete('/lessons/{lesson}', [Teach\CourseController::class, 'destroyLesson'])->name('lessons.destroy');
    Route::post('/courses/{course}/slots', [Teach\CourseController::class, 'storeSlot'])->name('slots.store');
    Route::delete('/slots/{slot}', [Teach\CourseController::class, 'destroySlot'])->name('slots.destroy');
    Route::get('/courses/{course}/attendance', [Teach\AttendanceController::class, 'show'])->name('attendance');
    Route::post('/courses/{course}/attendance', [Teach\AttendanceController::class, 'store'])->name('attendance.store');
    Route::get('/courses/{course}/assignments', [Teach\AssignmentController::class, 'index'])->name('assignments');
    Route::post('/courses/{course}/assignments', [Teach\AssignmentController::class, 'store'])->name('assignments.store');
    Route::delete('/assignments/{assignment}', [Teach\AssignmentController::class, 'destroy'])->name('assignments.destroy');
    Route::put('/submissions/{submission}', [Teach\AssignmentController::class, 'grade'])->name('submissions.grade');
    Route::get('/submissions/{submission}/file', [Teach\AssignmentController::class, 'download'])->name('submissions.file');
});

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
