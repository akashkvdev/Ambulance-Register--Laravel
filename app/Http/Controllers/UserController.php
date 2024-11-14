<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserDetail;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Store a newly created user and user details in storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // Validate incoming request
        $validator = Validator::make($request->all(), [
            'user_name' => 'required|string|max:255',
            'gender' => 'nullable|in:male,female,other',
            'user_type' => 'required|boolean',
            'entry_by' => 'required|exists:users,user_id',

            'organization_id' => 'required|exists:organizations,organization_id',
            'role_id' => 'required|exists:user_roles,role_id',
            // 'email' => 'required|email|unique:users,email',
            'contact_no' => 'required|numeric|digits_between:5,15|unique:user_details,contact_no', // Assuming max length of contact number
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

            // Create user
            $user = User::create([
                'user_name' => $request->user_name,
                'gender' => $request->gender,
                'user_type' => $request->user_type,
                'entry_by' => $request->entry_by,
                'updated_by' => $request->updated_by,
                'organization_id' => $request->organization_id,
                'role_id' => $request->role_id,
                'email' => $request->email,
                // Do not store password, since it's not in the users table
            ]);

            // Generate unique login ID (5-digit format)
            $lastUserDetail = UserDetail::orderBy('login_id', 'desc')->first();
            $newLoginId = str_pad(($lastUserDetail ? intval($lastUserDetail->login_id) : 0) + 1, 5, '0', STR_PAD_LEFT);

            // Create user detail
            UserDetail::create([
                'login_id' => $newLoginId,
                'user_id' => $user->user_id,
                'contact_no' => $request->contact_no,
                'entry_by' => $request->entry_by,
                'updated_by' => $request->updated_by,
                'status' => true,
            ]);

            // Commit transaction
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'User created successfully.',
                'user' => $user,
            ], 201);
        } catch (QueryException $e) {
            // Rollback transaction in case of error
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Error occurred while creating user.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }



    public function index(Request $request)
    {
        try {
            // Retrieve all users with their details
            // $users = User::with([
            //     'userDetails', // Include only login_id and contact_no for UserDetails
            //     'role:role_id,role_name',           // Include only role_id and role_name for UserRole
            //     'organization:organization_id,organization_name' // Include only organization_id and organization_name for Organization
            // ])->get();

            $entryBy = $request->input('entry_by'); // Get 'entry_by' from the request

            $users = User::with([
                'userDetails', // Include only login_id and contact_no for UserDetails
                'role:role_id,role_name', // Include only role_id and role_name for UserRole
                'organization:organization_id,organization_name' // Include only organization_id and organization_name for Organization
            ])
                ->when($entryBy, function ($query) use ($entryBy) {
                    return $query->where('entry_by', $entryBy); // Filter by entry_by if provided
                })
                ->get();



            return response()->json([
                'success' => true,
                'data' => $users,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving user data.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }



    public function update(Request $request, $id)

    {
        // Validate incoming request
        $validator = Validator::make($request->all(), [
            'user_name' => 'sometimes|required|string|max:255',
            'gender' => 'nullable|in:male,female,other',
            'user_type' => 'sometimes|required|boolean',
            'entry_by' => 'sometimes|required|exists:users,user_id',
            'organization_id' => 'sometimes|required|exists:organizations,organization_id',
            'role_id' => 'sometimes|required|exists:user_roles,role_id',
            'contact_no' => 'sometimes|required|numeric|digits_between:5,15|unique:user_details,contact_no,' . $id . ',user_id', // Assuming contact_no is unique
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

            // Find the user
            $user = User::findOrFail($id);

            // Update user fields
            $user->update([
                'user_name' => $request->user_name ?? $user->user_name,
                'gender' => $request->gender ?? $user->gender,
                'user_type' => $request->user_type ?? $user->user_type,
                'entry_by' => $request->entry_by ?? $user->entry_by,
                'updated_by' => $request->updated_by ?? $user->updated_by,
                'organization_id' => $request->organization_id ?? $user->organization_id,
                'role_id' => $request->role_id ?? $user->role_id,
                'email' => $request->email ?? $user->email,
            ]);

            // Update user detail
            $userDetail = UserDetail::where('user_id', $id)->first();
            if ($userDetail) {
                $userDetail->update([
                    'contact_no' => $request->contact_no ?? $userDetail->contact_no,
                    'entry_by' => $request->entry_by ?? $userDetail->entry_by,
                    'updated_by' => $request->updated_by ?? $userDetail->updated_by,
                    'status' => true, // Assuming you want to keep this as true
                ]);
            }

            // Commit transaction
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'User updated successfully.',
                'user' => $user,
            ], 200);
        } catch (QueryException $e) {
            // Rollback transaction in case of error
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Error occurred while updating user.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function updateUserStatus(Request $request, $id)
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
            $userDetail = UserDetail::where('user_id', $id)->first();

            // Check if the user detail exists
            if (!$userDetail) {
                return response()->json([
                    'success' => false,
                    'message' => 'User details not found.',
                ], 404);
            }

            // Update the status
            $userDetail->update([
                'status' => $request->status,
                'updated_by' => $request->updated_by ?? $userDetail->updated_by, // Update who modified it, if provided
            ]);

            // Commit transaction
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'User status updated successfully.',
                'userDetail' => $userDetail,
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
