<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\LeaveBalanceController;
use App\Http\Controllers\Admin\AttendanceLogController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\EmployeeDashboardController;
use App\Http\Controllers\HodDashboardController;
use App\Http\Controllers\MsDashboardController;
use App\Http\Controllers\MsAdhocController;
use App\Http\Controllers\HodAdhocController;
use App\Http\Controllers\AdminLoginController;
use App\Http\Controllers\AdminDashboardController;
use Illuminate\Support\Facades\Auth;
// Admin Leave Balance Management & Attendance Logs
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('leave-balances', [LeaveBalanceController::class, 'index'])->name('leave_balances.index');
    Route::post('leave-balances/set', [LeaveBalanceController::class, 'setBalance'])->name('leave_balances.set');
    Route::post('leave-balances/adjust', [LeaveBalanceController::class, 'adjustBalance'])->name('leave_balances.adjust');
    Route::post('leave-balances/reset', [LeaveBalanceController::class, 'resetYear'])->name('leave_balances.reset');
    Route::get('attendance-logs', [AttendanceLogController::class, 'index'])->name('attendance_logs.index');
    Route::get('attendance-logs/export', [AttendanceLogController::class, 'export'])->name('attendance_logs.export');
    // Leave Types management (Add / Edit / Delete / Toggle)
    Route::get('leave-types', [App\Http\Controllers\Admin\LeaveTypeController::class, 'index'])->name('leave_types.index');
    Route::get('leave-types/create', [App\Http\Controllers\Admin\LeaveTypeController::class, 'create'])->name('leave_types.create');
    Route::post('leave-types', [App\Http\Controllers\Admin\LeaveTypeController::class, 'store'])->name('leave_types.store');
    Route::get('leave-types/{id}/edit', [App\Http\Controllers\Admin\LeaveTypeController::class, 'edit'])->name('leave_types.edit');
    Route::put('leave-types/{id}', [App\Http\Controllers\Admin\LeaveTypeController::class, 'update'])->name('leave_types.update');
    Route::delete('leave-types/{id}', [App\Http\Controllers\Admin\LeaveTypeController::class, 'destroy'])->name('leave_types.destroy');
    Route::post('leave-types/{id}/toggle', [App\Http\Controllers\Admin\LeaveTypeController::class, 'toggleStatus'])->name('leave_types.toggle');
    Route::get('leave-records', [App\Http\Controllers\Admin\LeaveRecordsController::class, 'index'])->name('leave_records.index');
    Route::get('leave-records/export', [App\Http\Controllers\Admin\LeaveRecordsController::class, 'export'])->name('leave_records.export');
});
// Admin Department & HoD Management
Route::get('/admin/departments-hods', [App\Http\Controllers\Admin\DepartmentManagementController::class, 'index'])->name('admin.departments_hods.index');
Route::get('/admin/departments/edit/{id}', [App\Http\Controllers\Admin\DepartmentEditController::class, 'edit'])->name('admin.departments.edit');
Route::post('/admin/departments/edit/{id}', [App\Http\Controllers\Admin\DepartmentEditController::class, 'update'])->name('admin.departments.edit.update');
// Admin Department Create
Route::get('/admin/departments/create', [App\Http\Controllers\Admin\DepartmentCreateController::class, 'create'])->name('admin.departments.create');
Route::post('/admin/departments/create', [App\Http\Controllers\Admin\DepartmentCreateController::class, 'store'])->name('admin.departments.create.store');
// Admin Department Assign HoD
Route::get('/admin/departments/assign-hod/{id}', [App\Http\Controllers\Admin\DepartmentAssignHodController::class, 'assign'])->name('admin.departments.assign_hod');
Route::post('/admin/departments/assign-hod/{id}', [App\Http\Controllers\Admin\DepartmentAssignHodController::class, 'store'])->name('admin.departments.assign_hod.store');
// Admin Department Toggle Status
Route::get('/admin/departments/toggle-status/{id}', [App\Http\Controllers\Admin\DepartmentToggleStatusController::class, 'toggle'])->name('admin.departments.toggle-status');


