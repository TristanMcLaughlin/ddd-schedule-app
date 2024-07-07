<?php

namespace App\Infrastructure\Models;

use Illuminate\Database\Eloquent\Model;
use App\Domain\Entities\BacklogTicket;

class BacklogTicketModel extends Model
{
    protected $table = 'backlog_tickets';
    protected $primaryKey = 'ticket_id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    protected $fillable = ['ticket_id', 'assignee_id', 'priority', 'start_date', 'end_date'];

    public function assignee()
    {
        return $this->belongsTo(AssigneeModel::class, 'assignee_id');
    }

    public function toDomainEntity(): BacklogTicket
    {
        return new BacklogTicket(
            $this->ticket_id,
            $this->assignee_id,
            $this->priority,
            $this->start_date,
            $this->end_date
        );
    }
}
