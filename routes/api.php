<?php

use App\Http\Controllers\AssignNavigationController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ModeOfAdmissionController;
use App\Http\Controllers\NavigationController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AmbulanceRegisterController;
use App\Http\Controllers\UserRoleController;
use App\Http\Controllers\StateDistrictController;
use App\Http\Controllers\ReportController;
use App\Models\AssignNavigation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('login', [LoginController::class, 'login']);

Route::middleware('auth:api')->group(function () {
Route::post('organizations', [OrganizationController::class, 'store']); // Create a new organization
Route::get('organizations', [OrganizationController::class, 'index']); // Get all organizations
Route::get('organizations/{id}', [OrganizationController::class, 'show']); // Get a specific organization
Route::put('organizations/{id}', [OrganizationController::class, 'update']); // Update an existing organization
Route::patch('organizations/{id}/status', [OrganizationController::class, 'updateOrganizationStatus']);



Route::get('/user-roles', [UserRoleController::class, 'index']); // Get all active user roles
Route::post('/user-roles', [UserRoleController::class, 'store']); // Create a new user role
Route::get('/user-roles/{id}', [UserRoleController::class, 'show']); // Get a specific user role by ID
Route::put('/user-roles/{id}', [UserRoleController::class, 'update']); // Update a user role
Route::patch('user-roles/{id}/status', [UserRoleController::class, 'updateRoleStatus']);


Route::post('/users', [UserController::class, 'store'])->name('users.store'); // Create a new user 
Route::get('/users', [UserController::class, 'index'])->name('users.index');
Route::put('users/{id}', [UserController::class, 'update']);
Route::patch('users/{id}/status', [UserController::class, 'updateUserStatus']);


Route::get('modeofadmissions', [ModeOfAdmissionController::class, 'index']);
Route::post('modeofadmissions', [ModeOfAdmissionController::class, 'store']);
Route::put('modeofadmissions/{id}', [ModeOfAdmissionController::class, 'update']);
Route::patch('modeofadmissions/{id}/status', [ModeOfAdmissionController::class, 'updateModeOfAdmissionStatus']);


Route::post('navigations', [NavigationController::class, 'create']); // Create
Route::get('navigations', [NavigationController::class, 'index']);   // Read
Route::put('navigations/{id}', [NavigationController::class, 'update']); // Update


// Route::get('assignednavigationsonrole', [AssignNavigationController::class, 'allNavAssignedOnRole']); // Update
// Route::post('assignednavigations', [AssignNavigationController::class, 'store']); // Update

Route::apiResource('departments', DepartmentController::class);
Route::patch('departments/{id}/status', [DepartmentController::class, 'updateDepartmentStatus']);

Route::apiResource('assignnavigations', AssignNavigationController::class);


Route::get('/states', [StateDistrictController::class, 'getStates']);
Route::get('/districts', [StateDistrictController::class, 'getDistrictsByState']);


Route::post('ambulanceRegister', [AmbulanceRegisterController::class, 'store']); // Create
Route::get('ambulanceRegistereddata', [ReportController::class, 'index']); // Create
Route::put('updatePatientData', [AmbulanceRegisterController::class, 'updateAdmissionData']); // Create


});