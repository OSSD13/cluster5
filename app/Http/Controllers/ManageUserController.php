<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ManageUserController extends Controller
{
    
    public function createUser(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'email' => 'required|email',
            'name' => 'required|string|max:255',
            'password' => 'required|string|min:6',
            'role_name' => 'required|string|in:sale,supervisor,ceo',
            'user_status' => 'required|string|in:disabled,normal',
            'manager' => 'nullable|numeric',
        ], [
            'email.required' => 'กรุณากรอกอีเมล',
            'email.email' => 'รูปแบบอีเมลไม่ถูกต้อง',
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
        if ($request->input('manager')) $user->manager = $request->input('manager');

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

        $user->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'ลบผู้ใช้งานเรียบร้อยแล้ว'
        ]);
    }
}
