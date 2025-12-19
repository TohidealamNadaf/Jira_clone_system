<?php
/**
 * Authentication Controller
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Session;
use App\Services\AuthService;

class AuthController extends Controller
{
    private AuthService $auth;

    public function __construct()
    {
        $this->auth = new AuthService();
    }

    /**
     * Handle root path - redirect based on auth status
     */
    public function home(Request $request): void
    {
        if (auth()) {
            $this->redirect(url('/dashboard'));
        } else {
            $this->redirect(url('/login'));
        }
    }

    /**
     * Show login form
     */
    public function showLogin(Request $request): string
    {
        return $this->view('auth.login');
    }

    /**
     * Process login
     */
    public function login(Request $request): void
    {
        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        $remember = (bool) $request->input('remember');

        if (!$this->auth->attempt($data['email'], $data['password'], $remember)) {
            Session::flash('error', 'Invalid email or password.');
            Session::flash('_old_input', ['email' => $data['email']]);
            $this->redirect(url('/login'));
        }

        // Redirect to intended URL or dashboard
        $intended = Session::pullIntended(url('/dashboard'));
        $this->redirect($intended);
    }

    /**
     * Logout user
     */
    public function logout(Request $request): void
    {
        $this->auth->logout();
        Session::flash('success', 'You have been logged out.');
        $this->redirect(url('/login'));
    }

    /**
     * Show forgot password form
     */
    public function showForgotPassword(Request $request): string
    {
        return $this->view('auth.forgot-password');
    }

    /**
     * Send password reset link
     */
    public function sendResetLink(Request $request): void
    {
        $data = $request->validate([
            'email' => 'required|email',
        ]);

        $token = $this->auth->createPasswordResetToken($data['email']);

        if ($token) {
            // Queue email
            $resetUrl = url("/reset-password/$token");
            
            app(\App\Core\Mailer::class)->queue(
                $data['email'],
                'Reset Your Password',
                $this->view('emails.password-reset', [
                    'resetUrl' => $resetUrl,
                    'expiresIn' => '1 hour',
                ])
            );
        }

        // Always show success to prevent email enumeration
        Session::flash('success', 'If an account exists with that email, you will receive a password reset link.');
        $this->redirect(url('/login'));
    }

    /**
     * Show reset password form
     */
    public function showResetForm(Request $request): string
    {
        $token = $request->param('token');
        return $this->view('auth.reset-password', ['token' => $token]);
    }

    /**
     * Reset password
     */
    public function resetPassword(Request $request): void
    {
        $data = $request->validate([
            'token' => 'required',
            'password' => 'required|password|confirmed',
        ]);

        if (!$this->auth->resetPassword($data['token'], $data['password'])) {
            Session::flash('error', 'Invalid or expired reset token.');
            $this->redirect(url('/forgot-password'));
        }

        Session::flash('success', 'Your password has been reset. Please log in.');
        $this->redirect(url('/login'));
    }
}
