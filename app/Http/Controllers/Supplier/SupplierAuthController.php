<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Supplier;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerifyEmail;
use Illuminate\Support\Facades\Redirect;

class SupplierAuthController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::guard('web')->check()) {
            Auth::guard('web')->logout();
            return redirect()->route('supplier.login')->with('status', 'You were logged out from your previous account to access the supplier login.');
        }

        if (Auth::guard('supplier')->check()) {
            return redirect()->route('supplier.dashboard');
        }
        return view('supplier.login');
    }

    public function login(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $email = $request->input('email');
        $password = $request->input('password');
        
        $supplier = Supplier::where('email', $email)->first();

        if ($supplier) {
            if (Hash::check($password, $supplier->password)) {
                Auth::guard('supplier')->login($supplier);
                return redirect()->route('supplier.dashboard');
            } else {
                return view('supplier.login')
                    ->with('message', 'Wrong password. Please try again.')
                    ->with('email', $email);
            }
        } else {
            return view('supplier.login')
                ->with('message', 'Credential error. User not found.')
                ->with('email', $email);
        }
    }

    public function showRegisterForm()
    {
        if (Auth::guard('web')->check()) {
            Auth::guard('web')->logout();
            return redirect()->route('supplier.register')->with('status', 'You were logged out from your previous account to access the supplier register.');
        }

        if (Auth::guard('supplier')->check()) {
            return redirect()->route('supplier.dashboard');
        }
        return view('supplier.register');
    }

    // public function register(Request $request)
    // {
    //     // dd($request->all());
    //     $validator = Validator::make($request->all(), [
    //         'name' => 'required|string|max:255',
    //         'email' => 'required|string|email|max:255|unique:suppliers',
    //         'phone' => 'nullable|string|max:20',
    //         'password' => 'required|string|min:6|confirmed',
    //     ]);

    //     if ($validator->fails()) {
    //         return redirect()->back()->withErrors($validator)->withInput();
    //     }
    //     $supplier = new Supplier();
    //     $supplier->name = $request->name;
    //     $supplier->email = $request->email;
    //     $supplier->phone = $request->phone;
    //     $supplier->password = Hash::make($request->password);
    //     $supplier->slug = Str::slug($request->name);
    //     $supplier->status = 0;
    //     $supplier->save();

    //     return redirect()->route('supplier.login')->with('status', 'Registration successful! Please login.');
    // }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:suppliers,email',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $token = Str::random(60);

        Cache::put('registration_' . $token, [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'slug' => Str::slug($request->name),
            'token' => $token,
        ], now()->addMinutes(5));

        $verificationUrl = URL::temporarySignedRoute(
            'supplier.verify', now()->addMinutes(5), [
                'token' => $token
            ]
        );

        Mail::to($request->email)->send(new VerifyEmail($request->name, $verificationUrl));

        return Redirect::route('supplier.login')->with('status', 'Registration successful! Please check your email to verify your account.');
    }

    public function verify($token)
    {
        $data = Cache::get('registration_' . $token);

        if (!$data) {
            return Redirect::route('supplier.login')->with('status', 'Invalid or expired verification link.');
        }

        $supplier = new Supplier();
        $supplier->name = $data['name'];
        $supplier->email = $data['email'];
        $supplier->phone = $data['phone'];
        $supplier->password = $data['password'];
        $supplier->slug = $data['slug'];
        $supplier->status = 1;
        $supplier->save();

        Cache::forget('registration_' . $token);

        return Redirect::route('supplier.login')->with('status', 'Email successfully verified. You can now log in.');
    }

}
