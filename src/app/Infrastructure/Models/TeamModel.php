<?php

namespace App\Infrastructure\Models;

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
