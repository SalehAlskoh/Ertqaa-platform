<?php

use App\Http\Controllers\ApplicationReviewController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\GroupStudentsController;
use App\Http\Controllers\InstructorAreaController;
use App\Http\Controllers\InstructorController;
use App\Http\Controllers\InternalNotificationController;
use App\Http\Controllers\LectureController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicCatalogController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\StudentApplicationController;
use App\Http\Controllers\StudentEnrollmentController;
use App\Http\Controllers\TrainingGroupController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => to_route('courses.index'))->name('home');
Route::get('/courses', [PublicCatalogController::class, 'courses'])->name('courses.index');
Route::get('/courses/{course:slug}', [PublicCatalogController::class, 'course'])->name('courses.show');
Route::get('/groups', [PublicCatalogController::class, 'groups'])->name('groups.index');
Route::get('/groups/{trainingGroup}', [PublicCatalogController::class, 'group'])->name('groups.show');
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/login', [AuthController::class, 'authenticate'])->middleware('throttle:20,1')->name('login.store');
    Route::get('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/register', [AuthController::class, 'storeRegistration'])->middleware('throttle:10,1')->name('register.store');
});
Route::middleware(['auth', 'active'])->group(function () {
    Route::get('lectures/{lecture}', [LectureController::class, 'show'])->name('lectures.show');
    Route::get('notifications', [InternalNotificationController::class, 'index'])->name('notifications.index');
    Route::patch('notifications/{notificationRecipient}/read', [InternalNotificationController::class, 'read'])->name('notifications.read');
    Route::prefix('admin')->name('admin.')->middleware('can:manage-catalog')->group(function () {
        Route::get('lectures', [LectureController::class, 'index'])->name('lectures.index');
        Route::get('lectures/generate', [LectureController::class, 'generate'])->name('lectures.generate');
        Route::post('lectures/generate', [LectureController::class, 'generateStore'])->name('lectures.generate.store');
        Route::get('lectures/create', [LectureController::class, 'create'])->name('lectures.create');
        Route::post('lectures', [LectureController::class, 'store'])->name('lectures.store');
        Route::get('lectures/{lecture}/edit', [LectureController::class, 'edit'])->name('lectures.edit');
        Route::put('lectures/{lecture}', [LectureController::class, 'update'])->name('lectures.update');
        Route::patch('lectures/{lecture}/cancel', [LectureController::class, 'cancel'])->name('lectures.cancel');
        Route::patch('lectures/{lecture}/complete', [LectureController::class, 'complete'])->name('lectures.complete');
        Route::get('applications', [ApplicationReviewController::class, 'index'])->name('applications.index');
        Route::get('applications/{application}', [ApplicationReviewController::class, 'show'])->name('applications.show');
        Route::patch('applications/{application}/approve', [ApplicationReviewController::class, 'approve'])->name('applications.approve');
        Route::patch('applications/{application}/reject', [ApplicationReviewController::class, 'reject'])->name('applications.reject');
        Route::get('applications/{application}/confirm', [ApplicationReviewController::class, 'confirmation'])->name('applications.confirm');
        Route::post('applications/{application}/confirm', [ApplicationReviewController::class, 'confirm'])->name('applications.confirm.store');
        Route::get('training-groups/{trainingGroup}/students', [GroupStudentsController::class, 'index'])->name('training-groups.students');
    });
    Route::prefix('student')->name('student.')->middleware('can:study')->group(function () {
        Route::post('groups/{trainingGroup}/applications', [StudentApplicationController::class, 'store'])->name('applications.store');
        Route::get('applications', [StudentApplicationController::class, 'index'])->name('applications.index');
        Route::get('applications/{application}', [StudentApplicationController::class, 'show'])->name('applications.show');
        Route::patch('applications/{application}/cancel', [StudentApplicationController::class, 'cancel'])->name('applications.cancel');
        Route::get('enrollments', [StudentEnrollmentController::class, 'index'])->name('enrollments.index');
        Route::get('enrollments/{enrollment}', [StudentEnrollmentController::class, 'show'])->name('enrollments.show');
        Route::patch('enrollments/{enrollment}/withdraw', [StudentEnrollmentController::class, 'withdraw'])->name('enrollments.withdraw');
        Route::get('lectures', [LectureController::class, 'index'])->name('lectures.index');
    });
    Route::prefix('instructor')->name('instructor.')->middleware('can:teach')->group(function () {
        Route::get('lectures', [LectureController::class, 'index'])->name('lectures.index');
        Route::get('groups/{trainingGroup}/students', [GroupStudentsController::class, 'index'])->name('groups.students');
    });
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'password'])->middleware('throttle:6,1')->name('profile.password');
    Route::prefix('admin')->name('admin.')->middleware('can:manage-catalog')->group(function () {
        Route::resource('course-categories', CategoryController::class)->except('show');
        Route::patch('courses/{course}/publication', [CourseController::class, 'publication'])->name('courses.publication');
        Route::resource('courses', CourseController::class);
        Route::resource('training-groups', TrainingGroupController::class)->except('destroy');
    });
    Route::prefix('admin')->name('admin.')->middleware('can:manage-accounts')->group(function () {
        Route::patch('instructors/{instructor}/status', [InstructorController::class, 'status'])->name('instructors.status');
        Route::resource('instructors', InstructorController::class)->except('destroy');
        Route::patch('staff/{staffMember}/status', [StaffController::class, 'status'])->name('staff.status');
        Route::resource('staff', StaffController::class)->except(['show', 'destroy'])->parameters(['staff' => 'staffMember']);
    });
    Route::prefix('instructor')->name('instructor.')->middleware('can:teach')->group(function () {
        Route::get('profile', [InstructorAreaController::class, 'profile'])->name('profile');
        Route::get('groups', [InstructorAreaController::class, 'index'])->name('groups.index');
        Route::get('groups/{trainingGroup}', [InstructorAreaController::class, 'show'])->name('groups.show');
    });
});
