<?php

namespace App\Http\Controllers;

use App\Models\AmbulanceManage;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    //
    public function index(Request $request)
    {
        // Start building the query with relationships
        $query = AmbulanceManage::with([
            'patient', 
            'ambulance', 
            'location', 
            'location.district.state', // assuming state is related to location
            'location.district', // assuming district is related to location
            'hospitality',
            'hospitality.modeofadmission',
            'hospitality.departments',
            'entryBy',
            'payments'
        ]);
    
        // Check if the request has from_date and to_date for filtering
        if ($request->has(['from_date', 'to_date'])) {
            // Apply the date range filter on date_of_visit
            $query->whereBetween('date_of_visit', [$request->from_date, $request->to_date]);
        }
        if ($request->has(['fromTime', 'toTime'])) {
            // Apply the date range filter on date_of_visit
            $query->whereBetween('arrival_time', [$request->fromTime, $request->toTime]);
        }
    
        // Check if the request has an organization_id for filtering
        if ($request->has('organization_id')) {
            $query->where('organization_id', $request->organization_id);
        }
    
        // Fetch the records (filtered or unfiltered)
        $ambulanceRecords = $query->get();
    
        // Return the records as JSON response
        return response()->json(['data' => $ambulanceRecords], 200);
    }
    
    

}
