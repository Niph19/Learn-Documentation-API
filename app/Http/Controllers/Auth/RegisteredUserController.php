<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use OpenApi\Attributes as OA;

#[OA\Post(
    path: '/register',
    description: 'Endpoint untuk register user baru.',
    summary: 'Registrasi User baru',
    requestBody: new OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ['name', 'email', 'password', 'password_confirmation'],
            properties: [
                new OA\Property(property: 'name', type: 'string', description: 'Nama lengkap user'),
                new OA\Property(property: 'email', type: 'string', format: 'email', description: 'Alamat email user'),
                new OA\Property(property: 'password', type: 'string', format: 'password', description: 'Password user'),
                new OA\Property(property: 'password_confirmation', type: 'string', format: 'password', description: 'Konfirmasi password user'),
            ],
        ),
    ),
    responses : [(
        new OA\Response(
        response: 201,
        description: 'User berhasil registrasi',
        )
    ),
    new OA\Response(
        response: 422,
        description: 'Validasi gagal, data tidak valid',
    ),
    ],
)]

class RegisteredUserController extends Controller
{
    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->string('password')),
        ]);

        event(new Registered($user));

        Auth::login($user);
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'User registered successfully.',
            'user' => $user,
            'access_token' => $token,
        ]);
    }
}
