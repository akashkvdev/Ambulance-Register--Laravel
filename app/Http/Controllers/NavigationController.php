<?php

namespace App\Http\Controllers;

use App\Models\Navigation;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class NavigationController extends Controller
{
    //

    public function create(Request $request)
    {
        try {
            // Validate the request
            $request->validate([
                'nav_name' => 'required|string|max:255|unique:navigations',
                'nav_url' => 'required',
               'entry_by' => 'required|exists:users,user_id',
                'nav_url' => 'required',

            ]);

            // Create a new navigation entry
            $navigation = new Navigation();
            $navigation->nav_name = $request->nav_name;
            $navigation->nav_url = $request->nav_url;
            $navigation->nav_icon = $request->nav_icon;
            $navigation->entry_by =$request->entry_by ; // User ID who created the navigation
            // $navigation->updated_by = Auth::id(); // User ID who last updated the navigation
            $navigation->save();

            return response()->json([
                'message' => 'Navigation created successfully!',
                'data' => $navigation,
            ], 201); // Created response
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->validator->errors(),
            ], 422); // Unprocessable Entity response
        } catch (\Exception $e) {
            // Log the exception for debugging
            // \Log::error('Error creating navigation: ' . $e->getMessage());
            return response()->json([
                'message' => 'An error occurred while creating the navigation.',
                'error' => $e->getMessage(),
            ], 500); // Internal Server Error response
        }
    }

    /**
     * Display a listing of the active Navigations.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            // Retrieve all active navigations with status true
            $navigations = Navigation::where('status', true)->get();
           

            // Check if any navigations were found
            if ($navigations->isEmpty()) {
                return response()->json([
                    'message' => 'No active navigations found.',
                ], 404); // Not Found response
            }

            return response()->json([
                'message' => 'Active navigations retrieved successfully.',
                'data' => $navigations,
            ], 200); // OK response
        } catch (\Exception $e) {
            // Log the exception for debugging purposes
            // \Log::error('Error fetching navigations: ' . $e->getMessage());
            return response()->json([
                'message' => 'An error occurred while fetching navigations.',
                'error' => $e->getMessage(),
            ], 500); // Internal Server Error response
        }
    }

    /**
     * Update the specified Navigation.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        try {
            // Find the navigation entry or throw an exception
            $navigation = Navigation::findOrFail($id);
    
            // Validate the request
            $request->validate([
                'nav_name' => 'sometimes|string|max:255|unique:navigations,nav_name,' . $navigation->nav_id . ',nav_id', // Specify nav_id here
                'nav_url' => 'sometimes|string|max:255', // Ensure to specify string validation if needed
                'nav_icon' => 'sometimes|string|max:255',
                'updated_by' => 'required|exists:users,user_id',
            ]);
    
            // Update the navigation entry
            if ($request->has('nav_name')) {
                $navigation->nav_name = $request->nav_name;
            }
            if ($request->has('nav_url')) {
                $navigation->nav_url = $request->nav_url;
            }
            if ($request->has('nav_icon')) {
                $navigation->nav_icon = $request->nav_icon;
            }
            $navigation->updated_by = $request->updated_by; // Update the last updated user
            $navigation->save();
    
            return response()->json([
                'message' => 'Navigation updated successfully!',
                'data' => $navigation,
            ], 200); // OK response
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->validator->errors(),
            ], 422); // Unprocessable Entity response
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Navigation not found.',
            ], 404); // Not Found response
        } catch (\Exception $e) {
            // Log the exception for debugging
            // \Log::error('Error updating navigation: ' . $e->getMessage());
            return response()->json([
                'message' => 'An error occurred while updating the navigation.',
                'error' => $e->getMessage(),
            ], 500); // Internal Server Error response
        }
    }
    

    /**
     * Display the specified Navigation.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            // Find the navigation entry or throw an exception
            $navigation = Navigation::findOrFail($id);

            return response()->json([
                'message' => 'Navigation retrieved successfully.',
                'data' => $navigation,
            ], 200); // OK response
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Navigation not found.',
            ], 404); // Not Found response
        } catch (\Exception $e) {
            // Log the exception for debugging
            // \Log::error('Error fetching navigation: ' . $e->getMessage());
            return response()->json([
                'message' => 'An error occurred while fetching the navigation.',
                'error' => $e->getMessage(),
            ], 500); // Internal Server Error response
        }
    }

    /**
     * Remove the specified Navigation.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    // public function destroy($id)
    // {
    //     try {
    //         // Find the navigation entry or throw an exception
    //         $navigation = Navigation::findOrFail($id);
    //         $navigation->delete(); // Soft delete or remove the navigation

    //         return response()->json([
    //             'message' => 'Navigation deleted successfully.',
    //         ], 200); // OK response
    //     } catch (ModelNotFoundException $e) {
    //         return response()->json([
    //             'message' => 'Navigation not found.',
    //         ], 404); // Not Found response
    //     } catch (\Exception $e) {
    //         // Log the exception for debugging
    //         // \Log::error('Error deleting navigation: ' . $e->getMessage());
    //         return response()->json([
    //             'message' => 'An error occurred while deleting the navigation.',
    //             'error' => $e->getMessage(),
    //         ], 500); // Internal Server Error response
    //     }
    // }
}