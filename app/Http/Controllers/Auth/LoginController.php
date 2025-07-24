<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;
use App\Models\ClassModel;

class LoginController extends Controller
{
    // Show the login form
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // Handle the login request
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended($this->redirectTo());
        }

        Log::warning('Failed login attempt', [
            'email' => $request->email,
            'ip' => $request->ip()
        ]);

        return back()->withErrors([
            'email' => 'Invalid credentials',
        ])->withInput($request->except('password'));
    }
    
    // Handle logout
    public function logout(Request $request)
    {
        if (Auth::check()) {
            Log::info('User logged out', [
                'id' => Auth::id(),
                'email' => Auth::user()->email
            ]);
        }
        
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/');
    }
    
    protected function redirectTo()
    {
        $user = auth()->user();
        if ($user->role === 'admin') {
            return route('admin.dashboard');
        } elseif ($user->role === 'student') {
            return route('student.dashboard');
        } elseif ($user->role === 'guest') {
            return route('guest.dashboard');
        }
        return '/';
    }
}
