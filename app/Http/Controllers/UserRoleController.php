<?php

namespace App\Http\Controllers;

use App\Models\UserRole;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class UserRoleController extends Controller
{
    /**
     * Store a new UserRole.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'role_name' => 'required|string|max:255',
                'entry_by' => 'required|exists:users,user_id',
                'updated_by' => 'nullable|exists:users,user_id',
                'status' => 'required|boolean',
            ]);

            $role = UserRole::create($request->all());
            return response()->json([
                'message' => 'Role created successfully.',
                'data' => $role,
            ], 201); // Created response
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->validator->errors(),
            ], 422); // Unprocessable Entity response
        } catch (\Exception $e) {
            // Log the exception for debugging
            // Log::error('Error creating role: ' . $e->getMessage());
            return response()->json([
                'message' => 'An error occurred while creating the role.',
                'error' => $e->getMessage(),
            ], 500); // Internal Server Error response
        }
    }

    /**
     * Display a listing of the active UserRoles.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            // Retrieve all active user roles with status true
            $allRoles = UserRole::all();
            $roles = UserRole::where('status', true)->get();

            // Check if any roles were found
            if ($roles->isEmpty()) {
                return response()->json([
                    'message' => 'No active roles found.',
                ], 404); // Not Found response
            }
            if ($allRoles->isEmpty()) {
                return response()->json([
                    'message' => 'No Roles found.',
                ], 404); // Not Found response
            }

            return response()->json([
                'message' => 'Active roles retrieved successfully.',
                'data' => $roles,
                'allRoles' => $allRoles,
            ], 200); // OK response
        } catch (\Exception $e) {
            // Log the exception for debugging purposes
            // \Log::error('Error fetching user roles: ' . $e->getMessage());

            return response()->json([
                'message' => 'An error occurred while fetching roles.',
                'error' => $e->getMessage(), // Avoid exposing sensitive error details
            ], 500); // Internal Server Error response
        }
    }

    /**
     * Display the specified UserRole.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            $role = UserRole::findOrFail($id);
            return response()->json([
                'message' => 'Role retrieved successfully.',
                'data' => $role,
            ], 200); // OK response
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Role not found.',
            ], 404); // Not Found response
        } catch (\Exception $e) {
            // Log the exception for debugging
            // Log::error('Error fetching role: ' . $e->getMessage());
            return response()->json([
                'message' => 'An error occurred while fetching the role.',
                'error' => $e->getMessage(),
            ], 500); // Internal Server Error response
        }
    }

    /**
     * Update the specified UserRole.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'role_name' => 'sometimes|string|max:255',
                'entry_by' => 'sometimes|exists:users,user_id',
                'updated_by' => 'required|exists:users,user_id',
                'status' => 'sometimes|boolean',
            ]);

            $role = UserRole::findOrFail($id);
            $role->update($request->all());

            return response()->json([
                'message' => 'Role updated successfully.',
                'data' => $role,
            ], 200); // OK response
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->validator->errors(),
            ], 422); // Unprocessable Entity response
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Role not found.',
            ], 404); // Not Found response
        } catch (\Exception $e) {
            // Log the exception for debugging
            // Log::error('Error updating role: ' . $e->getMessage());
            return response()->json([
                'message' => 'An error occurred while updating the role.',
                'error' =>  $e->getMessage(),
            ], 500); // Internal Server Error response
        }
    }

    /**
     * Remove the specified UserRole.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    // public function destroy($id)
    // {
    //     try {
    //         $role = UserRole::findOrFail($id);
    //         $role->delete();

    //         return response()->json([
    //             'message' => 'Role deleted successfully.'
    //         ], 200); // OK response
    //     } catch (ModelNotFoundException $e) {
    //         return response()->json([
    //             'message' => 'Role not found.',
    //         ], 404); // Not Found response
    //     } catch (\Exception $e) {
    //         // Log the exception for debugging
    //         // Log::error('Error deleting role: ' . $e->getMessage());
    //         return response()->json([
    //             'message' => 'An error occurred while deleting the role.',
    //             'error' =>$e->getMessage(),
    //         ], 500); // Internal Server Error response
    //     }
    // }




    public function updateRoleStatus(Request $request, $id)
    {
        // Validate incoming request
        $validator = Validator::make($request->all(), [
            'status' => 'required|boolean', // Validate that status is provided and is a boolean
        ]);
    
        // Check if validation fails
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }
    
        try {
            // Begin transaction
            DB::beginTransaction();
    
            // Find the user detail record
            $userRole = UserRole::where('role_id', $id)->first();
    
            // Check if the user detail exists
            if (!$userRole) {
                return response()->json([
                    'success' => false,
                    'message' => 'User role details not found.',
                ], 404);
            }
    
            // Update the status
            $userRole->update([
                'status' => $request->status,
                'updated_by' => $request->entry_by ?? $userRole->updated_by, // Update who modified it, if provided
            ]);
    
            // Commit transaction
            DB::commit();
    
            return response()->json([
                'success' => true,
                'message' => 'User role status updated successfully.',
                'userRole' => $userRole,
            ], 200);
        } catch (QueryException $e) {
            // Rollback transaction in case of error
            DB::rollBack();
    
            return response()->json([
                'success' => false,
                'message' => 'Error occurred while updating User role status.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
