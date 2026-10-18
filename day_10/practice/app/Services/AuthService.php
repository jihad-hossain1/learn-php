<?php

namespace App\Services;

use App\DTOs\AuthLoginDTO;
use App\DTOs\AuthRegisterDTO;
use App\DTOs\AuthVerifyDTO;
use App\DTOs\ForgotPasswordDTO;
use App\DTOs\ResetPasswordDTO;
use App\Models\Otp;
use App\Models\User;
use App\Notifications\SendOtpNotification;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    /**
     * Register a new user and send an email verification OTP.
     *
     * @return array{success: bool, status: int, message: string, data: array<string, mixed>|null}
     */
    public function register(AuthRegisterDTO $dto): array
    {
        $user = User::create([
            'name' => $dto->name,
            'email' => $dto->email,
            'password' => $dto->password,
        ]);

        $otp = $this->generateAndStoreOtp($dto->email, 'verify_email');
        $user->notify(new SendOtpNotification($otp, 'verify_email'));

        return [
            'success' => true,
            'status' => 201,
            'message' => 'Registration successful. An OTP verification code has been sent to your email.',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ],
            ],
        ];
    }

    /**
     * Verify email with 6-digit OTP code and issue Sanctum token.
     *
     * @return array{success: bool, status: int, message: string, data: array<string, mixed>|null}
     */
    public function verify(AuthVerifyDTO $dto): array
    {
        $otpRecord = Otp::where('email', $dto->email)
            ->where('type', 'verify_email')
            ->where('otp', $dto->otp)
            ->first();

        if (! $otpRecord || $otpRecord->isExpired()) {
            return [
                'success' => false,
                'status' => 422,
                'message' => 'Invalid or expired OTP code.',
                'data' => null,
            ];
        }

        $user = User::where('email', $dto->email)->first();

        if (! $user) {
            return [
                'success' => false,
                'status' => 404,
                'message' => 'User not found.',
                'data' => null,
            ];
        }

        $user->email_verified_at = now();
        $user->save();

        $otpRecord->delete();

        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'success' => true,
            'status' => 200,
            'message' => 'Email verified successfully.',
            'data' => [
                'token' => $token,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'email_verified_at' => $user->email_verified_at,
                ],
            ],
        ];
    }

    /**
     * Authenticate user and issue Sanctum token.
     *
     * @return array{success: bool, status: int, message: string, data: array<string, mixed>|null}
     */
    public function login(AuthLoginDTO $dto): array
    {
        $user = User::where('email', $dto->email)->first();

        if (! $user || ! Hash::check($dto->password, $user->password)) {
            return [
                'success' => false,
                'status' => 401,
                'message' => 'Invalid credentials.',
                'data' => null,
            ];
        }

        if (is_null($user->email_verified_at)) {
            return [
                'success' => false,
                'status' => 403,
                'message' => 'Please verify your email before logging in.',
                'data' => null,
            ];
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'success' => true,
            'status' => 200,
            'message' => 'Login successful.',
            'data' => [
                'token' => $token,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ],
            ],
        ];
    }

    /**
     * Send password reset OTP code.
     *
     * @return array{success: bool, status: int, message: string, data: array<string, mixed>|null}
     */
    public function forgotPassword(ForgotPasswordDTO $dto): array
    {
        $user = User::where('email', $dto->email)->first();

        if (! $user) {
            return [
                'success' => false,
                'status' => 404,
                'message' => 'User not found.',
                'data' => null,
            ];
        }

        $otp = $this->generateAndStoreOtp($dto->email, 'reset_password');
        $user->notify(new SendOtpNotification($otp, 'reset_password'));

        return [
            'success' => true,
            'status' => 200,
            'message' => 'Password reset OTP has been sent to your email.',
            'data' => null,
        ];
    }

    /**
     * Reset password using OTP code.
     *
     * @return array{success: bool, status: int, message: string, data: array<string, mixed>|null}
     */
    public function resetPassword(ResetPasswordDTO $dto): array
    {
        $otpRecord = Otp::where('email', $dto->email)
            ->where('type', 'reset_password')
            ->where('otp', $dto->otp)
            ->first();

        if (! $otpRecord || $otpRecord->isExpired()) {
            return [
                'success' => false,
                'status' => 422,
                'message' => 'Invalid or expired OTP code.',
                'data' => null,
            ];
        }

        $user = User::where('email', $dto->email)->first();

        if (! $user) {
            return [
                'success' => false,
                'status' => 404,
                'message' => 'User not found.',
                'data' => null,
            ];
        }

        $user->password = $dto->password;
        $user->save();

        $otpRecord->delete();

        // Revoke tokens on password reset for security
        $user->tokens()->delete();

        return [
            'success' => true,
            'status' => 200,
            'message' => 'Password has been reset successfully. Please log in with your new password.',
            'data' => null,
        ];
    }

    /**
     * Helper to generate and persist a 6-digit OTP code.
     */
    private function generateAndStoreOtp(string $email, string $type): string
    {
        $otp = (string) random_int(100000, 999999);

        Otp::where('email', $email)->where('type', $type)->delete();

        Otp::create([
            'email' => $email,
            'otp' => $otp,
            'type' => $type,
            'expires_at' => now()->addMinutes(10),
        ]);

        return $otp;
    }
}
