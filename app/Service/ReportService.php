<?php

namespace App\Service;

use Illuminate\Database\Eloquent\Builder;

class ReportService
{
    public function getAmountForType(Builder $builder, string $type): float
    {
        return $builder->clone()->with('category')
            ->whereHas('category', function ($q) use ($type) {
                $q->where('type', $type);
            })->sum('amount');
    }
}
