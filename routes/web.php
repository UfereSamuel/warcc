<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\WeeklyTrackerController;
use App\Http\Controllers\MissionController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\ActivityCalendarController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\ActivityRequestController;

// Public Routes
Route::get('/', [PublicController::class, 'index'])->name('home');
Route::get('/about', [PublicController::class, 'about'])->name('public.about');
Route::get('/contact', [PublicController::class, 'contact'])->name('public.contact');
Route::get('/events', [PublicController::class, 'events'])->name('public.events');
Route::get('/events/{event}', [PublicController::class, 'eventShow'])->name('public.events.show');
Route::get('/api/events', [PublicController::class, 'eventsApi'])->name('public.events.api');
Route::get('/media', [PublicController::class, 'media'])->name('public.media');
Route::get('/videos', [PublicController::class, 'media'])->name('public.videos');

// Test Routes for Development (Remove in Production)
Route::get('/test-accounts', function () {
    return view('test-accounts');
})->name('test.accounts');

Route::get('/test-login/{staffId?}', function ($staffId = 'RCC-002') {
    $staff = \App\Models\Staff::where('staff_id', $staffId)->first();

    if (!$staff) {
        return redirect()->route('test.accounts')->with('error', 'Test staff not found. Please run: php artisan db:seed --class=TestStaffSeeder');
    }

    // Login the staff member
    \Illuminate\Support\Facades\Auth::guard('staff')->login($staff);

    // Ensure session is regenerated and saved
    request()->session()->regenerate();
    request()->session()->save();

    // Redirect admin users to admin dashboard, regular staff to staff dashboard
    if ($staff->is_admin) {
        return redirect()->route('admin.dashboard')->with('success', 'Logged in as Administrator: ' . $staff->full_name);
    } else {
        return redirect()->route('staff.dashboard')->with('success', 'Logged in as ' . $staff->full_name . ' for testing purposes.');
    }
})->name('test.login');

