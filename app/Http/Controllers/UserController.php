<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function queryUser(Request $request)
    {
        $limit = $request->input('limit', 10);
        $page = $request->input('page', 1);
        $offset = ($page - 1) * $limit;
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
            $query->where('role_name', '=', $role);
        }

        $target = $request->input('target', '');
        if ($target) {
            $reqUserId = session()->get('user')->user_id;
            $reqUser = User::where('user_id', $reqUserId)->first();
            $reqSub = $reqUser->getSubordinateIds();
            if (!in_array($target, $reqSub)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'คุณไม่มีสิทธิ์เข้าถึงข้อมูลของผู้ใช้งานนี้'
                ], 403);
            }

            $user = User::where('user_id', $target)->first();
            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'ไม่พบผู้ใช้งาน'
                ], 404);
            }

            $targetUserIds = array_merge([$target], $user->getSubordinateIds());
            $query->whereIn('user_id', $targetUserIds);
        }

        $total = $query->count();
        $users = $query->offset($offset)->limit($limit)->get();

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
                'message' => 'ไม่พบผู้ใช้งาน'
            ], 404);
        }
        return response()->json(['data' => $user]);
    }

    public function queryAllUser(Request $request)
    {
        $role = $request->input('role', 'sale');
        $users = User::where('role_name', '=', $role)->get();
        return response()->json(['data' => $users]);
    }

    
}