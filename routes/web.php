<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\InstructorAreaController;
use App\Http\Controllers\InstructorController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicCatalogController;
use App\Http\Controllers\StaffController;
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
