<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\ResearchProposal;
use App\Models\TerminalReport;
use App\Models\CompletedResearchSummary;
use App\Notifications\WorkflowStatusNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;

class CompletedResearchSummaryTest extends TestCase
{
    use RefreshDatabase;

    protected $coordinator;
    protected $dean;
    protected $researcher;
    protected $proposal1;
    protected $proposal2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->coordinator = User::factory()->create([
            'name' => 'Prof. Juan Coordinator',
            'email' => 'coordinator.cte@antiquespride.edu.ph',
            'role' => 'coordinator',
            'department' => 'College of Teacher Education',
            'is_approved' => true,
        ]);

        $this->dean = User::factory()->create([
            'name' => 'Dr. Maria Dean',
            'email' => 'dean.cte@antiquespride.edu.ph',
            'role' => 'dean',
            'department' => 'College of Teacher Education',
            'is_approved' => true,
        ]);

        $this->researcher = User::factory()->create([
            'name' => 'Prof. Ernesto Cruz',
            'email' => 'ernesto@antiquespride.edu.ph',
            'role' => 'researcher',
            'department' => 'College of Teacher Education',
            'is_approved' => true,
        ]);

        $this->proposal1 = ResearchProposal::create([
            'user_id' => $this->researcher->id,
            'title' => 'Digital Pedagogies in Rural Schools of Antique',
            'proposal_code' => 'PROP-2026-701',
            'abstract' => 'Completed educational study.',
            'research_field' => 'Education',
            'college_affiliation' => 'College of Teacher Education',
            'status' => 'completed',
            'total_budget' => 60000.00,
            'current_phase' => 5,
        ]);

        TerminalReport::create([
            'research_proposal_id' => $this->proposal1->id,
            'user_id' => $this->researcher->id,
            'executive_summary' => 'Comprehensive study summary.',
            'status' => 'completed',
        ]);

