<?php

namespace App\Service;

use App\Enum\ExpenseType;
use Illuminate\Database\Eloquent\Builder;

class ReportService
{
    public function getAmountForType(Builder $builder, ExpenseType $type): float
    {
        return $builder->clone()->with('category')
            ->whereHas('category', function ($q) use ($type) {
                $q->where('type', $type->name);
            })->sum('amount');
    }
}
