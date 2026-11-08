<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\ResearchProposal;
use App\Models\ProposalBudgetItem;
use App\Models\InceptionResearchList;
use App\Notifications\WorkflowStatusNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;

class InceptionResearchListTest extends TestCase
{
    use RefreshDatabase;

    protected $recordingStaff;
    protected $admin;
    protected $researcher;
    protected $proposal1;
    protected $proposal2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->recordingStaff = User::factory()->create([
            'name' => 'Maria Recording Staff',
            'email' => 'recording@antiquespride.edu.ph',
            'role' => 'recording_staff',
            'is_approved' => true,
        ]);

        $this->admin = User::factory()->create([
            'name' => 'Dr. Director UA',
            'email' => 'director@antiquespride.edu.ph',
            'role' => 'admin',
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
            'proposal_code' => 'PROP-2026-501',
            'abstract' => 'Inception research project for digital literacy in basic schools.',
            'research_field' => 'Education',
            'college_affiliation' => 'College of Teacher Education',
            'status' => 'approved',
            'total_budget' => 50000.00,
            'current_phase' => 1,
        ]);

        ProposalBudgetItem::create([
            'research_proposal_id' => $this->proposal1->id,
            'category_type' => 'Supplies',
            'category_group' => 'Direct Cost',
            'item_name' => 'Instructional Materials and Supplies',
            'amount' => 30000.00,
        ]);

        ProposalBudgetItem::create([
            'research_proposal_id' => $this->proposal1->id,
            'category_type' => 'Travel',
            'category_group' => 'Direct Cost',
            'item_name' => 'School Visitation Travelling Expenses',
            'amount' => 20000.00,
        ]);

