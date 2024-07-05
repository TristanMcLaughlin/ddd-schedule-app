<?php

namespace App\Application\Http\Controllers;

use App\Domain\Repositories\BankHolidayRepository;
use Illuminate\Http\JsonResponse;

class BankHolidayController
{
    protected $bankHolidayRepository;

    public function __construct(BankHolidayRepository $bankHolidayRepository)
    {
        $this->bankHolidayRepository = $bankHolidayRepository;
    }

    public function index(): JsonResponse
    {
        $bankHolidays = $this->bankHolidayRepository->list();
        $response = $bankHolidays->map(function ($bankHoliday) {
            return [
                'date' => $bankHoliday->getDate(),
            ];
        });

        return response()->json($response);
    }
}
