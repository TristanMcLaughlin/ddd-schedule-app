<?php

namespace App\Infrastructure\Models;

use App\Domain\Entities\Assignee;
use Illuminate\Database\Eloquent\Model;

class AssigneeModel extends Model
{
    protected $table = 'assignees';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    protected $fillable = ['id', 'name', 'role'];

    public function datePeriods()
    {
        return $this->hasMany(DatePeriodModel::class, 'assignee_id');
    }

    public function toDomainEntity(): Assignee
    {
        return new Assignee(
            $this->id,
            $this->name,
            $this->role
        );
    }
}
