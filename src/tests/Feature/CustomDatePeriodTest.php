<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Infrastructure\Models\ProjectModel;
use App\Infrastructure\Models\AssigneeModel;
use Carbon\Carbon;

class CustomDatePeriodTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_validates_request_and_inserts_custom_date_period()
    {
        // Create a dummy project
        $project = ProjectModel::create([
            'id' => 'dummy-project-id',
            'name' => 'Dummy Project',
            'build_status' => 'In Progress',
            'rag_status' => 'Green',
        ]);

        // Create a dummy assignee
        $assignee = AssigneeModel::create([
            'id' => 'dummy-assignee-id',
            'name' => 'Dummy Assignee',
            'email' => 'dummy@example.com',
            'role' => 'Developer',
        ]);

        // Define the payload for the request
        $payload = [
            'assignee_id' => $assignee->id,
            'start_date' => Carbon::now()->format('Y-m-d'),
            'end_date' => Carbon::now()->addDays(5)->format('Y-m-d'),
        ];

        // Define the expected date period
        $expectedDatePeriod = [
            'project_id' => $project->id,
            'assignee_id' => $assignee->id,
            'start_date' => $payload['start_date'],
            'end_date' => $payload['end_date'],
            'imported_from_jira' => false,
        ];

        // Perform the POST request
        $response = $this->postJson("/api/projects/{$project->id}/date-periods", $payload);


        // Assert validation passes and date period is inserted
        $response->assertStatus(201)
            ->assertJson([
                'message' => 'Date period added successfully',
                'date_period' => $expectedDatePeriod,
            ]);

        // Assert the date period is in the database
        $this->assertDatabaseHas('date_periods', $expectedDatePeriod);
    }

    /** @test */
    public function it_validates_request_data()
    {
        // Create a dummy project
        $project = ProjectModel::create([
            'id' => 'dummy-project-id',
            'name' => 'Dummy Project',
            'build_status' => 'In Progress',
            'rag_status' => 'Green',
        ]);

        // Define an invalid payload (missing assignee_id)
        $invalidPayload = [
            'start_date' => Carbon::now()->format('Y-m-d'),
            'end_date' => Carbon::now()->addDays(5)->format('Y-m-d'),
        ];

        // Perform the POST request
        $response = $this->postJson("/api/projects/{$project->id}/date-periods", $invalidPayload);

        // Assert validation fails
        $response->assertStatus(422)
            ->assertInvalid(['assignee_id']);
    }
}
