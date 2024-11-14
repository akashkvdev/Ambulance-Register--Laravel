<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class OrganizationController extends Controller
{
    //


    // Method to create a new organization
    public function store(Request $request)
    {
        // Validation rules
        $validator = Validator::make($request->all(), [
            'organization_name' => 'required|string|max:255|unique:organizations',
            'org_short_name' => 'required|string|max:255|unique:organizations',
            'organization_location' => 'nullable|string|max:255',
            'status' => 'nullable|boolean',
            'entry_by' => 'nullable|exists:users,user_id' // Assuming user_id exists in the users table
        ]);

        // Check for validation failure
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Try to create a new organization
        try {
            $organization = Organization::create($request->all());
            return response()->json(['message' => 'Organization created successfully.', 'data' => $organization], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while creating the organization: ' . $e->getMessage()], 500);
        }
    }

    // Method to get all organizations
    public function index()
{
    try {
        // Retrieve organizations where organization_status is true
        $allOrganizations = Organization::all();
        $organizations = Organization::where('status', true)->get();

        // Check if organizations are found
        if ($organizations->isEmpty()) {
            return response()->json(['error' => 'No active organizations found.'], 404);
        }
        if ($allOrganizations->isEmpty()) {
            return response()->json(['error' => 'No  allOrganizations found.'], 404);
        }

        return response()->json(['data' => $organizations,'allOrganizations'=>$allOrganizations], 200);
    } catch (\Exception $e) {
        return response()->json(['error' => 'An error occurred while fetching organizations: ' . $e->getMessage()], 500);
    }
}


    // Method to get a single organization by ID
    public function show($id)
    {
        try {
            $organization = Organization::findOrFail($id);
            return response()->json(['data' => $organization], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Organization not found.'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while fetching the organization: ' . $e->getMessage()], 500);
        }
    }



    public function update(Request $request, $id)
    {
        // Validation rules
        $validator = Validator::make($request->all(), [
            'organization_name' => 'required|string|max:255|unique:organizations,organization_name,' . $id . ',organization_id',
            'org_short_name' => 'sometimes|string|max:255|unique:organizations,org_short_name,' . $id . ',organization_id',
            'organization_location' => 'nullable|string|max:255',
            'status' => 'nullable|boolean',
            'entry_by' => 'nullable|exists:users,user_id' // Assuming user_id exists in the users table
        ]);

        // Check for validation failure
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Try to update the organization
        try {
            $organization = Organization::findOrFail($id);
            $organization->update($request->all());
            return response()->json(['message' => 'Organization updated successfully.', 'data' => $organization], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Organization not found.'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while updating the organization: ' . $e->getMessage()], 500);
        }
    }



    public function updateOrganizationStatus(Request $request, $id)
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
            $userDetail = Organization::where('organization_id', $id)->first();
    
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
                'message' => 'Organization status updated successfully.',
                'userDetail' => $userDetail,
            ], 200);
        } catch (QueryException $e) {
            // Rollback transaction in case of error
            DB::rollBack();
    
            return response()->json([
                'success' => false,
                'message' => 'Error occurred while updating Organization status.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

}
