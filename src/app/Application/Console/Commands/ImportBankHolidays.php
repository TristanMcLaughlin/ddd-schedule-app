<?php

namespace App\Application\Console\Commands;

use Illuminate\Console\Command;
use App\Infrastructure\Services\BankHolidayImportService;

class ImportBankHolidays extends Command
{
    protected $signature = 'bank-holidays:import';
    protected $description = 'Import bank holidays from GOV.UK';

    protected $importService;

    public function __construct(BankHolidayImportService $importService)
    {
        parent::__construct();
        $this->importService = $importService;
    }

    public function handle()
    {
        $this->importService->import();
        $this->info('Bank holidays imported successfully.');
    }
}
