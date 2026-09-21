<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\URL;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            return redirect()->intended(route('admin.dashboard'));
        }

        return back()
            ->withErrors(['email' => 'These credentials do not match our records.'])
            ->onlyInput('email');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    /*
    |--------------------------------------------------------------------------
    | Forgot / reset password
    |--------------------------------------------------------------------------
    */

    public function showForgotPassword(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * E-mail a reset link (branded HTML, see App\Mail\ResetPasswordMail).
     */
    public function sendResetLink(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        // Always the same response — never reveal whether the account exists.
        return back()->with(
            'status',
            __('If that e-mail belongs to a Kopi Rider staff account, a reset link is on its way. Please also check your spam folder.')
        );
    }

    /**
     * Show the "choose a new password" form.
     * The link is signed (see User::resetUrl) — reject tampered/expired ones.
     */
    public function showResetForm(Request $request, string $token): View|RedirectResponse
    {
        if (! URL::hasValidSignature($request)) {
            return redirect()
                ->route('password.request')
                ->withErrors(['email' => 'This reset link is invalid or has expired. Please request a new one.']);
        }

        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email', ''),
        ]);
    }

    /**
     * Store the new password.
     */
    public function resetPassword(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'token' => ['required', 'string'],
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $status = Password::reset(
            $data,
            function ($user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()
                ->route('login')
                ->with('status', __('Your new password is active — sign in below.'));
        }

        return back()
            ->withErrors(['email' => __($status)])
            ->onlyInput('email');
    }
}
