<?php

namespace App\Http\Controllers;

use App\Models\ResearchPublication;
use App\Models\ResearchProposal;
use App\Models\User;
use App\Notifications\WorkflowStatusNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ResearchPublicationController extends Controller
{
    /**
     * Step 1: Researcher submits Letter of Intent to Publish in Refereed Journal
     */
    public function storeIntent(Request $request, $proposalId)
    {
        $proposal = ResearchProposal::findOrFail($proposalId);

        // Ensure user is author/collaborator
        if (auth()->id() !== $proposal->user_id && !$proposal->collaborators->contains(auth()->id()) && !auth()->user()->isAdmin()) {
            return redirect()->back()->with('error', 'Unauthorized access.');
        }

        $request->validate([
            'intent_letter' => 'required|file|mimes:pdf,doc,docx|max:10240',
            'journal_title' => 'required|string|max:255',
            'issn_number' => 'nullable|string|max:100',
            'indexing_agency' => 'required|string|max:255',
        ]);

        $intentPath = $request->file('intent_letter')->store('phase4/intents', 'public');

        $publication = ResearchPublication::create([
            'research_proposal_id' => $proposal->id,
            'user_id' => auth()->id(),
            'intent_letter_path' => $intentPath,
            'journal_title' => $request->journal_title,
            'issn_number' => $request->issn_number,
            'indexing_agency' => $request->indexing_agency,
            'status' => 'intent_submitted',
        ]);

        // Notify Research Office / VPREI / Admins
        $execUsers = User::whereIn('role', ['vprei', 'admin', 'coordinator'])->get();
        foreach ($execUsers as $exec) {
            $exec->notify(new WorkflowStatusNotification(
                $proposal,
                '📄 Publication Letter of Intent Submitted',
                "Researcher {$proposal->user->name} submitted a Letter of Intent to publish '{$proposal->title}' in '{$request->journal_title}'.",
                'bi-journal-arrow-up',
                'text-primary'
            ));
        }

        return redirect()->back()->with('success', 'Letter of Intent to Publish successfully logged! Sent for IP Screening and VPREI Review.');
    }

    /**
     * Step 2: VPREI / Research Director / IEDC IP Screening & Approval
     */
    public function screenIp(Request $request, $id)
    {
        if (!in_array(auth()->user()->role, ['vprei', 'admin', 'coordinator'])) {
            return redirect()->back()->with('error', 'Unauthorized access.');
        }

        $publication = ResearchPublication::findOrFail($id);

        $request->validate([
            'has_ip_potential' => 'required|boolean',
            'ip_notes' => 'nullable|string',
        ]);

        $hasIp = (bool)$request->has_ip_potential;

        if ($hasIp) {
            $publication->update([
                'has_ip_potential' => DB::raw('true'),
                'ip_notes' => $request->ip_notes,
                'status' => 'ip_registration_required',
            ]);

            $publication->user->notify(new WorkflowStatusNotification(
                $publication->proposal,
                '💡 IP Potential Detected',
                "Your publication intent for '{$publication->journal_title}' was flagged for Intellectual Property (IP) potential. Please submit IP registration evidence.",
                'bi-lightbulb-fill',
                'text-warning'
            ));

            return redirect()->back()->with('warning', 'Publication flagged for IP potential. Researcher notified to submit IP registration proof.');
        } else {
            $publication->update([
                'has_ip_potential' => DB::raw('false'),
                'ip_notes' => $request->ip_notes,
                'ip_cleared' => DB::raw('true'),
                'ip_cleared_at' => now(),
                'vprei_approved' => DB::raw('true'),
                'vprei_approved_at' => now(),
                'status' => 'approved_for_publication',
            ]);

            $publication->user->notify(new WorkflowStatusNotification(
                $publication->proposal,
                '✅ Approved for Journal Publication',
                "VPREI & Research Office officially approved your publication in '{$publication->journal_title}'. You may now submit your manuscript.",
                'bi-check-circle-fill',
                'text-success'
            ));

            return redirect()->back()->with('success', 'No IP conflicts found. Publication officially approved by VPREI!');
        }
    }

    /**
     * Step 2b: Researcher uploads IP Registration Evidence
     */
    public function uploadIpRegistration(Request $request, $id)
    {
        $publication = ResearchPublication::findOrFail($id);

        if (auth()->id() !== $publication->user_id && !auth()->user()->isAdmin()) {
            return redirect()->back()->with('error', 'Unauthorized access.');
        }

        $request->validate([
            'ip_registration_file' => 'required|file|mimes:pdf,doc,docx,jpg,png|max:10240',
        ]);

        $ipProofPath = $request->file('ip_registration_file')->store('phase4/ip_proofs', 'public');

        $publication->update([
            'ip_registration_file_path' => $ipProofPath,
            'ip_cleared' => DB::raw('true'),
            'ip_cleared_at' => now(),
            'vprei_approved' => DB::raw('true'),
            'vprei_approved_at' => now(),
            'status' => 'approved_for_publication',
        ]);

        return redirect()->back()->with('success', 'IP Registration proof uploaded! Paper officially cleared for publication submission.');
    }

    /**
     * Step 4: Researcher logs Journal Submission Proof
     */
    public function logJournalSubmission(Request $request, $id)
    {
        $publication = ResearchPublication::findOrFail($id);

        if (auth()->id() !== $publication->user_id && !auth()->user()->isAdmin()) {
            return redirect()->back()->with('error', 'Unauthorized access.');
        }

        $request->validate([
            'submission_proof' => 'required|file|mimes:pdf,doc,docx,jpg,png|max:10240',
        ]);

        $subProofPath = $request->file('submission_proof')->store('phase4/submissions', 'public');

        $publication->update([
            'submission_proof_path' => $subProofPath,
            'status' => 'submitted_to_journal',
        ]);

        return redirect()->back()->with('success', 'Journal submission evidence logged successfully!');
    }

    /**
     * Step 5: Researcher uploads Published Copy & DOI to Archive
     */
    public function archivePublishedCopy(Request $request, $id)
    {
        $publication = ResearchPublication::findOrFail($id);

        if (auth()->id() !== $publication->user_id && !auth()->user()->isAdmin()) {
            return redirect()->back()->with('error', 'Unauthorized access.');
        }

        $request->validate([
            'published_copy' => 'required|file|mimes:pdf|max:15360',
            'doi_link' => 'nullable|url|max:255',
        ]);

        $pubCopyPath = $request->file('published_copy')->store('phase4/published', 'public');

        $publication->update([
            'published_copy_path' => $pubCopyPath,
            'doi_link' => $request->doi_link,
            'status' => 'published_and_archived',
        ]);

        // Advance proposal phase if needed
        $publication->proposal->update([
            'status' => 'completed',
        ]);

        // Notify All Executive Stakeholders
        $execUsers = User::whereIn('role', ['vprei', 'admin', 'president', 'coordinator'])->get();
        foreach ($execUsers as $exec) {
            $exec->notify(new WorkflowStatusNotification(
                $publication->proposal,
                '🎉 Research Paper Published & Archived!',
                "The research '{$publication->proposal->title}' has been officially published in '{$publication->journal_title}' and archived in the University Repository.",
                'bi-journal-check',
                'text-success'
            ));
        }

        return redirect()->back()->with('success', '🎉 Congratulations! Published journal copy archived in the University Research Repository!');
    }
}
