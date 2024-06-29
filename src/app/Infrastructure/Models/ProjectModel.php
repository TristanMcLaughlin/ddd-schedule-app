<?php

namespace App\Infrastructure\Models;

use App\Domain\Entities\Project;
use Illuminate\Database\Eloquent\Model;

class ProjectModel extends Model
{
    protected $table = 'projects';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['id', 'name', 'description'];

    public function datePeriods()
    {
        return $this->hasMany(DatePeriodModel::class, 'project_id');
    }

    public function toDomainEntity(): Project
    {
        return new Project(
            $this->id,
            $this->name,
            $this->description
        );
    }
}
