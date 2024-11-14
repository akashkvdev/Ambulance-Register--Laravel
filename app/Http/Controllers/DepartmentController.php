<?php

namespace App\Http\Controllers;
use App\Models\Department;
use App\Models\Organization;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;


class DepartmentController extends Controller
{
    //
    public function store(Request $request)
    {
        try {
            // Validate the request
            $request->validate([
                'department_name' => 'required|string|max:255|unique:departments',
                'entry_by' => 'required|exists:users,user_id',
                'organization_id' => 'required|exists:organizations,organization_id',
                'updated_by' => 'nullable|exists:users,user_id',
                'status' => 'required|boolean',
            ]);

            // Create the department
            $department = Department::create($request->all());

            return response()->json([
                'message' => 'Department created successfully.',
                'data' => $department,
            ], 201); // Created response
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->validator->errors(),
            ], 422); // Unprocessable Entity response
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while creating the department.',
                'error' => $e->getMessage(),
            ], 500); // Internal Server Error response
        }
    }

    /**
     * Display a listing of the active departments.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            // Check if organization_id is provided
            $organizationId = $request->input('organization_id');
    
            if ($organizationId) {
                // Validate organization_id if provided
                $validator = Validator::make($request->all(), [
                    'organization_id' => 'integer|exists:organizations,organization_id',
                ]);
    
                if ($validator->fails()) {
                    return response()->json([
                        'message' => 'Validation failed.',
                        'errors' => $validator->errors(),
                    ], 422); // Unprocessable Entity response
                }
    
                // Fetch active departments for the specified organization with its details
                $departments = Department::with('organization')
                                          ->where('organization_id', $organizationId)
                                          ->where('status', true)
                                          ->get();
    
                // Check if there are no active departments for this organization
                if ($departments->isEmpty()) {
                    return response()->json([
                        'message' => 'No active departments found for this organization.',
                        'organization_id' => $organizationId,
                        'data' => [],
                    ], 404);
                }
    
                return response()->json([
                    'message' => 'Active departments retrieved successfully.',
                    'organization_id' => $organizationId,
                    'data' => $departments,
                ], 200); // OK response
    
            } else {
                // Fetch all departments with their organization details if no organization_id is provided
                $allDepartments = Department::with('organization')->get();
    
                if ($allDepartments->isEmpty()) {
                    return response()->json(['message' => 'No departments or organizations found.'], 404);
                }
    
                return response()->json([
                    'message' => 'All departments with their organizations retrieved successfully.',
                    'data' => $allDepartments,
                ], 200); // OK response
            }
    
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while fetching departments.',
                'error' => $e->getMessage(),
            ], 500); // Internal Server Error response
        }
    }
    
    
    

    /**
     * Display the specified department.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            $department = Department::findOrFail($id);
            return response()->json([
                'message' => 'Department retrieved successfully.',
                'data' => $department,
            ], 200); // OK response
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Department not found.'], 404); // Not Found response
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while fetching the department.',
                'error' => $e->getMessage(),
            ], 500); // Internal Server Error response
        }
    }

    /**
     * Update the specified department.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        try {
            $validatedData = $request->validate([
               'department_name' => 'sometimes|string|max:255|unique:departments,department_name,' . $id . ',department_id',
               'organization_id' => 'sometimes|exists:organizations,organization_id',
                'updated_by' => 'required|exists:users,user_id',
                // 'status' => 'required|boolean',
            ]);
    
            $department = Department::findOrFail($id);
            $department->update($request->only(array_keys($validatedData)));
    
            return response()->json([
                'message' => 'Department updated successfully.',
                'data' => $department,
            ], 200); // OK response
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422); // Unprocessable Entity response
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Department not found.'], 404); // Not Found response
        } catch (QueryException $e) {
            return response()->json([
                'message' => 'Database error occurred.',
                'error' => $e->getMessage(),
            ], 500); // Internal Server Error response
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while updating the department.',
                'error' => $e->getMessage(),
            ], 500); // Internal Server Error response
        }
    }



    public function updateDepartmentStatus(Request $request, $id)
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
            $userDetail = Department::where('department_id', $id)->first();
    
            // Check if the user detail exists
            if (!$userDetail) {
                return response()->json([
                    'success' => false,
                    'message' => 'Department details not found.',
                ], 404);
            }
    
            // Update the status
            $userDetail->update([
                'status' => $request->status,
                'updated_by' => $request->entry_by ?? $userDetail->updated_by, // Update who modified it, if provided
            ]);
    
            // Commit transaction
            DB::commit();
    
            return response()->json([
                'success' => true,
                'message' => 'Department status updated successfully.',
                'userDetail' => $userDetail,
            ], 200);
        } catch (QueryException $e) {
            // Rollback transaction in case of error
            DB::rollBack();
    
            return response()->json([
                'success' => false,
                'message' => 'Error occurred while updating Department status.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

}
