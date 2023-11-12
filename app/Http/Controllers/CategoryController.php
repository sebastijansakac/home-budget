<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use OpenApi\Attributes as OA;
use OpenApi\Attributes\RequestBody;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class CategoryController extends Controller
{
    #[OA\Get(
        path: '/api/categories',
        summary: 'Get all categories',
        security: [
            [
                'bearerAuth' => [],
            ],
        ],
        tags: [
            'Category',
        ],
        responses: [
            new OA\Response(response: 200, description: 'Success'),
        ]
    )]
    public function getCategories(): JsonResponse
    {
        return response()->json([
            'data' => Category::all(),
        ], ResponseAlias::HTTP_OK);
    }

    #[OA\Post(
        path: '/api/categories',
        summary: 'Create new category',
        security: [
            [
                'bearerAuth' => [],
            ],
        ],
        requestBody: new RequestBody(
            content: new OA\JsonContent(
                required: [
                    'title',
                ],
                properties: [
                    new OA\Property(
                        property: 'title',
                        type: 'string',
                        example: 'Food',
                    ),
                    new OA\Property(
                        property: 'type',
                        type: 'string',
                        enum: [
                            'Income',
                            'Outcome',
                        ],
                        example: 'Income'
                    ),
                ],
            ),
        ),
        tags: [
            'Category',
        ],
        responses: [
            new OA\Response(response: 201, description: 'Success'),
        ]
    )]
    public function createCategory(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->toArray(), [
                'title' => ['required', 'string'],
                'type' => ['required', 'string', Rule::in(['Income', 'Outcome'])],
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'message' => $validator->errors(),
                ], ResponseAlias::HTTP_BAD_REQUEST);
            }
            $category = Category::create($validator->validated());

            return response()->json([
                'data' => $category,
            ], ResponseAlias::HTTP_CREATED);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], ResponseAlias::HTTP_BAD_REQUEST);
        }
    }
}
