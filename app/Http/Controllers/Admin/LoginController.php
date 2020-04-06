<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/admin/dashboard';

    public function __construct()
    {
        $this->middleware('guest:admin', ['except' => ['logout']]);
    }

    public function showLoginForm()
    {
        return view('admin.login');
    }

    public function login(Request $request)
    {
        $rules = [
            'email' => 'required|email',
            'password' => 'required'
        ];

        $this->validate($request, $rules);

        $credentials = $request->only('email', 'password');

        if (Auth::guard('admin')->attempt($credentials, $request->remember)) {
            return redirect()->intended(route(R_ADMIN_DASHBOARD));
        }

        return redirect()
            ->back()
            ->withInput($request->only('email'))
            ->with(['message' => 'Login failed. Please try again.', 'alert_type' => 'danger']);
    }

    public function logout()
    {
        Auth::guard('admin')->logout();
        return redirect(route(R_ADMIN_LOGIN))
            ->with(['message' => 'You have successfully logged out.', 'alert_type' => 'success']);

    }
}
