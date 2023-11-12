<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Expense;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use OpenApi\Attributes\RequestBody;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class ExpenseController extends Controller
{
    #[OA\Get(
        path: '/api/expenses',
        summary: 'Get all expenses',
        security: [
            [
                'bearerAuth' => [],
            ],
        ],
        tags: [
            'Expense',
        ],
        responses: [
            new OA\Response(response: 200, description: 'Success'),
        ]
    )]
    public function getExpenses(): JsonResponse
    {
        return response()->json([
            'data' => Expense::where('user_id', Auth::user()->getAuthIdentifier())
                ->with('category')
                ->get(),
        ], ResponseAlias::HTTP_OK);
    }

    #[OA\Get(
        path: '/api/expenses/{id}',
        summary: 'Get single expense',
        security: [
            [
                'bearerAuth' => [],
            ],
        ],
        tags: [
            'Expense',
        ],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
            ),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Success'),
        ]
    )]
    public function getExpense(int $id): JsonResponse
    {
        try {
            return response()->json([
                'data' => Expense::where('user_id', Auth::user()->getAuthIdentifier())
                    ->where('id', $id)
                    ->with('category')
                    ->firstOrFail(),
            ], ResponseAlias::HTTP_OK);
        } catch (\Exception $exception){
            return response()->json([
                'message' => $exception->getMessage(),
            ], ResponseAlias::HTTP_BAD_REQUEST);
        }
    }

    #[OA\Post(
        path: '/api/expenses',
        summary: 'Create new expense',
        security: [
            [
                'bearerAuth' => [],
            ],
        ],
        requestBody: new RequestBody(
            content: new OA\JsonContent(
                required: [
                    'description',
                    'amount',
                    'category_id',
                ],
                properties: [
                    new OA\Property(
                        property: 'description',
                        type: 'string',
                        example: 'Bill for food'
                    ),
                    new OA\Property(
                        property: 'amount',
                        type: 'number',
                        example: 123.45
                    ),
                    new OA\Property(
                        property: 'category_id',
                        type: 'integer',
                        example: 1
                    ),
                ]
            )
        ),
        tags: [
            'Expense',
        ],
        responses: [
            new OA\Response(response: 201, description: 'Success'),
        ]
    )]
    public function createExpense(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->toArray(), [
                'description' => ['required', 'string'],
                'amount' => ['required', 'numeric'],
                'category_id' => ['required', 'integer', Rule::in(Category::all()->pluck('id'))],
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'message' => $validator->errors(),
                ], ResponseAlias::HTTP_BAD_REQUEST);
            }
            $validated = $validator->validated();
            $validated['user_id'] = Auth::user()->getAuthIdentifier();
            $expense = Expense::create($validated);

            return response()->json([
                'data' => $expense->toArray(),
            ], ResponseAlias::HTTP_CREATED);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], ResponseAlias::HTTP_BAD_REQUEST);
        }
    }
}
