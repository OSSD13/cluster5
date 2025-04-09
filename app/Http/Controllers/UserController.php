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
        $role = $request->input('role', 'supervisor');
        $users = User::where('role_name', '=', $role)->get();
        return response()->json(['data' => $users]);
    }

    public function createUser(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'email' => 'required|email|unique:users,email',
            'name' => 'required|string|max:255',
            'password' => 'required|string|min:6',
            'role_name' => 'required|string|in:sale,supervisor,ceo',
            'user_status' => 'required|string|in:disabled,normal',
            'manager' => 'nullable|numeric',
        ], [
            'email.required' => 'กรุณากรอกอีเมล',
            'email.email' => 'รูปแบบอีเมลไม่ถูกต้อง',
            'email.unique' => 'อีเมลนี้ถูกใช้งานแล้ว',
            'name.required' => 'กรุณากรอกชื่อ',
            'password.required' => 'กรุณากรอกรหัสผ่าน',
            'password.min' => 'รหัสผ่านต้องมีอย่างน้อย 6 ตัวอักษร',
            'role_name.required' => 'กรุณาเลือกรูปแบบบทบาท',
            'role_name.in' => 'บทบาทที่เลือกไม่ถูกต้อง',
            'user_status.required' => 'กรุณาเลือกสถานะผู้ใช้งาน',
            'user_status.in' => 'สถานะผู้ใช้งานไม่ถูกต้อง',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'การตรวจสอบข้อมูลล้มเหลว',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = new User();
        $user->email = $request->input('email');
        $user->name = $request->input('name');
        $user->password = bcrypt($request->input('password'));
        $user->role_name = $request->input('role_name');
        $user->user_status = $request->input('user_status');
        $user->manager = $request->input('manager');
        $user->save();

        return response()->json([
            'status' => 'success',
            'message' => 'สร้างผู้ใช้งานเรียบร้อยแล้ว',
            'data' => $user
        ]);
    }

    public function editUser(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'user_id' => 'required|numeric',
            'email' => 'nullable|email',
            'name' => 'nullable|string|max:255',
            'password' => 'nullable|string|min:6',
            'role_name' => 'nullable|string|in:sale,supervisor,ceo',
            'user_status' => 'nullable|string|in:disabled,normal',
            'manager' => 'nullable|numeric',
        ], [
            'user_id.required' => 'กรุณาระบุรหัสผู้ใช้งาน',
            'user_id.numeric' => 'รหัสผู้ใช้งานต้องเป็นตัวเลข',
            'email.email' => 'รูปแบบอีเมลไม่ถูกต้อง',
            'password.min' => 'รหัสผ่านต้องมีอย่างน้อย 6 ตัวอักษร',
            'role_name.in' => 'บทบาทไม่ถูกต้อง',
            'user_status.in' => 'สถานะไม่ถูกต้อง',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'การตรวจสอบข้อมูลล้มเหลว',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::where('user_id', $request->input('user_id'))->first();
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'ไม่พบผู้ใช้งานที่ต้องการแก้ไข'
            ], 404);
        }

        if ($request->input('email')) $user->email = $request->input('email');
        if ($request->input('name')) $user->name = $request->input('name');
        if ($request->input('password')) $user->password = bcrypt($request->input('password'));
        if ($request->input('role_name')) $user->role_name = $request->input('role_name');
        if ($request->input('user_status')) $user->user_status = $request->input('user_status');
        if ($request->has('role_name') && $request->input('role_name') !== 'sale') {
            $user->manager = null;
        } elseif ($request->has('manager')) {
            $user->manager = $request->input('manager');
        }
        
        // ห้ามเลือกตัวเองเป็นหัวหน้า
        if ($request->has('manager') && $request->input('manager') == $user->user_id) {
            return response()->json([
                'status' => 'error',
                'message' => 'ไม่สามารถเลือกตัวเองเป็นหัวหน้าได้'
            ], 422);
        }

        $user->save();

        return response()->json([
            'status' => 'success',
            'message' => 'อัปเดตข้อมูลผู้ใช้งานเรียบร้อยแล้ว',
            'data' => $user
        ]);
    }

    public function deleteUser(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'user_id' => 'required|numeric',
        ], [
            'user_id.required' => 'กรุณาระบุรหัสผู้ใช้งาน',
            'user_id.numeric' => 'รหัสผู้ใช้งานต้องเป็นตัวเลข',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'การตรวจสอบข้อมูลล้มเหลว',
                'errors' => $validator->errors()
            ], 422);
        }
    
        $user = User::where('user_id', $request->input('user_id'))->first();
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'ไม่พบผู้ใช้งานที่ต้องการลบ'
            ], 404);
        }
    
        // ✅ เช็คก่อนว่ามีลูกน้องมั้ย แล้วอัปเดต manager_id ให้เป็น null
        User::where('manager', $user->user_id)->update([
            'manager' => null
        ]);
    
        // 🔥 ลบ user
        $user->delete();
    
        return response()->json([
            'status' => 'success',
            'message' => 'ลบผู้ใช้งานเรียบร้อยแล้ว และลูกน้องถูกย้ายออกจากหัวหน้าเรียบร้อย'
        ]);
    }
    
    

    public function getUserOptionsForBranchFilter(Request $request)
    {
        $currentUser = session()->get('user');

        if (!$currentUser) {
            return response()->json(['users' => []]);
        }

        $role = $currentUser->role_name;

        if ($role === 'sale') {
            return response()->json([
                'users' => [[
                    'user_id' => $currentUser->user_id,
                    'name' => $currentUser->name,
                    'role_name' => $currentUser->role_name,
                ]]
            ]);
        }

        if ($role === 'supervisor') {
            $users = User::where(function ($q) use ($currentUser) {
                $q->where('user_id', $currentUser->user_id)
                ->orWhere('manager', $currentUser->user_id);
            })->get(['user_id', 'name', 'role_name']);

            return response()->json(['users' => $users]);
        }

        if ($role === 'ceo') {
            $users = User::whereIn('role_name', ['sale', 'supervisor'])
                        ->get(['user_id', 'name', 'role_name']);
            return response()->json(['users' => $users]);
        }

        return response()->json(['users' => []]);
    }

    

}