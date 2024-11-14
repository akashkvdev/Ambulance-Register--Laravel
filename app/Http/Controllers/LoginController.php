<?php

namespace App\Http\Controllers;

use App\Models\AssignNavigation;
use App\Models\User;
use App\Models\UserDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'login_id' => 'required|string|exists:user_details,login_id',
            'contact_no' => 'required|string|exists:user_details,contact_no',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }
    
        // Retrieve the user details
        $userDetail = UserDetail::where([
            'login_id' => $request->login_id,
            'contact_no' => $request->contact_no,
        ])->first();
    
        if (!$userDetail) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid login credentials',
            ], 401);
        }
    
        // Retrieve the associated user
        $user = User::find($userDetail->user_id);
    
        // Generate a token using Passport
        $token = $user->createToken('User Access Token')->accessToken;
    
        // Retrieve the role ID
        $roleId = $user->role_id;
    
        // Get assigned navigations for the user role
        $assignedNavigations = AssignNavigation::where('role_id', $roleId)
            ->with('navigation') // Assuming you have a relationship defined in the model
            ->get();
    
        return response()->json([
            'success' => true,
            'token' => $token,
            'user' => $user,
            'assigned_navigations' => $assignedNavigations,
        ], 200);
    }
    
    
    
    
    
}
