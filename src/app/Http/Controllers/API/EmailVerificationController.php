<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Mail\SendPinCode;
use App\Models\EmailVerification;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class EmailVerificationController extends Controller
{
    /**
     * @throws Exception
     */
    public function sendVerificationPin(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $user = User::where('email', $data['email'])->first();
        if ($user->hasVerifiedEmail()) {
            throw new Exception('Email already verified');
        }

        $pinCode = rand(100000, 999999);
        $expiresAt = Carbon::now()->addMinutes(10);

        EmailVerification::create([
            'user_id' => $user->id,
            'pin_code' => $pinCode,
            'expires_at' => $expiresAt,
        ]);

         Mail::to($user->email)->send(new SendPinCode($pinCode));

        return response()->json(['message' => 'Verification pin sent']);
    }

    /**
     * @throws Exception
     */
    public function verifyPin(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email|exists:users,email',
            'pinCode' => 'required|string|size:6',
        ]);

        $user = User::where('email', $request['email'])->first();
        $verification = EmailVerification::where('user_id', $user->id)
            ->where('pin_code', $data['pinCode'])
            ->where('expires_at', '>', Carbon::now())
            ->first();

        if (!$verification) {
            throw new Exception('Invalid verification code');
        }

        $user->markEmailAsVerified();
        $user->save();
        $verification->delete();

        return response()->json(['message' => 'Your pin has been verified']);
    }
}
