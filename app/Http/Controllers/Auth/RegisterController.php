<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    use RegistersUsers;

    /**
     * Redirect after registration
     */
    protected $redirectTo = '/library'; // 🔥 FIXED

    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    /**
     * Create user (🔥 ROLE FIX HERE)
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'user', // 🔥 CRITICAL
        ]);
    }

    /**
     * Optional: override redirect logic (recommended)
     */
    protected function redirectTo()
    {
        return '/library'; // 🔥 ensures consistency
    }
}