<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    // Show login form
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // Handle login request
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            // Check if the authenticated user is an admin
            if (Auth::user()->role === 'admin') {
                return redirect()->route('orders.index');
            }

            // Logout and return an error message if not an admin
            Auth::logout();
            return back()->withErrors(['email' => 'Access restricted to admins only.']);
        }

        return back()->withErrors(['email' => 'Invalid credentials.']);
    }


    // Handle logout
    public function logout()
    {
        Auth::logout();
        return redirect()->route('login');
    }
}
