<?php

namespace Tests\Feature;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Domain\Entities\BacklogTicket;
use App\Domain\Repositories\BacklogTicketRepository;

class EloquentBacklogTicketRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected BacklogTicketRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->app->make(BacklogTicketRepository::class);
    }

    public function test_save_backlog_ticket()
    {

        $backlogTicket = new BacklogTicket(
            'TICKET-123',
            'assignee-1',
            'High',
            'Test',
            '2024-03-01',
            '2024-03-05',
            'status,'
        );

        $this->repository->save($backlogTicket);

        $this->assertDatabaseHas('backlog_tickets', [
            'ticket_id' => 'TICKET-123',
            'assignee_id' => 'assignee-1',
            'priority' => 'High',
            'start_date' => '2024-03-01',
            'end_date' => '2024-03-05',
            'summary' => 'Test',
        ]);
    }

    public function test_list_backlog_tickets()
    {
        Carbon::setTestNow(Carbon::create(2024, 3, 1));

        $backlogTicket1 = new BacklogTicket(
            'TICKET-123',
            'assignee-1',
            'High',
            'Test',
            '2024-03-01',
            '2024-03-05',
            'status,'
        );

        $backlogTicket2 = new BacklogTicket(
            'TICKET-456',
            'assignee-2',
            'Medium',
            'Test',
            '2024-04-01',
            '2024-04-05',
            'status,'
        );

        $this->repository->save($backlogTicket1);
        $this->repository->save($backlogTicket2);

        $backlogTickets = $this->repository->list();

        $this->assertCount(2, $backlogTickets);
        $this->assertEquals('TICKET-123', $backlogTickets[0]->getTicketId());
        $this->assertEquals('TICKET-456', $backlogTickets[1]->getTicketId());
    }
}
