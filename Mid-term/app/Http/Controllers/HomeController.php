<?php

namespace App\Http\Controllers;
use App\Models\User;
use Session;
class HomeController extends Controller
{
    public function changeLanguage($language): \Illuminate\Http\RedirectResponse
    {
        Session::put('language', $language);
        return redirect()->back();
    }
    public function index(){
        return view('admin.home');
    }
}
