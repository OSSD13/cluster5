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
    public function getUser(Request $request)
    {
        $user = User::where('user_id', $request->input('user_id'))->first();
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found'
            ], 404);
        }
        return response()->json([
            'data' => $user
        ]);
    }
    public function queryAllUser(Request $request)
    {
        $role = $request->input('role', 'sale');
        $users = User::where('role_name', '=', $role)->get();
        return response()->json([
            'data' => $users
        ]);
    }
    

    public function createUser(Request $request)
    {
        // validate request
        $validator = \Validator::make($request->all(), [
            'email' => 'required|email',
            'name' => 'required|string|max:255',
            'password' => 'required|string|min:6',
            'role_name' => 'required|string|in:sale,supervisor,ceo',
            'user_status' => 'required|string|in:disabled,normal',
            'manager' => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // create user
        $user = new User();
        $user->email = $request->input('email');
        $user->name = $request->input('name');
        $user->password = bcrypt($request->input(key: 'password'));
        $user->role_name = $request->input('role_name');
        $user->user_status = $request->input('user_status');
        $user->save();

        return response()->json([
            'status' => 'success',
            'message' => 'User created successfully',
            'data' => $user
        ]);
    }

    public function editUser(Request $request)
    {
        // validate request
        $validator = \Validator::make($request->all(), [
            'user_id' => 'required|numeric',
            'email' => 'nullable|email',
            'name' => 'nullable|string|max:255',
            'password' => 'nullable|string|min:6',
            'role_name' => 'nullable|string|in:sale,supervisor,ceo',
            'user_status' => 'nullable|string|in:disabled,normal',
            'manager' => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // find user
        $user = User::where('user_id', $request->input('user_id'))->first();
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found'
            ], 404);
        }

        // update user
        if ($request->input('email')) {
            $user->email = $request->input('email');
        }
        if ($request->input('name')) {
            $user->name = $request->input('name');
        }
        if ($request->input('password')) {
            $user->password = bcrypt($request->input(key: 'password'));
        }
        if ($request->input('role_name')) {
            $user->role_name = $request->input('role_name');
        }
        if ($request->input('user_status')) {
            $user->user_status = $request->input('user_status');
        }
        if ($request->input('manager')) {
            $user->manager = $request->input('manager');
        }
        
        // save user
        $user->save();

        return response()->json([
            'status' => 'success',
            'message' => 'User updated successfully',
            'data' => $user
        ]);
    }
    public function deleteUser(Request $request)
    {
        // validate request
        $validator = \Validator::make($request->all(), [
            'user_id' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // find user
        $user = User::where('user_id', $request->input('user_id'))->first();
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found'
            ], 404);
        }

        // delete user
        $user->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'User deleted successfully'
        ]);
    }
}
