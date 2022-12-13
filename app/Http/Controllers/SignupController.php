<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class SignupController extends Controller
{
    public function index()
    {
        return view('signup.index');
    }

    public function store(Request $request)
    {
        User::create($request->post());
    }
}