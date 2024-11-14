<?php

namespace App\Http\Controllers;

use App\Models\ModeOfAdmission;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ModeOfAdmissionController extends Controller
{
    //
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'mode_of_admission' => 'required|string|unique:mode_of_admissions,mode_of_admission|max:255',
            'entry_by' => 'required|exists:users,user_id'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        try {
            $modeOfAdmission = ModeOfAdmission::create([
                'mode_of_admission' => $request->input('mode_of_admission'),
                'entry_by' => $request->input('entry_by'),
                'updated_by' => $request->input('entry_by'), // Set as the same user for the first entry
                'status' => $request->input('status', true)
            ]);

            return response()->json(['message' => 'Mode of admission created successfully', 'data' => $modeOfAdmission], 201);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to create mode of admission'], 500);
        }
    }

    // Method to list all modes of admission

    public function index()
    {
        try {
            // Retrieve all active user roles with status true
            $allmodesOfAdmission = ModeOfAdmission::all();
            $modesOfAdmission = ModeOfAdmission::where('status', true)->get();

            // Check if any roles were found
            if ($modesOfAdmission->isEmpty()) {
                return response()->json([
                    'message' => 'No active  modes of admission found.',
                ], 404); // Not Found response
            }
            if ($allmodesOfAdmission->isEmpty()) {
                return response()->json([
                    'message' => 'No  Modes of admission found.',
                ], 404); // Not Found response
            }

            return response()->json([
                'message' => 'Active  modes of admission retrieved successfully.',
                'data' => $modesOfAdmission,
                'allRoles' => $allmodesOfAdmission,
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
   

    // Method to update a specific mode of admission
    public function update(Request $request, $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'mode_of_admission' => 'required|string|max:255|unique:mode_of_admissions,mode_of_admission,' . $id . ',mode_of_admission_id',
            'updated_by' => 'required|exists:users,user_id'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        try {
            $modeOfAdmission = ModeOfAdmission::findOrFail($id);
            $modeOfAdmission->update([
                'mode_of_admission' => $request->input('mode_of_admission'),
                'updated_by' => $request->input('updated_by'),
                'status' => $request->input('status', $modeOfAdmission->status) // Keep existing status if not provided
            ]);

            return response()->json(['message' => 'Mode of admission updated successfully', 'data' => $modeOfAdmission], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'Mode of admission not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update mode of admission'], 500);
        }
    }


    public function updateModeOfAdmissionStatus(Request $request, $id)
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
            $modeofadmissions = ModeOfAdmission::where('mode_of_admission_id', $id)->first();
    
            // Check if the user detail exists
            if (!$modeofadmissions) {
                return response()->json([
                    'success' => false,
                    'message' => 'User details not found.',
                ], 404);
            }
    
            // Update the status
            $modeofadmissions->update([
                'status' => $request->status,
                'updated_by' => $request->entry_by ?? $modeofadmissions->updated_by, // Update who modified it, if provided
            ]);
    
            // Commit transaction
            DB::commit();
    
            return response()->json([
                'success' => true,
                'message' => 'Mode Of Admissions status updated successfully.',
                'userDetail' => $modeofadmissions,
            ], 200);
        } catch (QueryException $e) {
            // Rollback transaction in case of error
            DB::rollBack();
    
            return response()->json([
                'success' => false,
                'message' => 'Error occurred while updating user status.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

}