// Authentication Routes
Route::prefix('auth')->name('auth.')->group(function () {
    // Staff SSO Login
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::get('/microsoft', [AuthController::class, 'redirectToMicrosoft'])->name('microsoft');
    Route::get('/microsoft/callback', [AuthController::class, 'handleMicrosoftCallback'])->name('microsoft.callback');

    // Super Admin Login
    Route::get('/admin-login', [AuthController::class, 'showAdminLogin'])->name('admin.login');
    Route::post('/admin-login', [AuthController::class, 'adminLogin'])->name('admin.login.post');

    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

// Protected Routes (Requires Authentication)
Route::middleware(['auth:staff', 'profile.complete'])->prefix('staff')->name('staff.')->group(function () {

    // Staff Dashboard
    Route::get('/dashboard', [StaffController::class, 'dashboard'])->name('dashboard');
    Route::get('/profile', [StaffController::class, 'profile'])->name('profile');
    Route::put('/profile', [StaffController::class, 'updateProfile'])->name('profile.update');
    
    // Profile Completion (for new SSO users)
    Route::get('/profile/complete', [StaffController::class, 'showProfileCompletion'])->name('profile.complete');
    Route::post('/profile/complete', [StaffController::class, 'completeProfile'])->name('profile.complete.post');

    // Attendance Management
    Route::prefix('attendance')->name('attendance.')->group(function () {
        Route::get('/', [AttendanceController::class, 'index'])->name('index');
        Route::post('/clock-in', [AttendanceController::class, 'clockIn'])->name('clock-in');
        Route::post('/clock-out', [AttendanceController::class, 'clockOut'])->name('clock-out');
        Route::get('/history', [AttendanceController::class, 'history'])->name('history');
    });

    // Weekly Tracker
    Route::prefix('weekly-tracker')->name('tracker.')->group(function () {
        Route::get('/', [WeeklyTrackerController::class, 'index'])->name('index');
        Route::get('/create', [WeeklyTrackerController::class, 'create'])->name('create');
        Route::post('/', [WeeklyTrackerController::class, 'store'])->name('store');
        Route::get('/{tracker}/edit', [WeeklyTrackerController::class, 'edit'])->name('edit');
        Route::put('/{tracker}', [WeeklyTrackerController::class, 'update'])->name('update');
        Route::post('/{tracker}/submit', [WeeklyTrackerController::class, 'submit'])->name('submit');
        Route::post('/{tracker}/request-edit', [WeeklyTrackerController::class, 'requestEditApproval'])->name('request-edit');
        Route::get('/{tracker}/download/{type}/{index?}', [WeeklyTrackerController::class, 'downloadDocument'])->name('download');
    });

    // Mission Management
    Route::prefix('missions')->name('missions.')->group(function () {
        Route::get('/', [MissionController::class, 'index'])->name('index');
        Route::get('/create', [MissionController::class, 'create'])->name('create');
        Route::post('/', [MissionController::class, 'store'])->name('store');
        Route::get('/{mission}', [MissionController::class, 'show'])->name('show');
        Route::get('/{mission}/edit', [MissionController::class, 'edit'])->name('edit');
        Route::put('/{mission}', [MissionController::class, 'update'])->name('update');
        Route::delete('/{mission}', [MissionController::class, 'destroy'])->name('destroy');
    });

    // Leave Management
    Route::prefix('leaves')->name('leaves.')->group(function () {
        Route::get('/', [LeaveController::class, 'index'])->name('index');
        Route::get('/create', [LeaveController::class, 'create'])->name('create');
        Route::post('/', [LeaveController::class, 'store'])->name('store');
        Route::get('/{leave}', [LeaveController::class, 'show'])->name('show');
        Route::get('/{leave}/edit', [LeaveController::class, 'edit'])->name('edit');
        Route::put('/{leave}', [LeaveController::class, 'update'])->name('update');
        Route::delete('/{leave}', [LeaveController::class, 'destroy'])->name('destroy');
        Route::get('/balance/summary', [LeaveController::class, 'balanceSummary'])->name('balance');
    });

    // Activity Calendar (View Only for Staff)
    Route::prefix('calendar')->name('calendar.')->group(function () {
        Route::get('/', [ActivityCalendarController::class, 'index'])->name('index');
        Route::get('/api/events', [ActivityCalendarController::class, 'apiEvents'])->name('api.events');
    });

    // Activity Requests (Staff)
    Route::prefix('activity-requests')->name('activity-requests.')->group(function () {
        Route::get('/', [ActivityRequestController::class, 'index'])->name('index');
        Route::get('/create', [ActivityRequestController::class, 'create'])->name('create');
        Route::post('/', [ActivityRequestController::class, 'store'])->name('store');
        Route::get('/{activityRequest}', [ActivityRequestController::class, 'show'])->name('show');
        Route::get('/{activityRequest}/edit', [ActivityRequestController::class, 'edit'])->name('edit');
        Route::put('/{activityRequest}', [ActivityRequestController::class, 'update'])->name('update');
        Route::delete('/{activityRequest}', [ActivityRequestController::class, 'destroy'])->name('destroy');
    });
});

// Admin Routes (Requires Admin Privileges)
Route::middleware(['auth:staff', 'profile.complete', 'admin'])->prefix('admin')->name('admin.')->group(function () {

    // Admin Dashboard
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/reports', [AdminController::class, 'reports'])->name('reports');
    Route::get('/reports/export', [AdminController::class, 'exportReports'])->name('reports.export');

    // Export Routes
    Route::get('/export/attendance', [AdminController::class, 'exportAttendance'])->name('export.attendance');
    Route::get('/export/weekly-trackers', [AdminController::class, 'exportWeeklyTrackers'])->name('export.weekly-trackers');
    Route::get('/export/dashboard-analytics', [AdminController::class, 'exportDashboardAnalytics'])->name('export.dashboard-analytics');

    // System Settings
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [AdminController::class, 'settingsIndex'])->name('index');
        Route::put('/', [AdminController::class, 'settingsUpdate'])->name('update');
        Route::delete('/reset', [AdminController::class, 'settingsReset'])->name('reset');
    });

    // Email Testing
    Route::prefix('email')->name('email.')->group(function () {
        Route::get('/test', [AdminController::class, 'emailTestForm'])->name('test');
        Route::post('/test', [AdminController::class, 'sendTestEmail'])->name('test.send');
        Route::post('/configure', [AdminController::class, 'configureEmail'])->name('configure');
        Route::post('/test-graph', [AdminController::class, 'testMicrosoftGraph'])->name('test.graph');
    });

    // Admin Management
    Route::prefix('admins')->name('admins.')->group(function () {
        Route::get('/', [AdminController::class, 'adminIndex'])->name('index');
    });

    // Roles & Permissions Management
    Route::prefix('roles')->name('roles.')->group(function () {
        Route::get('/', [AdminController::class, 'rolesIndex'])->name('index');
        Route::get('/create', [AdminController::class, 'rolesCreate'])->name('create');
        Route::post('/', [AdminController::class, 'rolesStore'])->name('store');
        Route::get('/{role}/edit', [AdminController::class, 'rolesEdit'])->name('edit');
        Route::put('/{role}', [AdminController::class, 'rolesUpdate'])->name('update');
        Route::delete('/{role}', [AdminController::class, 'rolesDestroy'])->name('destroy');
    });

    // Staff Management
    Route::prefix('staff')->name('staff.')->group(function () {
        Route::get('/', [AdminController::class, 'staffIndex'])->name('index');
        Route::get('/create', [AdminController::class, 'staffCreate'])->name('create');
        Route::post('/', [AdminController::class, 'staffStore'])->name('store');
        Route::get('/{staff}', [AdminController::class, 'staffShow'])->name('show');
        Route::get('/{staff}/edit', [AdminController::class, 'staffEdit'])->name('edit');
        Route::put('/{staff}', [AdminController::class, 'staffUpdate'])->name('update');
        Route::delete('/{staff}', [AdminController::class, 'staffDestroy'])->name('destroy');
        Route::put('/{staff}/promote', [AdminController::class, 'promoteStaff'])->name('promote');
        Route::put('/{staff}/demote', [AdminController::class, 'demoteStaff'])->name('demote');
        Route::put('/{staff}/leave-balance', [AdminController::class, 'updateLeaveBalance'])->name('leave-balance');
    });

    // Reports & Analytics Routes
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportsController::class, 'index'])->name('index');
        Route::get('/staff-performance', [ReportsController::class, 'staffPerformance'])->name('staff-performance');
        Route::get('/weekly-trackers', [ReportsController::class, 'weeklyTrackers'])->name('weekly-trackers');
        Route::get('/attendance', [ReportsController::class, 'attendance'])->name('attendance');
        Route::get('/export-pdf', [ReportsController::class, 'exportPDF'])->name('export-pdf');
    });

    // Attendance Management
    Route::prefix('attendance')->name('attendance.')->group(function () {
        Route::get('/', [AdminController::class, 'attendanceIndex'])->name('index');
        Route::get('/daily-report', [AdminController::class, 'dailyReport'])->name('daily-report');
        Route::get('/export', [AdminController::class, 'exportAttendance'])->name('export');
    });

    // Weekly Tracker Management
    Route::prefix('weekly-trackers')->name('weekly-trackers.')->group(function () {
        Route::get('/', [AdminController::class, 'weeklyTrackersIndex'])->name('index');
        Route::get('/{tracker}', [AdminController::class, 'weeklyTrackerShow'])->name('show');
        Route::put('/{tracker}/status', [AdminController::class, 'weeklyTrackerUpdateStatus'])->name('update-status');
        Route::post('/{tracker}/approve-edit', [AdminController::class, 'weeklyTrackerApproveEdit'])->name('approve-edit');
        Route::post('/{tracker}/reject-edit', [AdminController::class, 'weeklyTrackerRejectEdit'])->name('reject-edit');
    });

    // Mission Management
    Route::prefix('missions')->name('missions.')->group(function () {
        Route::get('/', [AdminController::class, 'missionsIndex'])->name('index');
        Route::get('/{mission}/approve', [AdminController::class, 'approveMission'])->name('approve');
        Route::get('/{mission}/reject', [AdminController::class, 'rejectMission'])->name('reject');
    });

    // Leave Management
    Route::prefix('leaves')->name('leaves.')->group(function () {
        Route::get('/', [AdminController::class, 'leavesIndex'])->name('index');
        Route::get('/{leave}/approve', [AdminController::class, 'approveLeave'])->name('approve');
        Route::get('/{leave}/reject', [AdminController::class, 'rejectLeave'])->name('reject');
    });

    // Leave Types Management
    Route::prefix('leave-types')->name('leave-types.')->group(function () {
        Route::get('/', [AdminController::class, 'leaveTypesIndex'])->name('index');
        Route::get('/create', [AdminController::class, 'leaveTypesCreate'])->name('create');
        Route::post('/', [AdminController::class, 'leaveTypesStore'])->name('store');
        Route::get('/{leaveType}/edit', [AdminController::class, 'leaveTypesEdit'])->name('edit');
        Route::put('/{leaveType}', [AdminController::class, 'leaveTypesUpdate'])->name('update');
        Route::delete('/{leaveType}', [AdminController::class, 'leaveTypesDestroy'])->name('destroy');
    });

    // Positions Management
    Route::prefix('positions')->name('positions.')->group(function () {
        Route::get('/', [AdminController::class, 'positionsIndex'])->name('index');
        Route::get('/create', [AdminController::class, 'positionsCreate'])->name('create');
        Route::post('/', [AdminController::class, 'positionsStore'])->name('store');
        Route::get('/{position}/edit', [AdminController::class, 'positionsEdit'])->name('edit');
        Route::put('/{position}', [AdminController::class, 'positionsUpdate'])->name('update');
        Route::delete('/{position}', [AdminController::class, 'positionsDestroy'])->name('destroy');
        Route::post('/{position}/toggle-status', [AdminController::class, 'positionsToggleStatus'])->name('toggle-status');
    });

    // Activity Calendar Management
    Route::prefix('calendar')->name('calendar.')->group(function () {
        Route::get('/', [AdminController::class, 'calendarIndex'])->name('index');
        Route::get('/create', [AdminController::class, 'calendarCreate'])->name('create');
        Route::post('/', [AdminController::class, 'calendarStore'])->name('store');
        Route::get('/api/events', [AdminController::class, 'calendarEvents'])->name('api.events');
        Route::get('/{activity}/edit', [AdminController::class, 'calendarEdit'])->name('edit');
        Route::put('/{activity}', [AdminController::class, 'calendarUpdate'])->name('update');
        Route::delete('/{activity}', [AdminController::class, 'calendarDestroy'])->name('destroy');
    });

    // Activity Request Management
    Route::prefix('activity-requests')->name('activity-requests.')->group(function () {
        Route::get('/', [AdminController::class, 'activityRequestsIndex'])->name('index');
        Route::get('/{activityRequest}', [AdminController::class, 'activityRequestShow'])->name('show');
        Route::post('/{activityRequest}/approve', [AdminController::class, 'approveActivityRequest'])->name('approve');
        Route::post('/{activityRequest}/reject', [AdminController::class, 'rejectActivityRequest'])->name('reject');
        Route::post('/batch-process', [AdminController::class, 'batchProcessActivityRequests'])->name('batch-process');
    });

    // Public Events Management
    Route::prefix('public-events')->name('public-events.')->group(function () {
        Route::get('/', [AdminController::class, 'publicEventsIndex'])->name('index');
        Route::get('/create', [AdminController::class, 'publicEventsCreate'])->name('create');
        Route::post('/', [AdminController::class, 'publicEventsStore'])->name('store');
        Route::get('/{publicEvent}', [AdminController::class, 'publicEventsShow'])->name('show');
        Route::get('/{publicEvent}/edit', [AdminController::class, 'publicEventsEdit'])->name('edit');
        Route::put('/{publicEvent}', [AdminController::class, 'publicEventsUpdate'])->name('update');
        Route::delete('/{publicEvent}', [AdminController::class, 'publicEventsDestroy'])->name('destroy');
        Route::post('/{publicEvent}/toggle-featured', [AdminController::class, 'publicEventsToggleFeatured'])->name('toggle-featured');
        Route::post('/{publicEvent}/toggle-status', [AdminController::class, 'publicEventsToggleStatus'])->name('toggle-status');
        Route::post('/bulk-action', [AdminController::class, 'publicEventsBulkAction'])->name('bulk-action');
    });

    // Website Content Management
    Route::prefix('content')->name('content.')->group(function () {
        Route::get('/', [AdminController::class, 'contentIndex'])->name('index');
        Route::get('/homepage', [AdminController::class, 'homepageEdit'])->name('homepage');
        Route::put('/homepage', [AdminController::class, 'homepageUpdate'])->name('homepage.update');
        Route::get('/about', [AdminController::class, 'aboutEdit'])->name('about');
        Route::put('/about', [AdminController::class, 'aboutUpdate'])->name('about.update');

        // Hero Slides Management
        Route::prefix('hero-slides')->name('hero-slides.')->group(function () {
            Route::get('/', [AdminController::class, 'heroSlidesIndex'])->name('index');
            Route::get('/create', [AdminController::class, 'heroSlidesCreate'])->name('create');
            Route::post('/', [AdminController::class, 'heroSlidesStore'])->name('store');
            Route::get('/{heroSlide}/edit', [AdminController::class, 'heroSlidesEdit'])->name('edit');
            Route::put('/{heroSlide}', [AdminController::class, 'heroSlidesUpdate'])->name('update');
            Route::delete('/{heroSlide}', [AdminController::class, 'heroSlidesDestroy'])->name('destroy');
            Route::post('/{heroSlide}/toggle-status', [AdminController::class, 'heroSlidesToggleStatus'])->name('toggle-status');
            Route::post('/reorder', [AdminController::class, 'heroSlidesReorder'])->name('reorder');
        });
    });
});

// File Download Routes
Route::middleware(['auth:staff'])->prefix('downloads')->name('downloads.')->group(function () {
    Route::get('/documents/{document}', [StaffController::class, 'downloadDocument'])->name('document');
});
