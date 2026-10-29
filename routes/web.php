<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\CheckInController;
use App\Http\Controllers\DiscussionController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\StudentCourseController;
use App\Http\Controllers\Teach;
use App\Http\Controllers\Admin\NewController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\AboutController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\EventController;
use App\Http\Controllers\Admin\MessageController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\CourseController;
use App\Http\Controllers\Admin\TeacherController;
use App\Http\Controllers\Admin\CategoryController;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

Auth::routes();

Route::get('/',[SiteController::class, 'index'])->name('site.index');
Route::get('/verify/{code?}', [CertificateController::class, 'verify'])->name('certificate.verify');
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
Route::get('/schedule', [ScheduleController::class, 'index'])->name('schedule');
Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
Route::get('/notifications/{id}', [NotificationController::class, 'open'])->name('notifications.open');
Route::post('/notifications/read-all', [NotificationController::class, 'readAll'])->name('notifications.readAll');
Route::put('/notifications/settings', [NotificationController::class, 'settings'])->name('notifications.settings');
Route::get('/my/courses/{course}', [StudentCourseController::class, 'show'])->name('student.course');
Route::get('/my/courses/{course}/certificate', [CertificateController::class, 'show'])->name('certificate.show');
Route::post('/my/lessons/{lesson}/done', [StudentCourseController::class, 'toggleLesson'])->name('student.lesson.done');
Route::get('/my/lessons/{lesson}/file', [StudentCourseController::class, 'lessonFile'])->name('student.lesson.file');
Route::post('/my/assignments/{assignment}/submit', [StudentCourseController::class, 'submit'])->name('student.assignment.submit');
Route::get('/here', [CheckInController::class, 'show'])->name('attendance.here');
Route::post('/here', [CheckInController::class, 'store'])->name('attendance.checkin');
Route::get('/courses/{course}/discussion', [DiscussionController::class, 'index'])->name('discussion.index');
Route::post('/courses/{course}/discussion', [DiscussionController::class, 'store'])->name('discussion.store');
Route::post('/questions/{question}/replies', [DiscussionController::class, 'reply'])->name('discussion.reply');
Route::delete('/questions/{question}', [DiscussionController::class, 'destroy'])->name('discussion.destroy');
Route::delete('/replies/{reply}', [DiscussionController::class, 'destroyReply'])->name('discussion.reply.destroy');

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
    Route::post('/courses/{course}/qr', [Teach\QrController::class, 'open'])->name('qr.open');
    Route::get('/sessions/{session}/qr', [Teach\QrController::class, 'show'])->name('qr');
    Route::get('/sessions/{session}/qr/status', [Teach\QrController::class, 'status'])->name('qr.status');
    Route::post('/sessions/{session}/qr/close', [Teach\QrController::class, 'close'])->name('qr.close');
    Route::get('/courses/{course}/assignments', [Teach\AssignmentController::class, 'index'])->name('assignments');
    Route::post('/courses/{course}/assignments', [Teach\AssignmentController::class, 'store'])->name('assignments.store');
    Route::delete('/assignments/{assignment}', [Teach\AssignmentController::class, 'destroy'])->name('assignments.destroy');
    Route::put('/submissions/{submission}', [Teach\AssignmentController::class, 'grade'])->name('submissions.grade');
    Route::get('/submissions/{submission}/file', [Teach\AssignmentController::class, 'download'])->name('submissions.file');
});

});

Route::prefix(LaravelLocalization::setLocale())->group(function () {
    Route::prefix('admin')->name('admin.')->middleware('auth')->group(function () {
        Route::get('/', [AdminController::class, 'index'])->name('index');

        Route::resource('categories', CategoryController::class)->except('show');
        Route::resource('courses', CourseController::class)->except('show');
        Route::post('courses/assignTeacher', [CourseController::class, 'assignTeacher'])->name('courses.assignTeacher');
        Route::resource('teachers', TeacherController::class)->except('show');
        Route::resource('abouts', AboutController::class)->except('show');
        Route::resource('events', EventController::class)->except('show');
        Route::resource('news', NewController::class)->except('show');
        Route::resource('roles', RoleController::class)->except('show');

        Route::get('users', [UserController::class, 'index'])->name('users.index');
        Route::get('users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('users/store', [UserController::class, 'store'])->name('users.store');
        Route::put('users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('users/{id}', [UserController::class, 'destroy'])->name('users.destroy');

        Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('reports/print', [ReportController::class, 'print'])->name('reports.print');
        Route::get('reports/export', [ReportController::class, 'export'])->name('reports.export');

        Route::get('messages', [MessageController::class, 'index'])->name('messages.index');
        Route::delete('messages/{message}', [MessageController::class, 'destroy'])->name('messages.destroy');
    });
});

Route::view('not_allowed', 'not_allowed');
