<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\ResearchProposal;
use App\Models\TerminalReport;
use App\Models\ServiceCreditList;
use App\Notifications\WorkflowStatusNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;

class ServiceCreditListTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $vprei;
    protected $researcher;
    protected $proposal1;
    protected $proposal2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'name' => 'Dr. Director UA',
            'email' => 'director@antiquespride.edu.ph',
            'role' => 'admin',
            'is_approved' => true,
        ]);

        $this->vprei = User::factory()->create([
            'name' => 'Dr. VPREI Leader',
            'email' => 'vprei@antiquespride.edu.ph',
            'role' => 'vprei',
            'is_approved' => true,
        ]);

        $this->researcher = User::factory()->create([
            'name' => 'Prof. Ernesto Cruz',
            'email' => 'ernesto@antiquespride.edu.ph',
            'role' => 'researcher',
            'is_approved' => true,
            'department' => 'College of Teacher Education',
            'campus' => 'Main Campus (Sibalom)',
        ]);

        $this->proposal1 = ResearchProposal::create([
            'user_id' => $this->researcher->id,
            'title' => 'Digital Pedagogies in Rural Schools of Antique',
            'proposal_code' => 'PROP-2026-601',
            'abstract' => 'Completed educational technology study.',
            'research_field' => 'Education',
            'college_affiliation' => 'College of Teacher Education',
            'status' => 'completed',
            'total_budget' => 60000.00,
            'current_phase' => 5,
        ]);

        TerminalReport::create([
            'research_proposal_id' => $this->proposal1->id,
            'user_id' => $this->researcher->id,
            'executive_summary' => 'Comprehensive summary of results.',
            'terminal_report_file' => 'terminal_reports/report_601.pdf',
            'full_paper_file' => 'terminal_reports/manuscript_601.pdf',
            'certificate_completion_file' => 'certificates/cert_601.pdf',
            'status' => 'completed',
        ]);

        $this->proposal2 = ResearchProposal::create([
            'user_id' => $this->researcher->id,
            'title' => 'Community-Based Agroforestry in Antique Watersheds',
            'proposal_code' => 'PROP-2026-602',
            'abstract' => 'Watershed sustainability and agroforestry implementation.',
            'research_field' => 'Agriculture',
            'college_affiliation' => 'College of Agriculture and Forestry',
            'status' => 'completed',
            'total_budget' => 90000.00,
            'current_phase' => 5,
        ]);

        TerminalReport::create([
            'research_proposal_id' => $this->proposal2->id,
            'user_id' => $this->researcher->id,
            'executive_summary' => 'Agroforestry terminal findings.',
            'status' => 'completed',
        ]);
    }

    /** @test */
    public function research_director_can_view_index_and_create_views()
    {
        $response = $this->actingAs($this->admin)->get(route('service_credits.index'));
        $response->assertStatus(200);
        $response->assertSee('Service Credits Qualification Dossiers');

        $responseCreate = $this->actingAs($this->admin)->get(route('service_credits.create'));
        $responseCreate->assertStatus(200);
        $responseCreate->assertSee('Digital Pedagogies in Rural Schools of Antique');
        $responseCreate->assertSee('List of Researchers Qualified to Avail Service Credits');
        $responseCreate->assertSee('Transmit to VPREI for Approval');
    }

    /** @test */
    public function research_director_can_prepare_service_credit_list_and_notify_vprei()
    {
        Notification::fake();

        $payload = [
            'title' => 'List of Researchers Qualified to Avail Service Credits - FY 2026',
            'report_date' => '2026-11-08',
            'fiscal_year' => 2026,
            'proposal_ids' => [$this->proposal1->id, $this->proposal2->id],
            'items' => [
                $this->proposal1->id => [
                    'college' => 'College of Teacher Education',
                    'date_completed' => 'October 25, 2026',
                    'service_credits' => '15 Days',
                ],
                $this->proposal2->id => [
                    'college' => 'College of Agriculture and Forestry',
                    'date_completed' => 'November 02, 2026',
                    'service_credits' => '10 Days',
                ],
            ],
            'remarks' => 'Completed researches with full terminal manuscripts verified.',
        ];

        $response = $this->actingAs($this->admin)->post(route('service_credits.store'), $payload);

        $this->assertDatabaseHas('service_credit_lists', [
            'title' => 'List of Researchers Qualified to Avail Service Credits - FY 2026',
            'prepared_by' => $this->admin->id,
            'status' => 'pending_approval',
            'fiscal_year' => 2026,
        ]);

        $list = ServiceCreditList::where('title', 'List of Researchers Qualified to Avail Service Credits - FY 2026')->first();
        $this->assertNotNull($list);
        $this->assertCount(2, $list->items);
        $this->assertEquals('15 Days', $list->items[0]['service_credits']);
        $this->assertEquals('10 Days', $list->items[1]['service_credits']);

        $response->assertRedirect(route('service_credits.show', $list->id));

        Notification::assertSentTo(
            $this->vprei,
            WorkflowStatusNotification::class,
            function ($notification) {
                return str_contains($notification->title, 'Service Credits Submitted');
            }
        );
    }

    /** @test */
    public function vprei_can_officially_approve_service_credit_list_and_notify_parties()
    {
        Notification::fake();

        $list = ServiceCreditList::create([
            'title' => 'List of Researchers Qualified to Avail Service Credits - FY 2026',
            'report_date' => '2026-11-08',
            'fiscal_year' => 2026,
            'prepared_by' => $this->admin->id,
            'status' => 'pending_approval',
            'proposal_ids' => [$this->proposal1->id],
            'items' => [
                [
                    'no' => 1,
                    'proposal_id' => $this->proposal1->id,
                    'researchers' => $this->researcher->name,
                    'college' => 'College of Teacher Education',
                    'title' => $this->proposal1->title,
                    'date_completed' => 'October 25, 2026',
                    'service_credits' => '15 Days',
                ],
            ],
        ]);

        $response = $this->actingAs($this->vprei)->post(route('service_credits.approve', $list->id));
        $response->assertRedirect();

        $list->refresh();
        $this->assertEquals('approved', $list->status);
        $this->assertEquals($this->vprei->id, $list->approved_by);
        $this->assertNotNull($list->approved_at);

        // Director receives approval notification
        Notification::assertSentTo(
            $this->admin,
            WorkflowStatusNotification::class,
            function ($notification) {
                return str_contains($notification->title, 'Service Credits List Approved by VPREI');
            }
        );

        // Researcher receives qualification notification
        Notification::assertSentTo(
            $this->researcher,
            WorkflowStatusNotification::class,
            function ($notification) {
                return str_contains($notification->title, 'Service Credits Qualification Approved');
            }
        );
    }

    /** @test */
    public function service_credit_list_pdf_can_be_generated()
    {
        $list = ServiceCreditList::create([
            'title' => 'List of Researchers Qualified to Avail Service Credits - FY 2026',
            'report_date' => '2026-11-08',
            'fiscal_year' => 2026,
            'prepared_by' => $this->admin->id,
            'approved_by' => $this->vprei->id,
            'approved_at' => now(),
            'status' => 'approved',
            'proposal_ids' => [$this->proposal1->id],
            'items' => [
                [
                    'no' => 1,
                    'proposal_id' => $this->proposal1->id,
                    'researchers' => $this->researcher->name,
                    'college' => 'College of Teacher Education',
                    'title' => $this->proposal1->title,
                    'date_completed' => 'October 25, 2026',
                    'service_credits' => '15 Days',
                ],
            ],
        ]);

        $response = $this->actingAs($this->admin)->get(route('service_credits.pdf', $list->id));
        $response->assertStatus(200);
        $this->assertEquals('application/pdf', $response->headers->get('Content-Type'));
    }

    /** @test */
    public function research_director_can_delete_unapproved_list()
    {
        $list = ServiceCreditList::create([
            'title' => 'Draft Service Credit List',
            'report_date' => '2026-11-08',
            'fiscal_year' => 2026,
            'prepared_by' => $this->admin->id,
            'status' => 'pending_approval',
            'proposal_ids' => [$this->proposal1->id],
            'items' => [],
        ]);

        $response = $this->actingAs($this->admin)->delete(route('service_credits.destroy', $list->id));
        $response->assertRedirect(route('service_credits.index'));

        $this->assertDatabaseMissing('service_credit_lists', [
            'id' => $list->id,
        ]);
    }

    /** @test */
    public function researcher_cannot_prepare_or_approve_service_credit_list()
    {
        $response = $this->actingAs($this->researcher)->get(route('service_credits.create'));
        $response->assertStatus(403);

        $list = ServiceCreditList::create([
            'title' => 'Test List',
            'report_date' => '2026-11-08',
            'fiscal_year' => 2026,
            'prepared_by' => $this->admin->id,
            'status' => 'pending_approval',
            'proposal_ids' => [$this->proposal1->id],
            'items' => [],
        ]);

        $responseApprove = $this->actingAs($this->researcher)->post(route('service_credits.approve', $list->id));
        $responseApprove->assertStatus(403);
    }
}
