<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DriverResetPasswordController extends Controller
{
    /**
     * @param Request $request
     * @return string
     */
    public function __invoke(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'email' => ['required', 'email', 'max:255', 'exists:drivers'],
            'token' => ['required', 'string']
        ]);

        if ($validator->fails()) {
            return $validator->errors()->first();
        }

        $email = $request->input('email');
        $token = $request->input('token');
        $url = config('app.password_reset.ios_link')
            . "?token={$token}&email={$email}";
        $minutes = config('app.password_reset.token_lifetime');

        return view('auth.reset-password', compact(['url', 'minutes']));
    }
}
