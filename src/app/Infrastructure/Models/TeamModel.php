<?php

namespace App\Infrastructure\Models;

use App\Domain\Entities\Assignee;
use App\Domain\Entities\DatePeriod;
use App\Domain\Entities\Team;
use Illuminate\Database\Eloquent\Model;

class TeamModel extends Model
{
    protected $table = 'teams';
    protected $primaryKey = 'id';
    public $incrementing = false;

    protected $fillable = ['id', 'name'];

    public function assignees()
    {
        return $this->hasMany(AssigneeModel::class, 'team_id');
    }

    public function toDomainEntity(): Team
    {
        $assignees = $this->assignees->map(function ($assigneeModel) {
            return new Assignee(
                $assigneeModel->id,
                $assigneeModel->name,
                $assigneeModel->role,
                $assigneeModel->team_id
            );
        })->all();

        return new Team(
            $this->id,
            $this->name,
            $assignees
        );
    }
}