        $this->proposal2 = ResearchProposal::create([
            'user_id' => $this->researcher->id,
            'title' => 'Literacy Development in Upland Schools',
            'proposal_code' => 'PROP-2026-702',
            'abstract' => 'Literacy outcomes in upland communities.',
            'research_field' => 'Education',
            'college_affiliation' => 'College of Teacher Education',
            'status' => 'completed',
            'total_budget' => 45000.00,
            'current_phase' => 5,
        ]);
    }

    /** @test */
    public function coordinator_can_view_index_and_create_views()
    {
        $response = $this->actingAs($this->coordinator)->get(route('completed_researches_summary.index'));
        $response->assertStatus(200);
        $response->assertSee('Completed Researches Summary Forms');

        $responseCreate = $this->actingAs($this->coordinator)->get(route('completed_researches_summary.create'));
        $responseCreate->assertStatus(200);
        $responseCreate->assertSee('Digital Pedagogies in Rural Schools of Antique');
        $responseCreate->assertSee('COMPLETED RESEARCHES SUMMARY FORM');
        $responseCreate->assertSee('Duration');
        $responseCreate->assertSee('Transmit to College Dean for Noting');
    }

    /** @test */
    public function coordinator_can_prepare_summary_form_and_notify_dean()
    {
        Notification::fake();

        $payload = [
            'title' => 'COMPLETED RESEARCHES SUMMARY FORM',
            'college' => 'College of Teacher Education',
            'report_date' => '2026-11-08',
            'fiscal_year' => 2026,
            'proposal_ids' => [$this->proposal1->id, $this->proposal2->id],
            'items' => [
                $this->proposal1->id => [
                    'project_title' => 'Digital Pedagogies in Rural Schools of Antique',
                    'proponents' => 'Prof. Ernesto Cruz',
                    'duration' => '12 Months',
                    'date_started' => 'Jan 15, 2026',
                    'date_completed' => 'Oct 20, 2026',
                    'budget' => '60,000.00',
                    'expenditure' => '58,400.00',
                ],
                $this->proposal2->id => [
                    'project_title' => 'Literacy Development in Upland Schools',
                    'proponents' => 'Prof. Ernesto Cruz',
                    'duration' => '10 Months',
                    'date_started' => 'Feb 01, 2026',
                    'date_completed' => 'Nov 01, 2026',
                    'budget' => '45,000.00',
                    'expenditure' => '44,200.00',
                ],
            ],
            'remarks' => 'All terminal documentation verified by college coordinator.',
        ];

        $response = $this->actingAs($this->coordinator)->post(route('completed_researches_summary.store'), $payload);

        $this->assertDatabaseHas('completed_research_summaries', [
            'title' => 'COMPLETED RESEARCHES SUMMARY FORM',
            'college' => 'College of Teacher Education',
            'prepared_by' => $this->coordinator->id,
            'status' => 'pending_noting',
            'fiscal_year' => 2026,
        ]);

        $summary = CompletedResearchSummary::where('college', 'College of Teacher Education')->first();
        $this->assertNotNull($summary);
        $this->assertCount(2, $summary->items);
        $this->assertEquals('58,400.00', $summary->items[0]['expenditure']);
        $this->assertEquals('10 Months', $summary->items[1]['duration']);

        $response->assertRedirect(route('completed_researches_summary.show', $summary->id));

        Notification::assertSentTo(
            $this->dean,
            WorkflowStatusNotification::class,
            function ($notification) {
                return str_contains($notification->title, 'Completed Researches Summary Form Prepared');
            }
        );
    }

    /** @test */
    public function dean_can_officially_note_summary_form_and_notify_coordinator()
    {
        Notification::fake();

        $summary = CompletedResearchSummary::create([
            'title' => 'COMPLETED RESEARCHES SUMMARY FORM',
            'college' => 'College of Teacher Education',
            'report_date' => '2026-11-08',
            'fiscal_year' => 2026,
            'prepared_by' => $this->coordinator->id,
            'status' => 'pending_noting',
            'proposal_ids' => [$this->proposal1->id],
            'items' => [
                [
                    'no' => 1,
                    'proposal_id' => $this->proposal1->id,
                    'project_title' => $this->proposal1->title,
                    'proponents' => $this->researcher->name,
                    'duration' => '12 Months',
                    'date_started' => 'Jan 15, 2026',
                    'date_completed' => 'Oct 20, 2026',
                    'budget' => '60,000.00',
                    'expenditure' => '58,400.00',
                ],
            ],
        ]);

        $response = $this->actingAs($this->dean)->post(route('completed_researches_summary.note', $summary->id));
        $response->assertRedirect();

        $summary->refresh();
        $this->assertEquals('noted', $summary->status);
        $this->assertEquals($this->dean->id, $summary->noted_by);
        $this->assertNotNull($summary->noted_at);

        Notification::assertSentTo(
            $this->coordinator,
            WorkflowStatusNotification::class,
            function ($notification) {
                return str_contains($notification->title, 'Completed Researches Summary Form Noted');
            }
        );
    }

    /** @test */
    public function completed_researches_summary_pdf_can_be_generated()
    {
        $summary = CompletedResearchSummary::create([
            'title' => 'COMPLETED RESEARCHES SUMMARY FORM',
            'college' => 'College of Teacher Education',
            'report_date' => '2026-11-08',
            'fiscal_year' => 2026,
            'prepared_by' => $this->coordinator->id,
            'noted_by' => $this->dean->id,
            'noted_at' => now(),
            'status' => 'noted',
            'proposal_ids' => [$this->proposal1->id],
            'items' => [
                [
                    'no' => 1,
                    'proposal_id' => $this->proposal1->id,
                    'project_title' => $this->proposal1->title,
                    'proponents' => $this->researcher->name,
                    'duration' => '12 Months',
                    'date_started' => 'Jan 15, 2026',
                    'date_completed' => 'Oct 20, 2026',
                    'budget' => '60,000.00',
                    'expenditure' => '58,400.00',
                ],
            ],
        ]);

        $response = $this->actingAs($this->dean)->get(route('completed_researches_summary.pdf', $summary->id));
        $response->assertStatus(200);
        $this->assertEquals('application/pdf', $response->headers->get('Content-Type'));
    }

    /** @test */
    public function coordinator_can_delete_unnoted_summary_form()
    {
        $summary = CompletedResearchSummary::create([
            'title' => 'Draft Completed Summary',
            'college' => 'College of Teacher Education',
            'report_date' => '2026-11-08',
            'fiscal_year' => 2026,
            'prepared_by' => $this->coordinator->id,
            'status' => 'pending_noting',
            'proposal_ids' => [$this->proposal1->id],
            'items' => [],
        ]);

        $response = $this->actingAs($this->coordinator)->delete(route('completed_researches_summary.destroy', $summary->id));
        $response->assertRedirect(route('completed_researches_summary.index'));

        $this->assertDatabaseMissing('completed_research_summaries', [
            'id' => $summary->id,
        ]);
    }

    /** @test */
    public function researcher_cannot_prepare_or_note_summary_form()
    {
        $response = $this->actingAs($this->researcher)->get(route('completed_researches_summary.create'));
        $response->assertStatus(403);

        $summary = CompletedResearchSummary::create([
            'title' => 'Test Summary',
            'college' => 'College of Teacher Education',
            'report_date' => '2026-11-08',
            'fiscal_year' => 2026,
            'prepared_by' => $this->coordinator->id,
            'status' => 'pending_noting',
            'proposal_ids' => [$this->proposal1->id],
            'items' => [],
        ]);

        $responseNote = $this->actingAs($this->researcher)->post(route('completed_researches_summary.note', $summary->id));
        $responseNote->assertStatus(403);
    }
}
