<?php

namespace App\Infrastructure\Models;

use App\Domain\Entities\BankHoliday;
use Illuminate\Database\Eloquent\Model;

class BankHolidayModel extends Model
{
    protected $table = 'bank_holidays';
    public $timestamps = false;
    protected $primaryKey = 'date';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['date'];

    public function toDomainEntity(): BankHoliday
    {
        return new BankHoliday(
            $this->date,
        );
    }
}
