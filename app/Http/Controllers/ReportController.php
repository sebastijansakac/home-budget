<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Service\ReportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class ReportController extends Controller
{
    #[OA\Get(
        path: '/api/reports',
        summary: 'Get filtered reports',
        security: [
            [
                'bearerAuth' => [],
            ],
        ],
        tags: [
            'Report',
        ],
        parameters: [
            new OA\Parameter(
                name: 'category_id',
                in: 'query',
                required: false,
                schema: new OA\Schema(
                    type: 'integer'
                ),
            ),
            new OA\Parameter(
                name: 'start_date',
                in: 'query',
                required: false,
                schema: new OA\Schema(
                    type: 'string',
                    format: 'date',
                ),
            ),
            new OA\Parameter(
                name: 'end_date',
                in: 'query',
                required: false,
                schema: new OA\Schema(
                    type: 'string',
                    format: 'date',
                ),
            ),
            new OA\Parameter(
                name: 'type',
                in: 'query',
                required: false,
                schema: new OA\Schema(
                    type: 'string',
                    format: 'date',
                    enum: [
                        'Income',
                        'Outcome',
                    ],
                ),
            ),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Success'),
        ]
    )]
    public function getReports(Request $request, ReportService $reportService): JsonResponse
    {
        $categoryId = $request->get('category_id');
        $startDate = $request->date('start_date');
        $endDate = $request->date('end_date');
        $type = $request->get('type');
        $expenses = Expense::where('user_id', Auth::user()->getAuthIdentifier());
        if ($categoryId) {
            $expenses->where('category_id', $categoryId);
        }
        if ($startDate) {
            $expenses->where('created_at', '>', $startDate);
        }
        if ($endDate) {
            $expenses->where('created_at', '<', $endDate);
        }
        if (in_array($type, ['Income', 'Outcome'])) {
            $expenses->whereHas('category', function ($q) use ($type) {
                $q->where('type', $type);
            });
        }
        $income = $reportService->getAmountForType($expenses, 'Income');
        $outcome = $reportService->getAmountForType($expenses, 'Outcome');

        return response()->json([
            'income' => $income,
            'outcome' => $outcome,
            'total' => $income - $outcome,
            'data' => $expenses->with('category')->get(),
        ], ResponseAlias::HTTP_OK);
    }
}
