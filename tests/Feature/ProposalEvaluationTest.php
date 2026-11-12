<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\ResearchProposal;
use App\Models\ProposalEvaluation;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProposalEvaluationTest extends TestCase
{
    use RefreshDatabase;

    protected $author;
    protected $reviewer;
    protected $otherUser;
    protected $proposal;

    protected function setUp(): void
    {
        parent::setUp();

        $this->author = User::factory()->create([
            'name' => 'Dr. Lead Researcher',
            'email' => 'author@antiquespride.edu.ph',
            'role' => 'researcher',
            'is_approved' => true,
            'department' => 'College of Computer Studies',
        ]);

        $this->reviewer = User::factory()->create([
            'name' => 'Dr. Peer Evaluator',
            'email' => 'reviewer@antiquespride.edu.ph',
            'role' => 'reviewer',
            'is_approved' => true,
        ]);

        $this->otherUser = User::factory()->create([
            'name' => 'Unassigned User',
            'email' => 'unassigned@antiquespride.edu.ph',
            'role' => 'researcher',
            'is_approved' => true,
        ]);

        $this->proposal = ResearchProposal::create([
            'user_id' => $this->author->id,
            'title' => 'Web-Based Research Information Tracking System',
            'abstract' => 'Comprehensive research proposal manuscript.',
            'status' => 'pending',
            'current_phase' => 1,
            'proposal_code' => 'RP-2026-EVAL',
            'college_affiliation' => 'College of Computer Studies',
        ]);

        // Assign reviewer
        \DB::table('proposal_assignments')->insert([
            'research_proposal_id' => $this->proposal->id,
            'user_id' => $this->reviewer->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function test_reviewer_can_input_ratings_and_save_resu_fm001_evaluation()
    {
        $response = $this->actingAs($this->reviewer)->postJson(route('proposal.evaluate', $this->proposal->id), [
            'status' => 'approved',
            'score_a' => 14.0, // max 15
            'score_b' => 4.5,  // max 5
            'score_c' => 22.5, // max 25
            'score_d' => 21.0, // max 25
            'score_e' => 26.0, // max 30
            'review_comments' => 'Commendable proposal with clear institutional impact.',
            'review_suggestions' => 'Ensure pilot testing includes all satellite campuses.',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'total_score' => 88.0,
            'meets_threshold' => true,
            'status' => 'approved',
        ]);

        $this->assertDatabaseHas('proposal_evaluations', [
            'research_proposal_id' => $this->proposal->id,
            'user_id' => $this->reviewer->id,
            'score_a' => 14.0,
            'score_b' => 4.5,
            'score_c' => 22.5,
            'score_d' => 21.0,
            'score_e' => 26.0,
            'total_score' => 88.0,
            'meets_threshold' => true,
            'decision' => 'approved',
            'comments' => 'Commendable proposal with clear institutional impact.',
        ]);

        $this->proposal->refresh();
        $this->assertEquals('approved', $this->proposal->status);
        $this->assertEquals($this->reviewer->id, $this->proposal->reviewer_id);
    }

    public function test_evaluation_below_80_percent_flags_threshold_false()
    {
        $response = $this->actingAs($this->reviewer)->postJson(route('proposal.evaluate', $this->proposal->id), [
            'status' => 'revision_required',
            'score_a' => 10.0,
            'score_b' => 3.0,
            'score_c' => 18.0,
            'score_d' => 18.0,
            'score_e' => 20.0,
            'review_comments' => 'Needs further refinement.',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'total_score' => 69.0,
            'meets_threshold' => false,
            'status' => 'revision_required',
        ]);

        $this->assertDatabaseHas('proposal_evaluations', [
            'research_proposal_id' => $this->proposal->id,
            'total_score' => 69.0,
            'meets_threshold' => false,
        ]);
    }

    public function test_author_cannot_evaluate_own_proposal_due_to_coi()
    {
        $response = $this->actingAs($this->author)->postJson(route('proposal.evaluate', $this->proposal->id), [
            'status' => 'approved',
            'score_a' => 15.0,
            'score_b' => 5.0,
            'score_c' => 25.0,
            'score_d' => 25.0,
            'score_e' => 30.0,
        ]);

        $response->assertStatus(403);
    }

    public function test_reviewer_and_author_can_download_official_resu_fm001_pdf()
    {
        // First create evaluation
        ProposalEvaluation::create([
            'research_proposal_id' => $this->proposal->id,
            'user_id' => $this->reviewer->id,
            'score_a' => 14.0,
            'score_b' => 4.5,
            'score_c' => 22.5,
            'score_d' => 21.0,
            'score_e' => 26.0,
            'total_score' => 88.0,
            'meets_threshold' => true,
            'decision' => 'approved',
            'comments' => 'Official evaluation comments.',
            'evaluated_at' => now(),
        ]);

        // Reviewer download
        $revResponse = $this->actingAs($this->reviewer)->get(route('proposal.evaluationPdf', $this->proposal->id));
        $revResponse->assertStatus(200);
        $revResponse->assertHeader('content-type', 'application/pdf');

        // Author download
        $authorResponse = $this->actingAs($this->author)->get(route('proposal.evaluationPdf', $this->proposal->id));
        $authorResponse->assertStatus(200);
        $authorResponse->assertHeader('content-type', 'application/pdf');

        // Unrelated user cannot download
        $otherResponse = $this->actingAs($this->otherUser)->get(route('proposal.evaluationPdf', $this->proposal->id));
        $otherResponse->assertStatus(403);
    }
}
