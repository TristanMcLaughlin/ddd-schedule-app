<?php

namespace App\Infrastructure\Models;

use App\Domain\Entities\Team;
use Illuminate\Database\Eloquent\Model;

class TeamModel extends Model
{
    protected $fillable = ['name'];

    public function assignees()
    {
        return $this->hasMany(AssigneeModel::class);
    }

    public function toDomainEntity(): Team
    {
        return new Team(
            $this->id,
            $this->name
        );
    }
}
