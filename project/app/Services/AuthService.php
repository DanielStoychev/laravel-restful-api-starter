<?php

namespace App\Services;

use App\Models\User;
use App\Events\UserRegistered;
use App\Exceptions\AuthenticationException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;
use Carbon\Carbon;

/**
 * Authentication Service
 * 
 * Handles all authentication-related business logic including
 * user registration, login, logout, and password management.
 */
class AuthService
{
    /**
     * Register a new user
     *
     * @param array $data
     * @return array
     * @throws ValidationException
     */
    public function register(array $data): array
    {
        try {
            DB::beginTransaction();

            // Create the user
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'role' => User::ROLE_USER,
            ]);

            // Generate authentication token
            $token = $user->createToken('auth_token')->plainTextToken;

            // Fire user registered event
            event(new UserRegistered($user));

            DB::commit();

            return [
                'user' => $user,
                'token' => $token,
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception('Registration failed: ' . $e->getMessage());
        }
    }

    /**
     * Authenticate user and generate token
     *
     * @param array $credentials
     * @return array
     * @throws AuthenticationException
     */
    public function login(array $credentials): array
    {
        // Find user by email
        $user = User::where('email', $credentials['email'])->first();

        // Verify user exists and password is correct
        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        // Check if user account is active (not soft deleted)
        if ($user->trashed()) {
            throw ValidationException::withMessages([
                'email' => ['This account has been deactivated.'],
            ]);
        }

        // Revoke existing tokens (optional - for single session)
        // $user->tokens()->delete();

        // Create new token
        $token = $user->createToken('auth_token');
        
        // Update last login timestamp
        $user->update(['last_login_at' => now()]);

        return [
            'user' => $user->fresh(),
            'token' => $token->plainTextToken,
            'expires_at' => $token->accessToken->expires_at ?? Carbon::now()->addHours(24),
        ];
    }

    /**
     * Logout user by revoking current token
     *
     * @param User $user
     * @return void
     */
    public function logout(User $user): void
    {
        // Revoke current access token
        $user->currentAccessToken()->delete();
    }

    /**
     * Logout user from all devices
     *
     * @param User $user
     * @return void
     */
    public function logoutFromAllDevices(User $user): void
    {
        // Revoke all tokens
        $user->tokens()->delete();
    }

    /**
     * Refresh user's authentication token
     *
     * @param User $user
     * @return array
     */
    public function refreshToken(User $user): array
    {
        // Revoke current token
        $user->currentAccessToken()->delete();

        // Create new token
        $token = $user->createToken('auth_token');

        return [
            'token' => $token->plainTextToken,
            'expires_at' => $token->accessToken->expires_at ?? Carbon::now()->addHours(24),
        ];
    }

    /**
     * Send password reset link to user's email
     *
     * @param array $data
     * @return void
     * @throws ValidationException
     */
    public function sendPasswordResetLink(array $data): void
    {
        $status = Password::sendResetLink(['email' => $data['email']]);

        if ($status !== Password::RESET_LINK_SENT) {
            throw ValidationException::withMessages([
                'email' => [trans($status)],
            ]);
        }
    }

    /**
     * Reset user password using reset token
     *
     * @param array $data
     * @return void
     * @throws ValidationException
     */
    public function resetPassword(array $data): void
    {
        $status = Password::reset(
            [
                'email' => $data['email'],
                'password' => $data['password'],
                'password_confirmation' => $data['password_confirmation'],
                'token' => $data['token'],
            ],
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                // Revoke all existing tokens for security
                $user->tokens()->delete();

                event(new PasswordReset($user));
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            throw ValidationException::withMessages([
                'email' => [trans($status)],
            ]);
        }
    }

    /**
     * Change user password (when authenticated)
     *
     * @param User $user
     * @param array $data
     * @return void
     * @throws ValidationException
     */
    public function changePassword(User $user, array $data): void
    {
        // Verify current password
        if (!Hash::check($data['current_password'], $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['The current password is incorrect.'],
            ]);
        }

        // Update password
        $user->update([
            'password' => Hash::make($data['new_password']),
            'remember_token' => Str::random(60),
        ]);

        // Optionally revoke all other tokens for security
        $user->tokens()->where('id', '!=', $user->currentAccessToken()->id)->delete();
    }

    /**
     * Verify user's email address
     *
     * @param User $user
     * @param string $hash
     * @return bool
     */
    public function verifyEmail(User $user, string $hash): bool
    {
        if (!hash_equals(sha1($user->getEmailForVerification()), $hash)) {
            return false;
        }

        if ($user->hasVerifiedEmail()) {
            return true;
        }

        if ($user->markEmailAsVerified()) {
            event(new \Illuminate\Auth\Events\Verified($user));
            return true;
        }

        return false;
    }

    /**
     * Resend email verification notification
     *
     * @param User $user
     * @return void
     * @throws ValidationException
     */
    public function resendEmailVerification(User $user): void
    {
        if ($user->hasVerifiedEmail()) {
            throw ValidationException::withMessages([
                'email' => ['Email is already verified.'],
            ]);
        }

        $user->sendEmailVerificationNotification();
    }

    /**
     * Get user's active sessions/tokens
     *
     * @param User $user
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getActiveSessions(User $user)
    {
        return $user->tokens()->where('expires_at', '>', now())->get();
    }

    /**
     * Revoke specific token
     *
     * @param User $user
     * @param int $tokenId
     * @return bool
     */
    public function revokeToken(User $user, int $tokenId): bool
    {
        $token = $user->tokens()->find($tokenId);
        
        if ($token) {
            return $token->delete();
        }

        return false;
    }

    /**
     * Check if user has required role
     *
     * @param User $user
     * @param string $role
     * @return bool
     */
    public function hasRole(User $user, string $role): bool
    {
        return $user->hasRole($role);
    }

    /**
     * Update user profile
     *
     * @param User $user
     * @param array $data
     * @return User
     */
    public function updateProfile(User $user, array $data): User
    {
        $user->update($data);
        return $user->fresh();
    }
}
