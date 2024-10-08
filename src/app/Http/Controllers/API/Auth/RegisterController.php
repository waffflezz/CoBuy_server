<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\UnauthorizedException;
use Illuminate\Validation\ValidationException;

class RegisterController extends Controller
{
    public function register(RegisterRequest $request): UserResource
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return new UserResource($user);
    }

    /**
     * @throws ValidationException
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $request->validated();

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            throw ValidationException::withMessages([
                'email' => ['User with this email does not exist.'],
            ]);
        }

        if (!Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'password' => ['The provided password is incorrect.'],
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'accessToken' => $token,
            'tokenType' => 'Bearer',
            'user' => new UserResource($user),
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }

    public function loginCheck(): UserResource
    {
        $user = Auth::user();
        return new UserResource($user);
    }
}
