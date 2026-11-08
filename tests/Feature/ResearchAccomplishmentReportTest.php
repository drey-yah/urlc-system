<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\ResearchProposal;
use App\Models\TerminalReport;
use App\Models\ResearchAccomplishmentReport;
use App\Notifications\WorkflowStatusNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;

class ResearchAccomplishmentReportTest extends TestCase
{
    use RefreshDatabase;

    protected $coordinator;
    protected $dean;
    protected $director;
    protected $researcher;
    protected $proposal1;

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

        $this->director = User::factory()->create([
            'name' => 'Dr. Director UA',
            'email' => 'director@antiquespride.edu.ph',
            'role' => 'admin',
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
            'proposal_code' => 'PROP-2026-801',
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
    }

    /** @test */
    public function coordinator_can_view_index_and_create_views()
    {
        $response = $this->actingAs($this->coordinator)->get(route('accomplishment_reports.index'));
        $response->assertStatus(200);
        $response->assertSee('Research Accomplishment Reports');

        $responseCreate = $this->actingAs($this->coordinator)->get(route('accomplishment_reports.create'));
        $responseCreate->assertStatus(200);
        $responseCreate->assertSee('Prepare Research Accomplishment Report');
        $responseCreate->assertSee('A. Targets vs. Accomplishment');
        $responseCreate->assertSee('Submit Accomplishment Report to Dean');
    }

    /** @test */
    public function coordinator_can_prepare_accomplishment_report_and_notify_dean()
    {
        Notification::fake();

        $payload = [
            'college' => 'College of Teacher Education',
            'quarter' => '1st Quarter',
            'school_year' => 'SY 2026-2027',
            'fiscal_year' => 2026,
            'report_date' => '2026-11-08',
            'section_a' => [
                1 => [
                    'indicator' => 'Number of research outputs completed within the year',
                    't_q1' => '2',
                    't_q2' => '2',
                    't_q3' => '2',
                    't_q4' => '2',
                    'a_q1' => '1',
                    'a_q2' => '0',
                    'a_q3' => '0',
                    'a_q4' => '0',
                    'remarks' => 'Completed target on track',
                ],
            ],
            'section_b' => [
                'b1' => [
                    [
                        'title' => 'College Research Colloquium',
                        'date' => 'October 15, 2026',
                        'venue' => 'CTE Hall',
                        'persons' => 'Faculty Researchers',
                    ],
                ],
                'b2' => [
                    [
                        'title' => 'PASUC Research Training',
                        'type' => 'Training',
                        'date' => 'September 12, 2026',
                        'venue' => 'Iloilo City',
                        'person' => 'Prof. Ernesto Cruz',
                        'sponsor' => 'CHED',
                    ],
                ],
            ],
            'section_c' => [],
            'section_d' => [
                [
                    'title' => 'Digital Pedagogies in Rural Schools of Antique',
                    'proponent' => 'Prof. Ernesto Cruz',
                    'college' => 'College of Teacher Education',
                    'date_started' => 'Jan 15, 2026',
                    'date_completed' => 'Oct 20, 2026',
                ],
            ],
            'section_e' => [],
            'section_f' => [],
            'section_g' => [],
            'section_h' => [
                'title' => 'CTE Research Colloquium',
                'venue' => 'CTE Hall',
                'date' => 'October 15, 2026',
                'pictures_note' => 'Activity photo documentation on file.',
            ],
            'remarks' => 'Submitted quarterly accomplishment report for CTE.',
        ];

        $response = $this->actingAs($this->coordinator)->post(route('accomplishment_reports.store'), $payload);

        $this->assertDatabaseHas('research_accomplishment_reports', [
            'college' => 'College of Teacher Education',
            'quarter' => '1st Quarter',
            'prepared_by' => $this->coordinator->id,
            'status' => 'pending_dean_noting',
            'fiscal_year' => 2026,
        ]);

        $report = ResearchAccomplishmentReport::where('college', 'College of Teacher Education')->first();
        $this->assertNotNull($report);
        $this->assertEquals('1st Quarter', $report->quarter);

        $response->assertRedirect(route('accomplishment_reports.show', $report->id));

        Notification::assertSentTo(
            $this->dean,
            WorkflowStatusNotification::class,
            function ($notification) {
                return str_contains($notification->title, 'Accomplishment Report Prepared');
            }
        );
    }

    /** @test */
    public function dean_can_officially_note_and_submit_report_to_director()
    {
        Notification::fake();

        $report = ResearchAccomplishmentReport::create([
            'college' => 'College of Teacher Education',
            'quarter' => '1st Quarter',
            'school_year' => 'SY 2026-2027',
            'fiscal_year' => 2026,
            'report_date' => '2026-11-08',
            'prepared_by' => $this->coordinator->id,
            'status' => 'pending_dean_noting',
            'section_a' => [],
        ]);

        $response = $this->actingAs($this->dean)->post(route('accomplishment_reports.note_and_submit', $report->id));
        $response->assertRedirect();

        $report->refresh();
        $this->assertEquals('submitted_to_director', $report->status);
        $this->assertEquals($this->dean->id, $report->noted_by);
        $this->assertNotNull($report->noted_at);

        // Director receives submission notification
        Notification::assertSentTo(
            $this->director,
            WorkflowStatusNotification::class,
            function ($notification) {
                return str_contains($notification->title, 'Research Accomplishment Report Submitted');
            }
        );

        // Coordinator receives noting confirmation
        Notification::assertSentTo(
            $this->coordinator,
            WorkflowStatusNotification::class,
            function ($notification) {
                return str_contains($notification->title, 'Accomplishment Report Noted');
            }
        );
    }

    /** @test */
    public function director_can_acknowledge_and_receive_report()
    {
        Notification::fake();

        $report = ResearchAccomplishmentReport::create([
            'college' => 'College of Teacher Education',
            'quarter' => '1st Quarter',
            'school_year' => 'SY 2026-2027',
            'fiscal_year' => 2026,
            'report_date' => '2026-11-08',
            'prepared_by' => $this->coordinator->id,
            'noted_by' => $this->dean->id,
            'noted_at' => now(),
            'status' => 'submitted_to_director',
            'section_a' => [],
        ]);

        $response = $this->actingAs($this->director)->post(route('accomplishment_reports.acknowledge', $report->id));
        $response->assertRedirect();

        $report->refresh();
        $this->assertEquals('acknowledged', $report->status);
        $this->assertEquals($this->director->id, $report->received_by);
        $this->assertNotNull($report->received_at);

        // Dean & Coordinator receive receipt notification
        Notification::assertSentTo(
            $this->dean,
            WorkflowStatusNotification::class,
            function ($notification) {
                return str_contains($notification->title, 'Accomplishment Report Received');
            }
        );

        Notification::assertSentTo(
            $this->coordinator,
            WorkflowStatusNotification::class,
            function ($notification) {
                return str_contains($notification->title, 'Accomplishment Report Received');
            }
        );
    }

    /** @test */
    public function accomplishment_report_pdf_can_be_generated()
    {
        $report = ResearchAccomplishmentReport::create([
            'college' => 'College of Teacher Education',
            'quarter' => '1st Quarter',
            'school_year' => 'SY 2026-2027',
            'fiscal_year' => 2026,
            'report_date' => '2026-11-08',
            'prepared_by' => $this->coordinator->id,
            'noted_by' => $this->dean->id,
            'noted_at' => now(),
            'status' => 'submitted_to_director',
            'section_a' => [],
        ]);

        $response = $this->actingAs($this->director)->get(route('accomplishment_reports.pdf', $report->id));
        $response->assertStatus(200);
        $this->assertEquals('application/pdf', $response->headers->get('Content-Type'));
    }

    /** @test */
    public function coordinator_can_delete_unnoted_report()
    {
        $report = ResearchAccomplishmentReport::create([
            'college' => 'College of Teacher Education',
            'quarter' => 'Draft Quarter',
            'school_year' => 'SY 2026-2027',
            'fiscal_year' => 2026,
            'report_date' => '2026-11-08',
            'prepared_by' => $this->coordinator->id,
            'status' => 'pending_dean_noting',
            'section_a' => [],
        ]);

        $response = $this->actingAs($this->coordinator)->delete(route('accomplishment_reports.destroy', $report->id));
        $response->assertRedirect(route('accomplishment_reports.index'));

        $this->assertDatabaseMissing('research_accomplishment_reports', [
            'id' => $report->id,
        ]);
    }

    /** @test */
    public function researcher_cannot_prepare_or_note_report()
    {
        $response = $this->actingAs($this->researcher)->get(route('accomplishment_reports.create'));
        $response->assertStatus(403);

        $report = ResearchAccomplishmentReport::create([
            'college' => 'College of Teacher Education',
            'quarter' => '1st Quarter',
            'school_year' => 'SY 2026-2027',
            'fiscal_year' => 2026,
            'report_date' => '2026-11-08',
            'prepared_by' => $this->coordinator->id,
            'status' => 'pending_dean_noting',
            'section_a' => [],
        ]);

        $responseNote = $this->actingAs($this->researcher)->post(route('accomplishment_reports.note_and_submit', $report->id));
        $responseNote->assertStatus(403);
    }
}
