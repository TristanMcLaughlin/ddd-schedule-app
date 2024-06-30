<?php

namespace App\Infrastructure\Models;

use App\Domain\Entities\DatePeriod;
use Illuminate\Database\Eloquent\Model;

class DatePeriodModel extends Model
{
    protected $table = 'date_periods';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    protected $fillable = ['id', 'project_id', 'assignee_id', 'start_date', 'end_date'];

    public function project()
    {
        return $this->belongsTo(ProjectModel::class, 'project_id');
    }

    public function assignee()
    {
        return $this->belongsTo(AssigneeModel::class, 'assignee_id');
    }

    public function toDomainEntity(): DatePeriod
    {
        return new DatePeriod(
            $this->id,
            $this->project_id,
            $this->assignee_id,
            $this->start_date,
            $this->end_date,
            $this->imported_from_jira
        );
    }
}
