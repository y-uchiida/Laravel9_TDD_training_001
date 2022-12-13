<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SignupController extends Controller
{
    public function index()
    {
        return view('signup.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'max:20'],
        ]);
        User::create([
            ...$request->post(),
            'password' => Hash::make($request->post('password'))
        ]);
    }
}