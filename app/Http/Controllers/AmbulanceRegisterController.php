<?php

namespace App\Http\Controllers;

use App\Models\Ambulance;
use App\Models\AmbulanceManage;
use App\Models\Location;
use App\Models\Patient;
use App\Models\Payment;
use App\Models\Hospitality;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AmbulanceRegisterController extends Controller
{
    public function store(Request $request)
    {
        // Validate the request data based on the Angular form inputs
        $request->validate([
            'ambulance_number' => 'required|string',
            'arrival_time' => 'required|date_format:H:i',
            'date_of_visit' => 'required|date',
            'coming_from_which_hospital' => 'nullable|string',
            'dist_id' => 'required|exists:districts,dist_id',
            'driver_contact_no' => 'required', // Assuming 10 digit mobile number
            'driver_name' => 'required|string',
            'entry_by' => 'required|exists:users,user_id',
            'location' => 'required|string',
            'patient_name' => 'required|string',
            'organization_id' => 'required|exists:organizations,organization_id',
            // 'payment_amount' => 'nullable|numeric',
            'state_id' => 'required|exists:states,state_id', // Assuming you have state validation too
            // Patient identification fields can be added later
        ]);

        // Start a database transaction
        DB::beginTransaction();

        try {
            // Check if ambulance exists
            $ambulance = Ambulance::where('ambulance_number', $request->ambulance_number)->first();

            // If ambulance does not exist, create a new ambulance entry
            if (!$ambulance) {
                $ambulance = Ambulance::create([
                    'ambulance_number' => $request->ambulance_number,
                    'entry_by' => $request->entry_by,
                ]);
            }

            // Insert patient data into the patients table
            $patient = Patient::create([
                'patient_name' => $request->patient_name,
                // Add patient identification fields here later
                'entry_by' => $request->entry_by,
                'updated_by' => $request->entry_by, // Assuming the same user updates
            ]);

            // Insert into Payments
            // $payment = Payment::create([
            //     'payment_amount' => $request->payment_amount,
            //     'ambulance_id' => $ambulance->ambulance_id,
            //     'entry_by' => $request->entry_by,
            // ]);

            // Insert into Hospitalities
            $hospitality = Hospitality::create([
                'mode_of_admission_id' => $request->mode_of_admission_id ?? null, // Optional fields
                'admitted_area' => $request->admitted_area ?? null,
                'referred_by' => $request->referred_by ?? null,
                'remarks' => $request->remarks ?? null,
                'ip' => $request->ip ?? false,
                'op' => $request->op ?? false,
                'patient_id' => $patient->patient_id, // Use the newly created patient ID
                'entry_by' => $request->entry_by,
                'department_id' => $request->department_id ?? null,
            ]);

            // Get or create the location and retrieve its ID
            $locationId = $this->getLocationId($request->location, $request->dist_id, $request->entry_by);

            // Insert into Ambulance Manages
            AmbulanceManage::create([
                'date_of_visit' => $request->date_of_visit,
                'arrival_time' => $request->arrival_time,
                'hospitality_id' => $hospitality->hospitality_id,
                'patient_id' => $patient->patient_id, // Use the patient ID here as well
                'location_id' => $locationId, // Use the retrieved location ID
                'ambulance_id' => $ambulance->ambulance_id,
                'entry_by' => $request->entry_by,
                // 'payment_id' => $payment->payment_id,
                'driver_name' => $request->driver_name,
                'driver_contact_no' => $request->driver_contact_no,
                'coming_from_which_hospital' => $request->coming_from_which_hospital,
                'organization_id' => $request->organization_id,
            ]);

            // Commit the transaction if everything is successful
            DB::commit();

            return response()->json([
                'message' => 'Data inserted successfully',
                'ambulance_id' => $ambulance->ambulance_id,
                // 'payment_id' => $payment->payment_id,
                'hospitality_id' => $hospitality->hospitality_id,
                'patient_id' => $patient->patient_id, // Return the patient ID as well
            ], 201);
        } catch (\Exception $e) {
            // Rollback the transaction if there's an error
            DB::rollBack();

            return response()->json([
                'message' => 'An error occurred while inserting data: ' . $e->getMessage(),
            ], 500);
        }
    }

    private function getLocationId($locationName, $dist_id, $entry_by)
    {
        // Check if location exists
        $location = Location::where('location', $locationName)->where('dist_id', $dist_id)->first();

        // If not exists, create a new location entry
        if (!$location) {
            $location = Location::create([
                'location' => $locationName,
                'dist_id' => $dist_id,
                'entry_by' => $entry_by,
            ]);
        }

        return $location->location_id;
    }



    public function updateAdmissionData(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'admitted_area' => 'nullable|string',
            'ambulace_manage_id' => 'required|exists:ambulance_manages,ambulace_manage_id',
            'department_id' => 'nullable|exists:departments,department_id',
            'ip' => 'nullable|string',
            'mobile_number' => 'required|string|max:15',
            'mode_of_admission_id' => 'nullable|exists:mode_of_admissions,mode_of_admission_id',
            'op' => 'nullable|string',
            'other_id_no' => 'nullable|string',
            'other_id_type' => 'nullable|string',
            'payment_amount' => 'required|numeric|min:0',
            'referred_by' => 'nullable|string',
            'remarks' => 'nullable|string',
            'updated_by' => 'required|exists:users,user_id',
            'patient_id' => 'required|exists:patients,patient_id',
            'hospitality_id' => 'required|exists:hospitalities,hospitality_id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();

        try {
            // Update ambulance_manages table
            $ambulanceManage = AmbulanceManage::findOrFail($request->ambulace_manage_id);
            $ambulanceManage->updated_by = $request->updated_by;
            $ambulanceManage->save();

            // Update hospitality table
            $hospitality = Hospitality::findOrFail($request->hospitality_id);
            $hospitality->admitted_area = $request->admitted_area;
            $hospitality->mode_of_admission_id = $request->mode_of_admission_id;
            $hospitality->referred_by = $request->referred_by;
            $hospitality->remarks = $request->remarks;
            $hospitality->ip = $request->ip;
            $hospitality->op = $request->op;
            $hospitality->patient_id = $request->patient_id;
            $hospitality->updated_by = $request->updated_by;
            $hospitality->department_id = $request->department_id;
            $hospitality->save();

             // Update patient table
             $patient = Patient::findOrFail($request->patient_id);
             $patient->other_id_type = $request->other_id_type;
             $patient->other_id_no = $request->other_id_no;
             $patient->mobile_number = $request->mobile_number;
             $patient->updated_by = $request->updated_by;
             $patient->save();

            // Insert into payments table
            $payment = new Payment();
            $payment->payment_amount = $request->payment_amount;
            $payment->ambulance_id = $ambulanceManage->ambulance_id ?? null;
            $payment->entry_by = $request->updated_by;
            $payment->updated_by = $request->updated_by;
            $payment->ambulace_manage_id = $request->ambulace_manage_id;
            $payment->save();

            $ambulanceManage->payment_id = $payment->payment_id;
            $ambulanceManage->save();

            DB::commit();

            return response()->json([
                'message' => 'Records updated successfully.',
                'ambulance_manage' => $ambulanceManage,
                'hospitality' => $hospitality,
                'payment' => $payment,
                'patient' => $patient,
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Failed to update records.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }


}
