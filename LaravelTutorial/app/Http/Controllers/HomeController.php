<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index()
    {
        return view('index');
    }

    public function login()
    {
        return view('login');
    }

    public function logout()
    {
        auth()->logout();
        return redirect()->route('home');
    }

    public function register()
    {
        return view('register');
    }

    public function API_Login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|exists:users,username',
            'password' => 'required'
        ], [
            'username.required' => 'Username không được để trống',
            'username.exists' => 'Username không tồn tại',
            'password.required' => 'Password không được để trống',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ]);
        }

        if (Auth::attempt(['username' => strtolower($request->username), 'password' => $request->password])) {
            return response()->json([
                'status' => true,
                'message' => 'Đăng nhập thành công'
            ]);
        }

        return response()->json([
            'status' => false,
            'message' => 'Tài khoản hoặc mật khẩu không chính xác'
        ]);
    }

    public function API_Register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|unique:users,username',
            'password' => 'required'
        ], [
            'username.required' => 'Username không được để trống',
            'username.unique' => 'Username đã tồn tại',
            'password.required' => 'Password không được để trống',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ]);
        }

        $user = new User();
        $user->username = strtolower($request->username);
        $user->password = bcrypt($request->password);
        $user->save();

        return response()->json([
            'status' => true,
            'message' => 'Đăng ký thành công'
        ]);
    }
}
