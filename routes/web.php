<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\ClassController;
use App\Http\Controllers\Admin\EnrollmentController;
use App\Http\Controllers\Admin\InvoicesController;
use App\Http\Controllers\Admin\MaterialsController; // Add this import
use App\Http\Controllers\Admin\LogsController; // Add this import
use App\Http\Controllers\StudentController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Middleware\RoleMiddleware;
use App\Http\Middleware\RedirectIfAuthenticated;
use App\Http\Controllers\GuestController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application.
|
*/

// Public Routes (accessible to everyone)
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Guest Routes (only for NON-authenticated users)
Route::middleware(['guest'])->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])
         ->name('login');
    
    Route::post('/login', [LoginController::class, 'login']);
    
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])
         ->name('register');
    
    Route::post('/register', [RegisterController::class, 'register']);
});

// Routes for authenticated users only
Route::middleware('auth')->group(function () {
    // Logout Route
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    
    // Dashboard Redirect
    Route::get('/dashboard', function () {
        if (Auth::user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        } elseif (Auth::user()->role === 'student') {
            return redirect()->route('student.dashboard');
        } elseif (Auth::user()->role === 'guest') {
            return redirect()->route('guest.dashboard');
        }
        abort(403);
    })->name('dashboard');
    
    // Profile Route
    Route::get('/profile', function () {
        return view('profile.show');
    })->name('profile.view');
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
Route::prefix('admin')
    ->middleware(['auth', 'role:admin'])
    ->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
        
        // Classes Management - Updated to use ClassController
        Route::get('/classes', [ClassController::class, 'index'])->name('admin.classes');
        Route::post('/classes', [ClassController::class, 'store'])->name('admin.classes.store');
        Route::put('/classes/{id}', [ClassController::class, 'update'])->name('admin.classes.update');
        Route::delete('/classes/{id}', [ClassController::class, 'destroy'])->name('admin.classes.destroy');
        Route::patch('/classes/{id}/status', [ClassController::class, 'changeStatus'])->name('admin.classes.status');
        
        // Enrollment Management - Added new routes
        Route::get('/enrolled-students', [EnrollmentController::class, 'index'])->name('admin.enrollments.index');
        Route::post('/enrollments', [EnrollmentController::class, 'store'])->name('admin.enrollments.store');
        Route::put('/enrollments/{id}', [EnrollmentController::class, 'update'])->name('admin.enrollments.update');
        Route::delete('/enrollments/{id}', [EnrollmentController::class, 'destroy'])->name('admin.enrollments.destroy');
        Route::patch('/enrollments/{id}/status', [EnrollmentController::class, 'changeStatus'])->name('admin.enrollments.status');
        Route::get('/enrollments/{id}/edit', [EnrollmentController::class, 'edit'])->name('admin.enrollments.edit'); // <-- Add this line
        Route::get('/enrollments/{id}', [EnrollmentController::class, 'show'])->name('admin.enrollments.show'); // <-- Add this line
        
        // Invoices Management - Updated to use InvoicesController
        Route::get('/invoices', [InvoicesController::class, 'index'])->name('admin.invoices.index');
        Route::post('/invoices', [InvoicesController::class, 'store'])->name('admin.invoices.store');
        Route::put('/invoices/{id}', [InvoicesController::class, 'update'])->name('admin.invoices.update');
        Route::delete('/invoices/{id}', [InvoicesController::class, 'destroy'])->name('admin.invoices.destroy');
        Route::patch('/invoices/{id}/paid', [InvoicesController::class, 'markAsPaid'])->name('admin.invoices.paid');
        Route::get('/invoices/{id}/edit', [InvoicesController::class, 'edit'])->name('admin.invoices.edit');
        
        // Materials Management - Updated to use MaterialsController
        Route::get('/materials', [MaterialsController::class, 'index'])->name('admin.materials');
        Route::post('/materials', [MaterialsController::class, 'store'])->name('admin.materials.store');
        Route::get('/materials/{id}/edit', [MaterialsController::class, 'edit'])->name('admin.materials.edit'); // Add this line
        Route::put('/materials/{id}', [MaterialsController::class, 'update'])->name('admin.materials.update');
        Route::delete('/materials/{id}', [MaterialsController::class, 'destroy'])->name('admin.materials.destroy');
        Route::patch('/materials/{id}/active', [MaterialsController::class, 'toggleActive'])->name('admin.materials.toggle-active');
        Route::post('/materials/order', [MaterialsController::class, 'updateOrder'])->name('admin.materials.order');
        Route::get('/materials/{id}/preview', [MaterialsController::class, 'preview'])->name('admin.materials.preview');
        
        // Students Management
        Route::get('/students', [AdminController::class, 'students'])
            ->name('admin.students');
        
        // Profile
        Route::get('/profile', [AdminController::class, 'showProfile'])->name('admin.profile');
        Route::get('/profile/edit', [AdminController::class, 'editProfile'])->name('admin.profile.edit');
        Route::post('/profile/update', [AdminController::class, 'updateProfile'])->name('admin.profile.update');
        
        // Logs
        Route::get('/logs', [LogsController::class, 'index'])->name('admin.logs');
    });

