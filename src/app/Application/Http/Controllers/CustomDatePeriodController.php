<?php

namespace App\Application\Http\Controllers;

use App\Domain\Entities\DatePeriod;
use App\Domain\Repositories\DatePeriodRepository;
use App\Domain\Repositories\ProjectRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CustomDatePeriodController
{
    protected $datePeriodRepository;
    protected $projectRepository;

    public function __construct(DatePeriodRepository $datePeriodRepository, ProjectRepository $projectRepository)
    {
        $this->datePeriodRepository = $datePeriodRepository;
        $this->projectRepository = $projectRepository;
    }

    public function store(Request $request, $projectId)
    {
        $request->validate([
            'assignee_id' => 'required|string|exists:assignees,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $project = $this->projectRepository->findById($projectId);

        if (!$project) {
            return response()->json(['error' => 'Project not found'], 404);
        }

        $datePeriod = new DatePeriod(
            uniqid(),
            $projectId,
            $request->input('assignee_id'),
            $request->input('start_date'),
            $request->input('end_date'),
            false, // importedFromJira flag set to false
            null
        );

        $this->datePeriodRepository->save($datePeriod);

        return response()->json([
            'message' => 'Date period added successfully',
            'date_period' => $datePeriod->toArray()
        ], 201);
    }
}
