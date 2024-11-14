<?php

namespace App\Http\Controllers;

use App\Models\District;
use App\Models\State;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StateDistrictController extends Controller
{
    //

    public function getStates()
    {
        try {
            // Retrieve all states from the database
            $states = State::all();

            // Check if any states are found
            if ($states->isEmpty()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'No states found in the database.',
                    'data' => [],
                ], 200);
            }

            return response()->json([
                'status' => 'success',
                'data' => $states,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while fetching states: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get districts by state.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDistrictsByState(Request $request)
    {
        // Validate the incoming request
        $validator = Validator::make($request->all(), [
            'state_id' => 'required|exists:states,state_id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation error: ' . $validator->errors()->first(),
            ], 422);
        }

        try {
            // Retrieve districts for the specified state
            $districts = District::where('state_id', $request->input('state_id'))->get();

            // Check if any districts were found
            if ($districts->isEmpty()) {
                return response()->json([
                    'status' => 'success',
                    'state_id' => $request->input('state_id'),
                    'message' => 'No districts found for the specified state.',
                    'districts' => [],
                ], 200);
            }

            return response()->json([
                'status' => 'success',
                'state_id' => $request->input('state_id'),
                'districts' => $districts,
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'State not found: ' . $e->getMessage(),
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while fetching districts: ' . $e->getMessage(),
            ], 500);
        }
    }

}
