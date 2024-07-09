<?php

namespace App\Infrastructure\Models;

use App\Domain\Entities\DatePeriod;
use App\Domain\Entities\Project;
use Illuminate\Database\Eloquent\Model;

class ProjectModel extends Model
{
    protected $table = 'projects';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = ['id', 'name', 'build_status', 'rag_status'];

    public function datePeriods()
    {
        return $this->hasMany(DatePeriodModel::class, 'project_id');
    }

    public function toDomainEntity(): Project
    {
        $datePeriods = $this->datePeriods->map(function ($datePeriodModel) {
            return new DatePeriod(
                $datePeriodModel->id,
                $datePeriodModel->project_id,
                $datePeriodModel->assignee_id,
                $datePeriodModel->start_date,
                $datePeriodModel->end_date,
                $datePeriodModel->imported_from_jira,
                $datePeriodModel->description,
            );
        })->all();

        return new Project(
            $this->id,
            $this->name,
            $this->build_status,
            $this->rag_status,
            $datePeriods
        );
    }
}
