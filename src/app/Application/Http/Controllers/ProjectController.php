<?php

namespace App\Application\Http\Controllers;

use App\Infrastructure\Services\DatePeriods\AssigneePeriodsFormatterService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ProjectController
{
    protected $assigneePeriodsFormatterService;

    public function __construct(AssigneePeriodsFormatterService $assigneePeriodsFormatterService)
    {
        $this->assigneePeriodsFormatterService = $assigneePeriodsFormatterService;
    }

    public function getFormattedData(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $startDate = $startDate ? Carbon::createFromFormat('Y-m-d', $startDate) : Carbon::now();
        $endDate = $endDate ? Carbon::createFromFormat('Y-m-d', $endDate) : Carbon::now()->addDays(60);

        $data = $this->assigneePeriodsFormatterService->formatAssigneePeriods($startDate, $endDate);
        return response()->json($data);
    }
}
