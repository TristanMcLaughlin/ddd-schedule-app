<?php

namespace App\Infrastructure\Services;

use GuzzleHttp\Client;
use App\Domain\Entities\BankHoliday;
use App\Domain\Repositories\BankHolidayRepository;

class BankHolidayImportService
{
    protected $client;
    protected $repository;

    public function __construct(Client $client, BankHolidayRepository $repository)
    {
        $this->client = $client;
        $this->repository = $repository;
    }

    public function import()
    {
        $response = $this->client->get('https://www.gov.uk/bank-holidays.json');
        $data = json_decode($response->getBody()->getContents(), true);

        foreach ($data as $region => $holidays) {
            foreach ($holidays['events'] as $event) {
                $bankHoliday = new BankHoliday($event['date']);
                $this->repository->save($bankHoliday);
            }
        }
    }
}
