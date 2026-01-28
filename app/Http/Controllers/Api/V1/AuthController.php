<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\LoginRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use App\Http\Requests\ForgotPasswordRequest;


class AuthController extends Controller
{

    /**
     * Authenticate user login
     * Validated credentials and returns authentication token
     * @param \App\Http\Requests\LoginRequest $request
     * @return JsonResponse|mixed
     */
    public function login(LoginRequest $request): JsonResponse
    {
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials',
            ], 401);
        }

        $user = Auth::user();
        $token = $user->createToken('auth_token')->plainTextToken;

        // Get user active subscription details
        $subscription = $user->currentSubscription();

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'user' => [
                'id' => $user->id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
                'full_name' => $user->fullName,
                'phone_number' => $user->phone_number,
                'subscription' => $subscription,
            ],
            'token' => $token,
        ], 200);
    }

    /**
     * Update user profile details
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone_number' => 'nullable|string|max:20',
        ]);

        $user = $request->user();

        $user->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'phone_number' => $request->phone_number,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Perfil atualizado com sucesso.',
            'user' => [
                'id' => $user->id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
                'full_name' => $user->fullName, // Ensure your User model has this accessor
                'phone_number' => $user->phone_number,
            ]
        ]);
    }

    /**
     * Send password reset link
     * Generates and emails password reset token
     */
    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return response()->json([
                'success' => true,
                'message' => 'Password reset link sent to your email address.'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Failed to send password reset link. Please try again later.'
        ], 500);
    }

    /**
     * Change user password
     * Validates current password and updates to new password
     */
    public function changePassword(Request $request): JsonResponse
    {
        $request->validate([
            'current_password' => 'required|current_password',
            'new_password' => 'required|confirmed|min:8|different:current_password',
        ]);

        $request->user()->update([
            'password' => Hash::make($request->new_password)
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Senha atualizada com sucesso.'
        ]);
    }

    /**
     * Reset user password
     * Validates reset token and update password
     */
    public function resetPassword(Request $request): JsonResponse
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ]);
                $user->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return response()->json([
                'success' => true,
                'message' => 'Password reset successfully.'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Invalid or expired reset token.'
        ], 400);
    }

    /**
     * Logout user
     * Revokes current authentication token
     * 
     * @param \Illuminate\Http\Request $request
     * @return JsonResponse|mixed
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout bem-sucedido'
        ]);
    }

    /**
     * Get authenticated user profile
     * Returns current user data
     */
    public function profile(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'user' => [
                'id' => $request->user()->id,
                'first_name' => $request->user()->first_name,
                'last_name' => $request->user()->last_name,
                'email' => $request->user()->email,
                'full_name' => $request->user()->fullName,
            ]
        ]);
    }

    /**
     * Verify email address
     */
    public function verifyEmail(Request $request): JsonResponse
    {
        $user = User::findOrFail($request->route('id'));

        if (!hash_equals((string) $request->route('hash'), sha1($user->getEmailForVerification()))) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid verification link.'
            ], 400);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'success' => true,
                'message' => 'Email already verified.'
            ]);
        }

        if ($user->markEmailAsVerified()) {
            return response()->json([
                'success' => true,
                'message' => 'Email verified successfully.'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Failed to verify email.'
        ], 500);
    }

    /**
     * Resend email verification notification
     */
    public function resendVerification(Request $request): JsonResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return response()->json([
                'success' => false,
                'message' => 'Email already verified.'
            ], 400);
        }

        $request->user()->sendEmailVerificationNotification();

        return response()->json([
            'success' => true,
            'message' => 'Verification email sent.'
        ]);
    }

    /**
     * Check email verification status
     */
    public function checkVerificationStatus(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'email_verified' => $request->user()->hasVerifiedEmail(),
            'email_verified_at' => $request->user()->email_verified_at
        ]);
    }

    /**
     * Get current authenticated user
     * 
     * GET /api/user
     */
    public function user(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'success' => true,
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'email' => $user->email,
                    'full_name' => $user->full_name,
                    'phone_number' => $user->phone_number,
                ],
                'subscription' => $user->currentSubscription(),
                'has_active_subscription' => $user->hasActiveSubscription(),
            ]
        ]);
    }
}
