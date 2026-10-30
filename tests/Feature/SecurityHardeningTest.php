<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\ResearchProposal;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class SecurityHardeningTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function http_security_headers_are_present_on_web_responses()
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
    }

    /** @test */
    public function unauthorized_user_cannot_download_another_researchers_document()
    {
        Storage::fake('public');

        // Researcher 1 creates a proposal and document
        $author = User::factory()->create([
            'role' => 'researcher',
            'is_approved' => true,
        ]);

        $filePath = 'documents/confidential_proposal_123.pdf';
        Storage::disk('public')->put($filePath, 'Confidential content');

        $proposal = ResearchProposal::create([
            'user_id' => $author->id,
            'proposal_code' => 'RP-2026-999',
            'title' => 'Secret Defense Research',
            'abstract' => 'Top secret research abstract',
            'document_path' => $filePath,
            'status' => 'pending',
        ]);

        // Researcher 2 attempts to download Researcher 1's document via serveFile
        $attacker = User::factory()->create([
            'role' => 'researcher',
            'is_approved' => true,
        ]);

        $response = $this->actingAs($attacker)->get(route('file.serve', ['path' => $filePath]));

        // Must be rejected with 403 Forbidden
        $response->assertStatus(403);
    }

    /** @test */
    public function author_and_admin_can_download_proposal_document()
    {
        Storage::fake('public');

        $author = User::factory()->create([
            'role' => 'researcher',
            'is_approved' => true,
        ]);

        $filePath = 'documents/authorized_proposal_456.pdf';
        Storage::disk('public')->put($filePath, 'Legitimate content');

        $proposal = ResearchProposal::create([
            'user_id' => $author->id,
            'proposal_code' => 'RP-2026-998',
            'title' => 'Authorized Research',
            'abstract' => 'Abstract for authorized research',
            'document_path' => $filePath,
            'status' => 'pending',
        ]);

        // Author can download
        $authorResponse = $this->actingAs($author)->get(route('file.serve', ['path' => $filePath]));
        $authorResponse->assertStatus(200);

        // Admin can download
        $admin = User::factory()->create([
            'role' => 'admin',
            'is_approved' => true,
        ]);

        $adminResponse = $this->actingAs($admin)->get(route('file.serve', ['path' => $filePath]));
        $adminResponse->assertStatus(200);
    }
}
