<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\ResearchProposal;
use App\Models\InHouseReviewList;
use App\Notifications\WorkflowStatusNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;

class InHouseReviewListTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $vprei;
    protected $reviewer;
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
            'name' => 'Dr. VPREI Executive',
            'email' => 'vprei@antiquespride.edu.ph',
            'role' => 'vprei',
            'is_approved' => true,
        ]);

        $this->reviewer = User::factory()->create([
            'name' => 'Dr. External Panelist',
            'email' => 'panelist@antiquespride.edu.ph',
            'role' => 'reviewer',
            'is_approved' => true,
        ]);

        $this->researcher = User::factory()->create([
            'name' => 'Prof. Maria Santos',
            'email' => 'maria@antiquespride.edu.ph',
            'role' => 'researcher',
            'is_approved' => true,
            'department' => 'CCIS',
            'campus' => 'Main Campus (Sibalom)',
        ]);

        $this->proposal1 = ResearchProposal::create([
            'user_id' => $this->researcher->id,
            'title' => 'Deep Learning for Crop Disease Classification in Western Visayas',
            'proposal_code' => 'PROP-2026-101',
            'abstract' => 'Sample abstract for crop disease AI',
            'research_field' => 'Information Technology',
            'status' => 'accepted_for_in_house_review',
            'reviewer_id' => $this->reviewer->id,
            'total_budget' => 180000.00,
        ]);

        $this->proposal2 = ResearchProposal::create([
            'user_id' => $this->researcher->id,
            'title' => 'Mangrove Reforestation and Biomass Assessment in Culasi',
            'proposal_code' => 'PROP-2026-102',
            'abstract' => 'Sample abstract for mangrove reforestation',
            'research_field' => 'Environmental Science',
            'status' => 'approved_with_revisions',
            'reviewer_id' => $this->reviewer->id,
            'total_budget' => 240000.00,
        ]);
    }

    /** @test */
    public function research_director_can_view_index_and_create_views()
    {
        $response = $this->actingAs($this->admin)->get(route('in_house_reviews.index'));
        $response->assertStatus(200);
        $response->assertSee('In-house Reviewed Research Proposals');

        $responseCreate = $this->actingAs($this->admin)->get(route('in_house_reviews.create'));
        $responseCreate->assertStatus(200);
        $responseCreate->assertSee('Deep Learning for Crop Disease Classification in Western Visayas');
        $responseCreate->assertSee('Transmit to VPREI for Noting');
    }

    /** @test */
    public function research_director_can_prepare_in_house_reviewed_list_and_notify_vprei()
    {
        Notification::fake();

        $response = $this->actingAs($this->admin)->post(route('in_house_reviews.store'), [
            'title' => 'In-house Reviewed Research Proposals - 1st Cycle 2026',
            'report_date' => '2026-11-08',
            'fiscal_year' => 2026,
            'proposal_ids' => [$this->proposal1->id, $this->proposal2->id],
            'items' => [
                $this->proposal1->id => [
                    'panelist' => 'Dr. External Panelist, Prof. Cruz',
                    'remarks' => 'Passed with minor revisions',
                ],
                $this->proposal2->id => [
                    'panelist' => 'Dr. External Panelist',
                    'remarks' => 'Approved unconditionally',
                ],
            ],
            'remarks' => 'Institutional in-house review presentation held at the Conference Hall.',
        ]);

        $list = InHouseReviewList::first();
        $this->assertNotNull($list);
        $this->assertEquals('In-house Reviewed Research Proposals - 1st Cycle 2026', $list->title);
        $this->assertEquals('pending_noting', $list->status);
        $this->assertEquals($this->admin->id, $list->prepared_by);
        $this->assertCount(2, $list->proposal_ids);
        $this->assertCount(2, $list->items);
        $this->assertEquals('Dr. External Panelist, Prof. Cruz', $list->items[0]['panelist']);
        $this->assertEquals('Passed with minor revisions', $list->items[0]['remarks']);

        $response->assertRedirect(route('in_house_reviews.show', $list->id));

        Notification::assertSentTo(
            [$this->vprei],
            WorkflowStatusNotification::class,
            function ($notification) {
                return $notification->title === 'In-house Reviewed Proposals Prepared';
            }
        );
    }

    /** @test */
    public function vprei_can_officially_note_the_in_house_reviewed_list()
    {
        Notification::fake();

        $list = InHouseReviewList::create([
            'title' => 'In-house Reviewed Research Proposals - Academic Year 2026',
            'report_date' => '2026-11-08',
            'fiscal_year' => 2026,
            'prepared_by' => $this->admin->id,
            'status' => 'pending_noting',
            'proposal_ids' => [$this->proposal1->id],
            'items' => [
                [
                    'proposal_id' => $this->proposal1->id,
                    'title' => $this->proposal1->title,
                    'proponents' => 'Prof. Maria Santos',
                    'time_frame' => '12 Months',
                    'college' => 'CCIS',
                    'panelist' => 'Dr. Reviewer A, Dr. Reviewer B',
                    'remarks' => 'Recommended for funding',
                ]
            ],
        ]);

        $response = $this->actingAs($this->vprei)->post(route('in_house_reviews.note', $list->id));

        $response->assertRedirect();

        $list->refresh();
        $this->assertEquals('noted', $list->status);
        $this->assertEquals($this->vprei->id, $list->noted_by);
        $this->assertNotNull($list->noted_at);

        Notification::assertSentTo(
            [$this->admin],
            WorkflowStatusNotification::class,
            function ($notification) {
                return $notification->title === 'In-house Reviewed Proposals Noted';
            }
        );
    }

    /** @test */
    public function users_can_view_preview_and_download_official_pdf()
    {
        $list = InHouseReviewList::create([
            'title' => 'In-house Reviewed Research Proposals - Final Dossier',
            'report_date' => '2026-11-08',
            'fiscal_year' => 2026,
            'prepared_by' => $this->admin->id,
            'noted_by' => $this->vprei->id,
            'noted_at' => now(),
            'status' => 'noted',
            'proposal_ids' => [$this->proposal1->id],
            'items' => [
                [
                    'proposal_id' => $this->proposal1->id,
                    'title' => $this->proposal1->title,
                    'proponents' => 'Prof. Maria Santos',
                    'time_frame' => '12 Months',
                    'college' => 'CCIS',
                    'panelist' => 'Dr. External Panelist',
                    'remarks' => 'Approved by Review Panel',
                ]
            ],
        ]);

        // Web view
        $responseShow = $this->actingAs($this->vprei)->get(route('in_house_reviews.show', $list->id));
        $responseShow->assertStatus(200);
        $responseShow->assertSee('UNIVERSITY OF ANTIQUE');
        $responseShow->assertSee('In-house Reviewed Research Proposals');
        $responseShow->assertSee('Deep Learning for Crop Disease Classification in Western Visayas');
        $responseShow->assertSee('Officially Noted by VPREI');

        // PDF download
        $responsePdf = $this->actingAs($this->admin)->get(route('in_house_reviews.pdf', $list->id));
        $responsePdf->assertStatus(200);
        $this->assertStringContainsString('application/pdf', $responsePdf->headers->get('content-type'));
    }

    /** @test */
    public function researcher_cannot_prepare_or_note_in_house_reviewed_list()
    {
        $response = $this->actingAs($this->researcher)->get(route('in_house_reviews.index'));
        $response->assertStatus(403);

        $responseCreate = $this->actingAs($this->researcher)->get(route('in_house_reviews.create'));
        $responseCreate->assertStatus(403);

        $list = InHouseReviewList::create([
            'title' => 'Test In-House List',
            'report_date' => '2026-11-08',
            'fiscal_year' => 2026,
            'prepared_by' => $this->admin->id,
            'status' => 'pending_noting',
            'proposal_ids' => [$this->proposal1->id],
        ]);

        $responseNote = $this->actingAs($this->researcher)->post(route('in_house_reviews.note', $list->id));
        $responseNote->assertStatus(403);
    }
}
