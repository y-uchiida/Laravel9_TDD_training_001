<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule as ValidationRule;

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
            'email' => ['required', 'email', ValidationRule::unique('users', 'email')]
        ]);
        User::create([
            ...$request->post(),
            'password' => Hash::make($request->post('password'))
        ]);
    }
}