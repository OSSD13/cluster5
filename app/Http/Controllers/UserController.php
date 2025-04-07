<?php

namespace App\Http\Controllers;

use App\Models\PointOfInterestType;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{

    public function queryUser(Request $request)
    {
        $limit = $request->input('limit', 10);
        $page = $request->input('page', 1);
        $offset = ($page - 1) * $limit;

        // magic search with one search field
        $search = $request->input('search', '');

        $query = User::query();
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('email', 'like', "%$search%")
                    ->orWhere('name', 'like', "%$search%")
                    ->orWhere('user_id', 'like', "%$search%")
                    ->orWhere('role_name', 'like', "%$search%")
                    ->orWhere('user_status', 'like', "%$search%");
            });
        }

        $role = $request->input('role', '');
        if ($role) {
            $query->where('role_name', '=',$role);
        }


        $target = $request->input('target', '');
        if ($target) {
            $reqUserId = session()->get('user')->user_id;
            // check if target is user's subordinate
            $reqUser = User::where('user_id', $reqUserId)->first();
            $reqSub = $reqUser->getSubordinateIds();
            if (!in_array($target, $reqSub)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'You are not allowed to view this user branch'
                ], 403);
            }
            // check if target is valid user id
            $user = User::where('user_id', $target)->first();
            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User not found'
                ], 404);
            }
            $subordinate = $user->getSubordinateIds();
            $targetUserIds = array_merge([$target], $subordinate);
            $query->whereIn('user_id', $targetUserIds);
        }

        $total = $query->count();
        $users = $query->offset($offset)->limit($limit)->get();
        // return the response
        return response()->json([
            'data' => $users,
            'total' => $total,
            'page' => $page,
            'limit' => $limit
        ]);
    }

}
