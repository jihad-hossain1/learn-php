<?php

use App\Models\Otp;
use App\Models\User;
use App\Notifications\SendOtpNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;

uses(RefreshDatabase::class);

test('user can register successfully and receives an email verification otp', function () {
    Notification::fake();

    $response = $this->postJson('/api/auth/register', [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'password' => 'secret123',
    ]);

    $response->assertStatus(201)
        ->assertJson([
            'success' => true,
            'data' => [
                'user' => [
                    'name' => 'John Doe',
                    'email' => 'john@example.com',
                ],
            ],
        ]);

    $this->assertDatabaseHas('users', [
        'email' => 'john@example.com',
        'email_verified_at' => null,
    ]);

    $this->assertDatabaseHas('otps', [
        'email' => 'john@example.com',
        'type' => 'verify_email',
    ]);

    $user = User::where('email', 'john@example.com')->first();
    Notification::assertSentTo($user, SendOtpNotification::class, function ($notification) {
        return $notification->type === 'verify_email' && strlen($notification->otp) === 6;
    });
});

test('registration fails with duplicate email', function () {
    User::factory()->create([
        'email' => 'john@example.com',
    ]);

    $response = $this->postJson('/api/auth/register', [
        'name' => 'John Duplicate',
        'email' => 'john@example.com',
        'password' => 'secret123',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
});

test('user can verify email with valid otp and receives sanctum token', function () {
    $user = User::factory()->create([
        'email' => 'jane@example.com',
        'email_verified_at' => null,
    ]);

    Otp::create([
        'email' => 'jane@example.com',
        'otp' => '123456',
        'type' => 'verify_email',
        'expires_at' => now()->addMinutes(10),
    ]);

    $response = $this->postJson('/api/auth/verify', [
        'email' => 'jane@example.com',
        'otp' => '123456',
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Email verified successfully.',
        ])
        ->assertJsonStructure([
            'data' => [
                'token',
                'user' => ['id', 'name', 'email', 'email_verified_at'],
            ],
        ]);

    expect($user->fresh()->email_verified_at)->not->toBeNull();

    $this->assertDatabaseMissing('otps', [
        'email' => 'jane@example.com',
        'otp' => '123456',
    ]);
});

test('email verification fails with invalid or expired otp', function () {
    User::factory()->create([
        'email' => 'jane@example.com',
        'email_verified_at' => null,
    ]);

    Otp::create([
        'email' => 'jane@example.com',
        'otp' => '123456',
        'type' => 'verify_email',
        'expires_at' => now()->subMinutes(1), // Expired
    ]);

    $response = $this->postJson('/api/auth/verify', [
        'email' => 'jane@example.com',
        'otp' => '123456',
    ]);

    $response->assertStatus(422)
        ->assertJson([
            'success' => false,
            'message' => 'Invalid or expired OTP code.',
        ]);
});

test('login succeeds for verified user with correct credentials', function () {
    $user = User::factory()->create([
        'email' => 'user@example.com',
        'password' => Hash::make('password123'),
        'email_verified_at' => now(),
    ]);

    $response = $this->postJson('/api/auth/login', [
        'email' => 'user@example.com',
        'password' => 'password123',
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Login successful.',
        ])
        ->assertJsonStructure([
            'data' => [
                'token',
                'user' => ['id', 'name', 'email'],
            ],
        ]);
});

test('login fails if user email is not verified', function () {
    User::factory()->create([
        'email' => 'unverified@example.com',
        'password' => Hash::make('password123'),
        'email_verified_at' => null,
    ]);

    $response = $this->postJson('/api/auth/login', [
        'email' => 'unverified@example.com',
        'password' => 'password123',
    ]);

    $response->assertStatus(403)
        ->assertJson([
            'success' => false,
            'message' => 'Please verify your email before logging in.',
        ]);
});

test('login fails with invalid credentials', function () {
    User::factory()->create([
        'email' => 'user@example.com',
        'password' => Hash::make('password123'),
        'email_verified_at' => now(),
    ]);

    $response = $this->postJson('/api/auth/login', [
        'email' => 'user@example.com',
        'password' => 'wrongpassword',
    ]);

    $response->assertStatus(401)
        ->assertJson([
            'success' => false,
            'message' => 'Invalid credentials.',
        ]);
});

test('forgot password generates otp and notifies user', function () {
    Notification::fake();

    $user = User::factory()->create([
        'email' => 'forgot@example.com',
    ]);

    $response = $this->postJson('/api/auth/forgot-password', [
        'email' => 'forgot@example.com',
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Password reset OTP has been sent to your email.',
        ]);

    $this->assertDatabaseHas('otps', [
        'email' => 'forgot@example.com',
        'type' => 'reset_password',
    ]);

    Notification::assertSentTo($user, SendOtpNotification::class, function ($notification) {
        return $notification->type === 'reset_password' && strlen($notification->otp) === 6;
    });
});

test('forgot password fails for non-existent email', function () {
    $response = $this->postJson('/api/auth/forgot-password', [
        'email' => 'nonexistent@example.com',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
});

test('reset password succeeds with valid otp and allows login with new password', function () {
    $user = User::factory()->create([
        'email' => 'reset@example.com',
        'password' => Hash::make('oldpassword'),
        'email_verified_at' => now(),
    ]);

    Otp::create([
        'email' => 'reset@example.com',
        'otp' => '654321',
        'type' => 'reset_password',
        'expires_at' => now()->addMinutes(10),
    ]);

    $response = $this->postJson('/api/auth/reset-password', [
        'email' => 'reset@example.com',
        'otp' => '654321',
        'password' => 'newpassword123',
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Password has been reset successfully. Please log in with your new password.',
        ]);

    $this->assertDatabaseMissing('otps', [
        'email' => 'reset@example.com',
        'otp' => '654321',
    ]);

    // Verify login works with new password
    $loginResponse = $this->postJson('/api/auth/login', [
        'email' => 'reset@example.com',
        'password' => 'newpassword123',
    ]);

    $loginResponse->assertStatus(200);
});

test('reset password fails with invalid or expired otp', function () {
    User::factory()->create([
        'email' => 'reset@example.com',
        'password' => Hash::make('oldpassword'),
    ]);

    Otp::create([
        'email' => 'reset@example.com',
        'otp' => '654321',
        'type' => 'reset_password',
        'expires_at' => now()->subMinutes(5), // Expired
    ]);

    $response = $this->postJson('/api/auth/reset-password', [
        'email' => 'reset@example.com',
        'otp' => '654321',
        'password' => 'newpassword123',
    ]);

    $response->assertStatus(422)
        ->assertJson([
            'success' => false,
            'message' => 'Invalid or expired OTP code.',
        ]);
});
