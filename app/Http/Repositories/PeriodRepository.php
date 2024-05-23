<?php

namespace App\Http\Repositories;

use App\Http\Interfaces\PeriodInterface;
use App\Http\Requests\SetPeriodRequest;
use App\Models\Period;
use Carbon\Carbon;

class PeriodRepository implements PeriodInterface
{
    public function __construct(
        private Period $period
    ) {
    }


    public function getByEstablishment(): array
    {
        try {
            $auth = auth()->user();
            return [
                'success' => true,
                'message' => 'Establishment periods are gotten successfully',
                'periods' => $auth->person->establishment->periods,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => "Could not retreive periods",
            ];
        }
    }

    public function setPeriod(SetPeriodRequest $request): array
    {
        try {
            $period = $this->period::find($request->period);
            if (!$period) {
                return [
                    "success" => false,
                    "message" => "period not found",
                ];
            }
            if (!$period->can_update(auth()->user())) {
                return [
                    "success" => false,
                    "message" => "You cannot update this period",
                ];
            }

            $startDate = Carbon::parse($request->start_date);
            $endDate = Carbon::parse($request->end_date);
            $existingPeriods = $this->period->where('establishment_id', $period->establishment_id)
                ->where('id', '!=', $period->id)
                ->get();

            foreach ($existingPeriods as $existingPeriod) 
            {
                if ($period->id < $existingPeriod->id) {
                    if ($endDate >= $existingPeriod->start_date) {
                        return [
                            'success' => false,
                            'message' => 'The new period must end before ' . $existingPeriod->name . ' start date'
                        ];
                    }
                }
                if ($period->id > $existingPeriod->id) {
                    if ($startDate <= $existingPeriod->end_date) {
                        return [
                            'success' => false,
                            'message' => 'The new period must end before ' . $existingPeriod->name . ' start date'
                        ];
                    }
                }
                if ($startDate->between($existingPeriod->start_date, $existingPeriod->end_date) ||
                    $endDate->between($existingPeriod->start_date, $existingPeriod->end_date) ||
                    $startDate->lt($existingPeriod->start_date) && $endDate->gt($existingPeriod->end_date)
                ) {
                    return [
                        'success' => false,
                        'message' => 'The new period conflicts with an existing period ' . $existingPeriod->name
                    ];
                }
            }
            $period->update([
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d')
            ]);
            return [
                'success' => true,
                'message' => 'Period has been set successfully',
                'period' => $period,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' =>  "Something went wrong",
            ];
        }
    }
}