        $this->proposal2 = ResearchProposal::create([
            'user_id' => $this->researcher->id,
            'title' => 'Community-Based Agroforestry in Antique Watersheds',
            'proposal_code' => 'PROP-2026-502',
            'abstract' => 'Watershed sustainability and agroforestry implementation.',
            'research_field' => 'Agriculture',
            'college_affiliation' => 'College of Agriculture and Forestry',
            'status' => 'funds_certified',
            'total_budget' => 80000.00,
            'current_phase' => 2,
        ]);
    }

    /** @test */
    public function recording_staff_can_view_index_and_create_views()
    {
        $response = $this->actingAs($this->recordingStaff)->get(route('inception_researches.index'));
        $response->assertStatus(200);
        $response->assertSee('Research for Inception Dossiers');

        $responseCreate = $this->actingAs($this->recordingStaff)->get(route('inception_researches.create'));
        $responseCreate->assertStatus(200);
        $responseCreate->assertSee('Digital Pedagogies in Rural Schools of Antique');
        $responseCreate->assertSee('Line item Budget (PhP)');
        $responseCreate->assertSee('Supplies');
        $responseCreate->assertSee('Travelling Expenses');
        $responseCreate->assertSee('Submit to Director for Noting');
    }

    /** @test */
    public function recording_staff_can_prepare_inception_research_list_and_notify_director()
    {
        Notification::fake();

        $payload = [
            'title' => 'LIST OF RESEARCH FOR INCEPTION (REVIEW FOR FUNDING AND IMPLEMENTATION) - FY 2026',
            'report_date' => '2026-11-08',
            'fiscal_year' => 2026,
            'proposal_ids' => [$this->proposal1->id, $this->proposal2->id],
            'items' => [
                $this->proposal1->id => [
                    'college' => 'College of Teacher Education',
                    'supplies_budget' => '30,000.00',
                    'travel_budget' => '20,000.00',
                ],
                $this->proposal2->id => [
                    'college' => 'College of Agriculture and Forestry',
                    'supplies_budget' => '48,000.00',
                    'travel_budget' => '32,000.00',
                ],
            ],
            'remarks' => 'Proposals queued for funding allocation and memorandum signing.',
        ];

        $response = $this->actingAs($this->recordingStaff)->post(route('inception_researches.store'), $payload);

        $this->assertDatabaseHas('inception_research_lists', [
            'title' => 'LIST OF RESEARCH FOR INCEPTION (REVIEW FOR FUNDING AND IMPLEMENTATION) - FY 2026',
            'prepared_by' => $this->recordingStaff->id,
            'status' => 'pending_noting',
            'fiscal_year' => 2026,
        ]);

        $list = InceptionResearchList::where('title', 'LIST OF RESEARCH FOR INCEPTION (REVIEW FOR FUNDING AND IMPLEMENTATION) - FY 2026')->first();
        $this->assertNotNull($list);
        $this->assertCount(2, $list->items);
        $this->assertEquals('30,000.00', $list->items[0]['supplies_budget']);
        $this->assertEquals('20,000.00', $list->items[0]['travel_budget']);

        $response->assertRedirect(route('inception_researches.show', $list->id));

        Notification::assertSentTo(
            $this->admin,
            WorkflowStatusNotification::class,
            function ($notification) {
                return str_contains($notification->title, 'List of Research for Inception Prepared');
            }
        );
    }

    /** @test */
    public function director_can_formally_note_the_inception_research_list_and_notify_preparer()
    {
        Notification::fake();

        $list = InceptionResearchList::create([
            'title' => 'LIST OF RESEARCH FOR INCEPTION (REVIEW FOR FUNDING AND IMPLEMENTATION) - FY 2026',
            'report_date' => '2026-11-08',
            'fiscal_year' => 2026,
            'prepared_by' => $this->recordingStaff->id,
            'status' => 'pending_noting',
            'proposal_ids' => [$this->proposal1->id],
            'items' => [
                [
                    'proposal_id' => $this->proposal1->id,
                    'title' => $this->proposal1->title,
                    'authors' => $this->researcher->name,
                    'college' => 'College of Teacher Education',
                    'supplies_budget' => '30,000.00',
                    'travel_budget' => '20,000.00',
                ],
            ],
        ]);

        $response = $this->actingAs($this->admin)->post(route('inception_researches.note', $list->id));
        $response->assertRedirect();

        $list->refresh();
        $this->assertEquals('noted', $list->status);
        $this->assertEquals($this->admin->id, $list->noted_by);
        $this->assertNotNull($list->noted_at);

        Notification::assertSentTo(
            $this->recordingStaff,
            WorkflowStatusNotification::class,
            function ($notification) {
                return str_contains($notification->title, 'List of Research for Inception Noted');
            }
        );
    }

    /** @test */
    public function inception_research_list_pdf_can_be_generated()
    {
        $list = InceptionResearchList::create([
            'title' => 'LIST OF RESEARCH FOR INCEPTION (REVIEW FOR FUNDING AND IMPLEMENTATION) - FY 2026',
            'report_date' => '2026-11-08',
            'fiscal_year' => 2026,
            'prepared_by' => $this->recordingStaff->id,
            'noted_by' => $this->admin->id,
            'noted_at' => now(),
            'status' => 'noted',
            'proposal_ids' => [$this->proposal1->id],
            'items' => [
                [
                    'proposal_id' => $this->proposal1->id,
                    'title' => $this->proposal1->title,
                    'authors' => $this->researcher->name,
                    'college' => 'College of Teacher Education',
                    'supplies_budget' => '30,000.00',
                    'travel_budget' => '20,000.00',
                ],
            ],
        ]);

        $response = $this->actingAs($this->admin)->get(route('inception_researches.pdf', $list->id));
        $response->assertStatus(200);
        $this->assertEquals('application/pdf', $response->headers->get('Content-Type'));
    }

    /** @test */
    public function recording_staff_can_delete_unnoted_inception_list()
    {
        $list = InceptionResearchList::create([
            'title' => 'Draft Inception List',
            'report_date' => '2026-11-08',
            'fiscal_year' => 2026,
            'prepared_by' => $this->recordingStaff->id,
            'status' => 'pending_noting',
            'proposal_ids' => [$this->proposal1->id],
            'items' => [],
        ]);

        $response = $this->actingAs($this->recordingStaff)->delete(route('inception_researches.destroy', $list->id));
        $response->assertRedirect(route('inception_researches.index'));

        $this->assertDatabaseMissing('inception_research_lists', [
            'id' => $list->id,
        ]);
    }
}
