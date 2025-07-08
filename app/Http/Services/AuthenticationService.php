<?php

namespace App\Http\Services;

use App\Models\User;
use App\Models\UserLogin;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Jenssegers\Agent\Agent;
use Log;
use Torann\GeoIP\Facades\GeoIP;

class AuthenticationService
{

    protected $otpExpiresAfter = 5; // in minutes
    protected $maxAttempts = 5;
    protected $blockMinutes = 30;

    public function generateOtp(User $user): User
    {
        $otp = rand(1000, 9999);
        $expiresAt = Carbon::now()->addMinutes($this->otpExpiresAfter);

        $user->update(['otp' => $otp, 'otp_expires_at' => $expiresAt, 'otp_sent_at' => Carbon::now()]);
        $user->refresh();

        // Send email here (use queueable mailable in production)
        Mail::raw("Your OTP code is: $user->otp", function ($message) use ($user) {
            $message->to($user->email)->subject('Your OTP Verification Code');
        });

        return $user;
    }

    public function verifyOtp(User $user, string $otp): bool
    {
        $record = User::where('otp', $otp)
            ->where('otp_expires_at', '>=', Carbon::now())
            ->first();

        if (!$record) {
            return false;
        }
        $record->update(['email_verified_at' => Carbon::now()]);
        return true;
    }

    public function isVerified(User $user): bool
    {
        return $user->email_verified_at !== null;
    }


    public function resendOtp(User $user): array
    {
        $key = "otp_resend_attempts:{$user->id}";
        $blockedKey = "otp_blocked:{$user->id}";

        if (Cache::has($blockedKey)) {
            return [
                'blocked' => true,
                'message' => "You have exceeded the maximum OTP resend attempts. Try again in {$this->blockMinutes} minutes.",
            ];
        }
        $attempts = Cache::get($key, 0) + 1;
        Cache::put($key, $attempts, Carbon::now()->addMinutes($this->blockMinutes));

        if ($attempts > $this->maxAttempts) {
            // Block the user for 30 minutes
            Cache::put($blockedKey, true, Carbon::now()->addMinutes($this->blockMinutes));
            Cache::forget($key); // Reset attempt counter
            return [
                'blocked' => true,
                'message' => "You have exceeded the maximum OTP resend attempts. Try again in {$this->blockMinutes} minutes.",
            ];
        }

        // Re-generate and send OTP
        $this->generateOtp($user);

        return [
            'blocked' => false,
            'message' => 'OTP resent successfully.',
        ];
    }


    public function resetPassword(User $user, string $password): void
    {
        $user->update(['password' => bcrypt($password)]);
    }


    public function getDeviceInfo($ip)
    {
        $agent = new Agent();
        $device = $agent->device();
        $browser = $agent->browser();
        $platform = $agent->platform();

        // Get more detailed info
        $isMobile = $agent->isMobile();
        $isTablet = $agent->isTablet();
        $isDesktop = $agent->isDesktop();

        // Additional info
        $version = $agent->version($browser);

        $deviceId = "{$ip}_{$device}_{$browser}_{$platform}_{$version}_Mobile_{$isMobile}_Tablet_{$isTablet}_Desktop_{$isDesktop}";
        return [
            'device' => $device,
            'browser' => $browser,
            'platform' => $platform,
            'device_id' => $deviceId,
        ];
    }


    public function getUserInfo($ip)
    {
        $daviceInfo = $this->getDeviceInfo($ip);
        $location = GeoIP::getLocation($ip);
        $data = [
            'order_index' => 0,
            'ip' => $ip,
            'country' => $location['country_name'] ?? 'Unknown',
            'city' => $location['city'] ?? 'Unknown',
            'region' => $location['region'] ?? 'Unknown',
            'lat' => $location['lat'] ?? 0.0,
            'lon' => $location['lon'] ?? 0.0,
            'status' => UserLogin::STATUS_ACTIVE,
            'last_login_at' => Carbon::now(),
        ];
        return array_merge($data, $daviceInfo);
    }



}
