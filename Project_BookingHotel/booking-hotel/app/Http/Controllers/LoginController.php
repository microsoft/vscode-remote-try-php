<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
class LoginController extends Controller
{
    public function login(){

        return view('home/login');
    }
    public function register(){
        
        return view('home/register');
    }
    public function postRegister(Request $req)
    {
        // Validation rules
        $validator = Validator::make($req->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
        ]);
    
        // Check if validation fails
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
    
        // Hash the password
        $req->merge(['password' => Hash::make($req->password)]);
    
        try {
            User::create($req->all());
        } catch (\Throwable $th) {
            dd($th);
        }
    
        return redirect()->route('login');
    }
    public function postLogin(Request $req){
        
        if(Auth::attempt(['email'=>$req->email,'password'=>$req->password]))
        {
            return redirect()->route('index');
        }else{
            return redirect()->back()->with('error','Wrong account or password information');
        }
    }
    public function logout(){
        Auth::logout();
        return redirect()->back();
    }
}
