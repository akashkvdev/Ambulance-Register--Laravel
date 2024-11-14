<?php

namespace App\Http\Controllers;

use App\Models\AssignNavigation;
use App\Models\Navigation;
use App\Models\UserRole;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AssignNavigationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            $roleId = $request->input('role_id');

            if (!$roleId) {
                return response()->json(['message' => 'Role ID is required.'], 400);
            }

            $assignNavigations = AssignNavigation::with(['navigation', 'userrole', 'user'])
                ->where('role_id', $roleId)
                ->get();

            if ($assignNavigations->isEmpty()) {
                return response()->json(['message' => 'No pages are assigned for the given role.'], 404);
            }

            $result = [
                'role_id' => $roleId,
                'role_name' => $assignNavigations->first()->userrole->role_name,
                'navigation_count' => $assignNavigations->count(),
                'navigations' => $assignNavigations->map(function ($item) {
                    return [
                        'nav_id' => $item->nav_id,
                        'assign_nav_id' => $item->assign_navigations_id,
                        'nav_name' => $item->navigation->nav_name,
                        'entry_by' => $item->entry_by,
                        'entry_by_user' => $item->user->user_name,
                    ];
                }),
            ];

            return response()->json($result, 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to retrieve assigned navigations.', 'error' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $validatedData = $request->validate([
                '*.nav_id' => 'required|exists:navigations,nav_id',
                '*.role_id' => 'required|exists:user_roles,role_id',
                '*.entry_by' => 'required|exists:users,user_id',
            ]);

            foreach ($validatedData as $nav) {
                $exists = AssignNavigation::where('nav_id', $nav['nav_id'])
                    ->where('role_id', $nav['role_id'])
                    ->exists();

                if ($exists) {
                    $roleName = UserRole::where('role_id', $nav['role_id'])->value('role_name');
                    $navName = Navigation::where('nav_id', $nav['nav_id'])->value('nav_name');

                    return response()->json([
                        'message' => 'Validation failed.',
                        'errors' => ["The navigation '{$navName}' is already assigned to role '{$roleName}'."]
                    ], 422);
                }

                AssignNavigation::create($nav);
            }

            return response()->json(['message' => 'Navigations assigned successfully.'], 201);
        } catch (ValidationException $e) {
            return response()->json(['message' => 'Validation failed.', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to create assigned navigations.', 'error' => $e->getMessage()], 500);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $assignNavigation = AssignNavigation::find($id);

            if (!$assignNavigation) {
                return response()->json(['message' => 'Assigned navigation not found.'], 404);
            }

            $assignNavigation->delete();

            return response()->json(['message' => 'Assigned navigation deleted successfully.'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete assigned navigation.', 'error' => $e->getMessage()], 500);
        }
    }
}
