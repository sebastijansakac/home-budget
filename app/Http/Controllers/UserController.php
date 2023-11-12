<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class UserController extends Controller
{
    #[OA\Post(
        path: '/api/register',
        description: 'Register user',
        requestBody: new OA\RequestBody(
            description: 'Register request body',
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(
                        property: 'first_name',
                        type: 'string',
                    ),
                    new OA\Property(
                        property: 'last_name',
                        type: 'string',
                    ),
                    new OA\Property(
                        property: 'email',
                        type: 'string',
                    ),
                    new OA\Property(
                        property: 'password',
                        type: 'string',
                    ),
                    new OA\Property(
                        property: 'password_confirmation',
                        type: 'string',
                    ),
                ],
            ),
        ),
        tags: ['Auth'],
        responses: [
            new OA\Response(response: 200, description: 'Success'),
            new OA\Response(response: 401, description: 'Not allowed'),
        ]
    )]
    public function register(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'first_name' => ['required', 'string',],
                'last_name' => ['required', 'string',],
                'email' => ['required', 'email',],
                'password' => ['required', 'string',],
                'password_confirmation' => ['required', 'string',],
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'message' => $validator->errors(),
                ], ResponseAlias::HTTP_BAD_REQUEST);
            }
            if ($validator->validated()['password'] !== $validator->validated()['password_confirmation']) {
                return response()->json([
                    'message' => 'Passwords do not match',
                ], ResponseAlias::HTTP_BAD_REQUEST);
            }
            $user = User::create($validator->validated());

            return response()->json([
                'data' => $user->toArray(),
            ], ResponseAlias::HTTP_CREATED);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], ResponseAlias::HTTP_BAD_REQUEST);
        }
    }

    #[OA\Post(
        path: '/api/login',
        description: 'Login user',
        requestBody: new OA\RequestBody(
            description: 'Login request body',
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(
                        property: 'email',
                        type: 'string',
                    ),
                    new OA\Property(
                        property: 'password',
                        type: 'string',
                    ),
                ],
            ),
        ),
        tags: ['Auth'],
        responses: [
            new OA\Response(response: 200, description: 'Success'),
            new OA\Response(response: 401, description: 'Not allowed'),
        ]
    )]
    public function login(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'email' => ['required', 'email'],
                'password' => ['required', 'string'],
            ]);
            if ($user = User::where('email', $validated['email'])->first()) {
                if (!Auth::attempt($request->only('email', 'password'))) {
                    return response()->json([
                        'message' => 'Invalid credentials',
                    ], ResponseAlias::HTTP_BAD_REQUEST);
                }
                $token = $user->createToken('auth_token')->plainTextToken;

                return response()->json([
                    'data' => [
                        'token' => $token,
                    ],
                ], ResponseAlias::HTTP_CREATED);
            }

            return response()->json([
                'message' => 'Invalid credentials',
            ], ResponseAlias::HTTP_BAD_REQUEST);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], ResponseAlias::HTTP_BAD_REQUEST);
        }
    }
}