/*
|--------------------------------------------------------------------------
| Guest Routes (for users with role 'guest')
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:guest'])->group(function () {
    Route::get('/guest', [GuestController::class, 'dashboard'])->name('guest.dashboard');
    Route::get('/guest/join-class', [GuestController::class, 'showJoinClassForm'])->name('guest.join-class');
    Route::post('/guest/join-class', [GuestController::class, 'submitJoinClass'])->name('guest.join-class.submit');
});

/*
|--------------------------------------------------------------------------
| Student Routes
|--------------------------------------------------------------------------
*/
Route::prefix('student')
    ->middleware(['auth', 'role:student'])
    ->name('student.') // Add this line to prefix all route names
    ->group(function () {
        // Changed from a closure to the controller method
        Route::get('/dashboard', [StudentController::class, 'dashboard'])->name('dashboard');
        
        // Class Enrollment
        Route::get('/join-class', [StudentController::class, 'showJoinClassForm'])->name('join-class');
        Route::post('/join-class', [StudentController::class, 'submitJoinClass'])->name('join-class.submit');
        
        // Classes
        Route::get('/classes', [StudentController::class, 'myClasses'])
            ->name('classes');
        Route::get('/classes/{id}', [StudentController::class, 'viewClass'])
            ->name('classes.view');
        
        // Materials
        Route::get('/classes/{class_id}/materials/{material_id}', [StudentController::class, 'viewMaterial'])
            ->name('classes.materials.view');
        
        // Progress Tracking
        Route::post('/classes/{class_id}/progress/{module_id}', [StudentController::class, 'markProgress'])
            ->name('classes.progress.mark');
        Route::delete('/classes/{class_id}/progress/{module_id}', [StudentController::class, 'unmarkProgress'])
            ->name('classes.progress.unmark');
        
        // Resources
        Route::get('/resources', [StudentController::class, 'resources'])
            ->name('resources');
        
        // Contact Admin
        Route::get('/contact-admin', [StudentController::class, 'contactAdmin'])
            ->name('contact-admin');

        // Add this route if you want a modules page
        Route::get('/classes/{class_id}/modules', [StudentController::class, 'modules'])->name('modules');
        Route::get('/modules', [StudentController::class, 'allModules'])->name('modules.all');

        // Profile  
        Route::get('/profile', [StudentController::class, 'profile'])->name('profile');
        Route::post('/profile', [StudentController::class, 'updateProfile'])->name('profile.update');

        // Preview Material
        Route::get('/modules/{material}/preview', [StudentController::class, 'materialPreview']);

    });

/*
|--------------------------------------------------------------------------
| Test Route
|--------------------------------------------------------------------------
*/
Route::get('/test-middleware', function () {
    return "This route should only be visible if you're logged in.";
})->middleware('auth');

Route::get('/test-upload', function() {
    return view('test-upload');
});

Route::middleware(['auth', 'role:student,guest'])->group(function () {
    // routes accessible by student or guest
});

Route::middleware(['auth', 'role:student'])->group(function () {
    Route::get('/student/dashboard', [StudentController::class, 'dashboard'])->name('student.dashboard');
});