Route::get('/', function () {
    return Auth::check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [EmployeeDashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/tour', [EmployeeDashboardController::class, 'tour'])->name('dashboard.tour');
    Route::post('/dashboard/tour', [EmployeeDashboardController::class, 'submitTour'])->name('dashboard.tour.store');
    Route::post('/dashboard/attendance', [EmployeeDashboardController::class, 'attendance'])->name('dashboard.attendance');
    Route::get('/dashboard/attendance-summary', [EmployeeDashboardController::class, 'attendanceSummary'])->name('dashboard.attendance_summary');
    Route::get('/dashboard/adhoc-requests', [EmployeeDashboardController::class, 'adhocRequests'])->name('dashboard.adhoc_requests');
    Route::get('/dashboard/leave', [EmployeeDashboardController::class, 'leaveForm'])->name('dashboard.leave_form');
    Route::post('/dashboard/adhoc-requests', [EmployeeDashboardController::class, 'submitAdhocRequest'])->name('dashboard.adhoc_requests.store');
    Route::post('/dashboard/leave', [EmployeeDashboardController::class, 'submitLeave'])->name('dashboard.leave');
    Route::post('/dashboard/profile-picture', [EmployeeDashboardController::class, 'uploadProfilePicture'])->name('dashboard.profile.picture');
    Route::get('/hod-dashboard', [HodDashboardController::class, 'index'])->name('hod.dashboard');
    Route::post('/hod-dashboard/action', [HodDashboardController::class, 'processAction'])->name('hod.dashboard.action');
    Route::get('/hod-dashboard/staff', [HodDashboardController::class, 'staffList'])->name('hod.staff_list');
    Route::get('/hod/pending', [HodDashboardController::class, 'pending'])->name('hod.pending');
    Route::get('/hod/recent', [HodDashboardController::class, 'recent'])->name('hod.recent');
    Route::get('/hod/on-tour', [HodDashboardController::class, 'onTour'])->name('hod.on_tour');
    Route::post('/hod-dashboard/unit', [HodDashboardController::class, 'updateUnit'])->name('hod.staff_unit.update');
    Route::get('/ms-dashboard', [MsDashboardController::class, 'index'])->name('ms.dashboard');
    Route::post('/ms-dashboard/action', [MsDashboardController::class, 'processAction'])->name('ms.dashboard.action');
    Route::get('/ms-dashboard/staff', [MsDashboardController::class, 'staffList'])->name('ms.staff_list');
    Route::get('/ms-dashboard/pending', [MsDashboardController::class, 'pending'])->name('ms.pending');
    Route::get('/ms-dashboard/on-tour', [MsDashboardController::class, 'onTourList'])->name('ms.on_tour');
    Route::post('/ms-dashboard/on-tour', [MsDashboardController::class, 'storeTour'])->name('ms.on_tour.store');
    Route::get('/ms-dashboard/on-tour/edit/{id}', [MsDashboardController::class, 'editTour'])->name('ms.on_tour.edit');
    Route::post('/ms-dashboard/on-tour/update/{id}', [MsDashboardController::class, 'updateTour'])->name('ms.on_tour.update');
    Route::post('/ms-dashboard/on-tour/delete/{id}', [MsDashboardController::class, 'deleteTour'])->name('ms.on_tour.delete');
    Route::get('/ms-dashboard/recent', [MsDashboardController::class, 'recentList'])->name('ms.recent');
    // MS Dashboard
    Route::get('/ms/adhoc', [MsAdhocController::class, 'index'])->name('ms.adhoc.index');

    // HoD Dashboard
    Route::get('/hod/adhoc', [HodAdhocController::class, 'index'])->name('hod.adhoc.index');
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});

// Admin Authentication Routes (separate from employee auth)
Route::middleware('guest')->group(function () {
    Route::get('/admin-login', [AdminLoginController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/admin-login', [AdminLoginController::class, 'login'])->name('admin.login.post');
});

// Admin Dashboard Routes
Route::get('/admin-dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
Route::post('/admin-logout', [AdminLoginController::class, 'logout'])->name('admin.logout');
// Admin: Staff On Tour (admin-facing list)
Route::get('/admin/on-tour', [App\Http\Controllers\Admin\AdminOnTourController::class, 'index'])->name('admin.on_tour');
Route::get('/admin/on-tour/export', [App\Http\Controllers\Admin\AdminOnTourController::class, 'export'])->name('admin.on_tour.export');
Route::get('/admin/on-tour/edit/{id}', [App\Http\Controllers\Admin\AdminOnTourController::class, 'edit'])->name('admin.on_tour.edit');
Route::post('/admin/on-tour/update/{id}', [App\Http\Controllers\Admin\AdminOnTourController::class, 'update'])->name('admin.on_tour.update');
Route::post('/admin/on-tour/delete/{id}', [App\Http\Controllers\Admin\AdminOnTourController::class, 'delete'])->name('admin.on_tour.delete');

// Admin: Adhoc requests (read-only admin listing)
Route::get('/admin/adhoc', [App\Http\Controllers\Admin\AdminAdhocController::class, 'index'])->name('admin.adhoc');
Route::get('/admin/adhoc/export', [App\Http\Controllers\Admin\AdminAdhocController::class, 'export'])->name('admin.adhoc.export');
Route::get('/admin/adhoc/edit/{id}', [App\Http\Controllers\Admin\AdminAdhocController::class, 'edit'])->name('admin.adhoc.edit');
Route::post('/admin/adhoc/update/{id}', [App\Http\Controllers\Admin\AdminAdhocController::class, 'update'])->name('admin.adhoc.update');
Route::post('/admin/adhoc/delete/{id}', [App\Http\Controllers\Admin\AdminAdhocController::class, 'delete'])->name('admin.adhoc.delete');


// Admin User Management
Route::get('/admin/users', [App\Http\Controllers\Admin\UserManagementController::class, 'index'])->name('admin.users.index');

// Admin: Device Bindings (fraud & compliance checks)
Route::get('/admin/device-bindings', [App\Http\Controllers\Admin\AdminDeviceBindingController::class, 'index'])->name('admin.device_bindings');
Route::post('/admin/device-bindings/unbind/{id}', [App\Http\Controllers\Admin\AdminDeviceBindingController::class, 'unbind'])->name('admin.device_bindings.unbind');
Route::post('/admin/device-bindings/rebind/{id}', [App\Http\Controllers\Admin\AdminDeviceBindingController::class, 'rebind'])->name('admin.device_bindings.rebind');
Route::get('/admin/users/edit/{id}', [App\Http\Controllers\Admin\UserEditController::class, 'edit'])->name('admin.users.edit');
Route::post('/admin/users/edit/{id}', [App\Http\Controllers\Admin\UserEditController::class, 'update'])->name('admin.users.edit.update');

// Admin User Create
Route::get('/admin/users/create', [App\Http\Controllers\Admin\UserCreateController::class, 'create'])->name('admin.users.create');
Route::post('/admin/users/create', [App\Http\Controllers\Admin\UserCreateController::class, 'store'])->name('admin.users.create.store');

// Admin User Toggle Status
Route::get('/admin/users/toggle-status/{id}', [App\Http\Controllers\Admin\UserToggleStatusController::class, 'toggle'])->name('admin.users.toggle-status');

// Admin Roles & Permissions Management
Route::get('/admin/roles-permissions', [App\Http\Controllers\Admin\RolePermissionManagementController::class, 'index'])->name('admin.roles_permissions');
Route::post('/admin/roles-permissions/save-role', [App\Http\Controllers\Admin\RolePermissionManagementController::class, 'saveRole'])->name('admin.roles_permissions.saveRole');
Route::post('/admin/roles-permissions/save-permission', [App\Http\Controllers\Admin\RolePermissionManagementController::class, 'savePermission'])->name('admin.roles_permissions.savePermission');
Route::post('/admin/roles-permissions/save-assign', [App\Http\Controllers\Admin\RolePermissionManagementController::class, 'saveAssign'])->name('admin.roles_permissions.saveAssign');
Route::get('/admin/roles-permissions/toggle-role/{id}', [App\Http\Controllers\Admin\RolePermissionManagementController::class, 'toggleRole'])->name('admin.roles_permissions.toggleRole');
Route::get('/admin/roles-permissions/toggle-perm/{id}', [App\Http\Controllers\Admin\RolePermissionManagementController::class, 'togglePermission'])->name('admin.roles_permissions.togglePerm');

// Admin Settings pages (stubs)
Route::get('/admin/settings/add-admin', [App\Http\Controllers\Admin\AdminSettingsController::class, 'create'])->name('admin.settings.add_admin');
Route::post('/admin/settings/add-admin', [App\Http\Controllers\Admin\AdminSettingsController::class, 'store'])->name('admin.settings.add_admin.store');
Route::get('/admin/settings/change-admin-password', [App\Http\Controllers\Admin\AdminSettingsController::class, 'changePassword'])->name('admin.settings.change_password');
Route::post('/admin/settings/change-admin-password', [App\Http\Controllers\Admin\AdminSettingsController::class, 'updatePassword'])->name('admin.settings.change_password.update');
Route::get('/admin/settings/edit-admin', [App\Http\Controllers\Admin\AdminSettingsController::class, 'edit'])->name('admin.settings.edit_admin');
Route::post('/admin/settings/edit-admin', [App\Http\Controllers\Admin\AdminSettingsController::class, 'update'])->name('admin.settings.edit_admin.update');
Route::get('/admin/settings/toggle-admin', [App\Http\Controllers\Admin\AdminSettingsController::class, 'toggle'])->name('admin.settings.toggle');

// Backwards-compatibility: redirect legacy settings.php to admin settings
Route::get('/settings.php', function () { return redirect()->route('admin.settings.index'); });
// Admin settings index (list admins)
Route::get('/admin/settings', [App\Http\Controllers\Admin\AdminSettingsController::class, 'index'])->name('admin.settings.index');

// Legacy admin PHP page bridges (for old .php URL compatibility)
Route::get('/admin_dashboard.php', function () {
    return redirect()->route('admin.dashboard');
});

