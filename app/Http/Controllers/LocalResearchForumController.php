<?php

namespace App\Http\Controllers;

use App\Models\LocalResearchForum;
use App\Models\LocalForumSubmission;
use App\Models\ResearchProposal;
use App\Models\User;
use App\Notifications\WorkflowStatusNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LocalResearchForumController extends Controller
{
    /**
     * Step 1: Research Director creates a Call for Paper Presentation (Local Research Forum)
     */
    public function createForum(Request $request)
    {
        if (!in_array(auth()->user()->role, ['admin', 'vprei', 'coordinator', 'super_admin'])) {
            return redirect()->back()->with('error', 'Unauthorized access.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'theme' => 'nullable|string|max:255',
            'event_date' => 'nullable|date',
            'venue' => 'nullable|string|max:255',
            'submission_deadline' => 'nullable|date',
            'guidelines' => 'nullable|string',
        ]);

        $forum = LocalResearchForum::create([
            'created_by' => auth()->id(),
            'title' => $request->title,
            'theme' => $request->theme,
            'event_date' => $request->event_date,
            'venue' => $request->venue,
            'submission_deadline' => $request->submission_deadline,
            'guidelines' => $request->guidelines,
            'status' => 'open',
        ]);

        // Notify All College Coordinators & Researchers
        $usersToNotify = User::whereIn('role', ['coordinator', 'researcher', 'dean'])->get();
        foreach ($usersToNotify as $user) {
            $user->notify(new WorkflowStatusNotification(
                null,
                '📢 Call for Local Research Forum Presentations',
                "The Research Director launched '{$forum->title}'. Submit your research paper for presentation by " . ($forum->submission_deadline ? $forum->submission_deadline->format('M d, Y') : 'the deadline') . ".",
                'bi-megaphone-fill',
                'text-primary'
            ));
        }

        return redirect()->back()->with('success', 'Local Research Forum event launched! Call for paper presentations disseminated across all colleges.');
    }

    /**
     * Step 3: Researcher submits paper output to College Research Coordinator
     */
    public function submitPaper(Request $request, $proposalId)
    {
        $proposal = ResearchProposal::findOrFail($proposalId);

        if (auth()->id() !== $proposal->user_id && !$proposal->collaborators->contains(auth()->id()) && !auth()->user()->isAdmin()) {
            return redirect()->back()->with('error', 'Unauthorized access.');
        }

        $request->validate([
            'local_research_forum_id' => 'required|exists:local_research_forums,id',
            'paper_title' => 'required|string|max:255',
            'abstract' => 'required|string',
            'extended_abstract_file' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
            'presentation_file' => 'nullable|file|mimes:pdf,ppt,pptx|max:20480',
        ]);

        $extendedPath = null;
        if ($request->hasFile('extended_abstract_file')) {
            $extendedPath = $request->file('extended_abstract_file')->store('phase5/extended_abstracts', 'public');
        }

        $presPath = null;
        if ($request->hasFile('presentation_file')) {
            $presPath = $request->file('presentation_file')->store('phase5/slides', 'public');
        }

        $submission = LocalForumSubmission::create([
            'local_research_forum_id' => $request->local_research_forum_id,
            'research_proposal_id' => $proposal->id,
            'user_id' => auth()->id(),
            'paper_title' => $request->paper_title,
            'abstract' => $request->abstract,
            'extended_abstract_path' => $extendedPath,
            'presentation_file_path' => $presPath,
            'status' => 'submitted_to_coordinator',
        ]);

        // Notify College Coordinators
        $coordinators = User::where('role', 'coordinator')
            ->where(function($q) use ($proposal) {
                $q->where('department', $proposal->user->department)->orWhereNull('department');
            })->get();

        foreach ($coordinators as $coord) {
            $coord->notify(new WorkflowStatusNotification(
                $proposal,
                '📑 New Local Forum Submission Received',
                "Researcher {$proposal->user->name} submitted '{$request->paper_title}' for endorsement to the Local Research Forum.",
                'bi-file-earmark-arrow-up-fill',
                'text-info'
            ));
        }

        return redirect()->back()->with('success', 'Paper output submitted to College Research Coordinator for endorsement!');
    }

    /**
     * Step 2: College Research Coordinator endorses submission to Research Director
     */
    public function endorseSubmission(Request $request, $id)
    {
        if (!in_array(auth()->user()->role, ['coordinator', 'admin', 'vprei', 'super_admin'])) {
            return redirect()->back()->with('error', 'Unauthorized access.');
        }

        $submission = LocalForumSubmission::findOrFail($id);

        $submission->update([
            'coordinator_endorsed' => DB::raw('true'),
            'coordinator_endorsed_at' => now(),
            'coordinator_id' => auth()->id(),
            'status' => 'endorsed_by_coordinator',
        ]);

        // Notify Research Office / Directors / Admins
        $directors = User::whereIn('role', ['admin', 'vprei', 'super_admin'])->get();
        foreach ($directors as $dir) {
            $dir->notify(new WorkflowStatusNotification(
                $submission->proposal,
                '👍 College Coordinator Endorsed Forum Submission',
                "College Coordinator endorsed paper '{$submission->paper_title}' for acceptance in '{$submission->forum->title}'.",
                'bi-hand-thumbs-up-fill',
                'text-success'
            ));
        }

        return redirect()->back()->with('success', 'Submission officially endorsed to Research Director!');
    }

    /**
     * Step 4: Research Director sends Notice of Acceptance
     */
    public function issueNoticeOfAcceptance(Request $request, $id)
    {
        if (!in_array(auth()->user()->role, ['admin', 'vprei', 'super_admin'])) {
            return redirect()->back()->with('error', 'Unauthorized access.');
        }

        $submission = LocalForumSubmission::findOrFail($id);

        $request->validate([
            'notice_file' => 'nullable|file|mimes:pdf|max:10240',
        ]);

        $noticePath = $submission->notice_of_acceptance_path;
        if ($request->hasFile('notice_file')) {
            $noticePath = $request->file('notice_file')->store('phase5/notices', 'public');
        }

        $submission->update([
            'is_accepted' => DB::raw('true'),
            'accepted_at' => now(),
            'notice_of_acceptance_path' => $noticePath,
            'status' => 'accepted_by_director',
        ]);

        // Notify Researcher
        $submission->user->notify(new WorkflowStatusNotification(
            $submission->proposal,
            '🎉 Notice of Acceptance Issued for Local Forum',
            "Research Director issued a Notice of Acceptance for your paper '{$submission->paper_title}' at the '{$submission->forum->title}'.",
            'bi-award-fill',
            'text-success'
        ));

        return redirect()->back()->with('success', 'Notice of Acceptance issued! Researcher notified to present output.');
    }

    /**
     * Step 5: Researcher uploads Presentation Certificate & Completes Forum Milestone
     */
    public function uploadCertificate(Request $request, $id)
    {
        $submission = LocalForumSubmission::findOrFail($id);

        if (auth()->id() !== $submission->user_id && !auth()->user()->isAdmin()) {
            return redirect()->back()->with('error', 'Unauthorized access.');
        }

        $request->validate([
            'certificate_file' => 'required|file|mimes:pdf,jpg,png|max:10240',
        ]);

        $certPath = $request->file('certificate_file')->store('phase5/certificates', 'public');

        $submission->update([
            'certificate_path' => $certPath,
            'status' => 'presented_and_completed',
        ]);

        return redirect()->back()->with('success', '🎉 Certificate of Presentation uploaded! Local Research Forum milestone completed.');
    }
}
