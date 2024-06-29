<?php

namespace App\Application\Http\Controllers;

use App\Infrastructure\Services\DatePeriods\AssigneePeriodsFormatterService;

class ProjectController
{
    protected $assigneePeriodsFormatterService;

    public function __construct(AssigneePeriodsFormatterService $assigneePeriodsFormatterService)
    {
        $this->assigneePeriodsFormatterService = $assigneePeriodsFormatterService;
    }

    public function getFormattedData()
    {
        $data = $this->assigneePeriodsFormatterService->formatAssigneePeriods();
        return response()->json($data);
    }
}
