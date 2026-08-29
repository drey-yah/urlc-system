<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ResearchPresentation;
use App\Models\ResearchProposal;
use App\Models\User;
use App\Notifications\WorkflowStatusNotification;
use Illuminate\Support\Facades\DB;

class ResearchPresentationController extends Controller
{
    /**
     * Step 1: Researcher logs/submits abstract submission details for presentation.
     */
    public function store(Request $request, $proposalId)
    {
        $proposal = ResearchProposal::findOrFail($proposalId);

        if ($proposal->user_id !== auth()->id()) {
            abort(403, 'Unauthorized submission.');
        }

        $request->validate([
            'sponsoring_agency' => 'required|string|max:255',
            'conference_name' => 'required|string|max:255',
            'presentation_type' => 'required|in:oral,poster',
            'presentation_title' => 'required|string|max:255',
            'event_date' => 'nullable|date',
            'venue' => 'nullable|string|max:255',
            'acceptance_letter_file' => 'nullable|file|mimes:pdf,doc,docx,jpg,png|max:20480',
            'presentation_file' => 'nullable|file|mimes:pdf,ppt,pptx|max:51200',
        ]);

        $acceptancePath = null;
        if ($request->hasFile('acceptance_letter_file')) {
            $acceptancePath = $request->file('acceptance_letter_file')->store('phase3/acceptance_letters', config('filesystems.default', 'public'));
        }

        $presentationPath = null;
        if ($request->hasFile('presentation_file')) {
            $presentationPath = $request->file('presentation_file')->store('phase3/presentation_files', config('filesystems.default', 'public'));
        }

        $status = 'abstract_submitted';
        if ($acceptancePath && $presentationPath) {
            $status = 'paper_uploaded';
        } elseif ($acceptancePath) {
            $status = 'acceptance_uploaded';
        }

        $presentation = ResearchPresentation::create([
            'research_proposal_id' => $proposal->id,
            'user_id' => auth()->id(),
            'sponsoring_agency' => $request->sponsoring_agency,
            'conference_name' => $request->conference_name,
            'presentation_type' => $request->presentation_type,
            'presentation_title' => $request->presentation_title,
            'event_date' => $request->event_date,
            'venue' => $request->venue,
            'acceptance_letter_path' => $acceptancePath,
            'presentation_file_path' => $presentationPath,
            'status' => $status,
        ]);

        // Update proposal phase if needed
        $proposal->update(['current_phase' => 3]);

        // Notify Director & VPREI
        $officials = User::whereIn('role', ['admin', 'vprei'])->get();
        foreach ($officials as $off) {
            $off->notify(new WorkflowStatusNotification(
                $proposal,
                'Phase 3: Presentation Abstract Logged',
                "Abstract for '{$request->presentation_title}' submitted to {$request->sponsoring_agency} ({$request->conference_name}).",
                'bi-easel',
                'text-primary'
            ));
        }

        return redirect()->back()->with('success', 'Oral/Poster Presentation details logged successfully in Phase 3 Dissemination!');
    }

    /**
     * Step 2: Upload Sponsoring Agency Acceptance Letter or mark as Rejected.
     */
    public function uploadAcceptance(Request $request, $id)
    {
        $presentation = ResearchPresentation::findOrFail($id);

        if ($presentation->user_id !== auth()->id()) {
            abort(403, 'Unauthorized operation.');
        }

        $request->validate([
            'decision' => 'required|in:accepted,rejected',
            'acceptance_letter_file' => 'nullable|required_if:decision,accepted|file|mimes:pdf,doc,docx,jpg,png|max:20480',
        ]);

        if ($request->decision === 'rejected') {
            $presentation->update([
                'status' => 'agency_rejected',
            ]);

            return redirect()->back()->with('warning', 'Presentation recorded as Rejected by sponsoring agency.');
        }

        $acceptancePath = $presentation->acceptance_letter_path;
        if ($request->hasFile('acceptance_letter_file')) {
            $acceptancePath = $request->file('acceptance_letter_file')->store('phase3/acceptance_letters', config('filesystems.default', 'public'));
        }

        $status = $presentation->presentation_file_path ? 'paper_uploaded' : 'acceptance_uploaded';

        $presentation->update([
            'acceptance_letter_path' => $acceptancePath,
            'status' => $status,
        ]);

        // Notify Research Office
        $directors = User::where('role', 'admin')->get();
        foreach ($directors as $dir) {
            $dir->notify(new WorkflowStatusNotification(
                $presentation->proposal,
                'Presentation Acceptance Letter Uploaded',
                "Letter of Acceptance from {$presentation->sponsoring_agency} uploaded for '{$presentation->presentation_title}'.",
                'bi-file-earmark-check-fill',
                'text-success'
            ));
        }

        return redirect()->back()->with('success', 'Sponsoring Agency Letter of Acceptance uploaded successfully!');
    }

    /**
     * Step 3: Upload Full Paper or Poster presentation slides.
     */
    public function uploadPresentation(Request $request, $id)
    {
        $presentation = ResearchPresentation::findOrFail($id);

        if ($presentation->user_id !== auth()->id()) {
            abort(403, 'Unauthorized operation.');
        }

        $request->validate([
            'presentation_file' => 'required|file|mimes:pdf,ppt,pptx|max:51200',
        ]);

        $presentationPath = $request->file('presentation_file')->store('phase3/presentation_files', config('filesystems.default', 'public'));

        $presentation->update([
            'presentation_file_path' => $presentationPath,
            'status' => 'paper_uploaded',
        ]);

        // Notify Director & VPREI
        $officials = User::whereIn('role', ['admin', 'vprei'])->get();
        foreach ($officials as $off) {
            $off->notify(new WorkflowStatusNotification(
                $presentation->proposal,
                'Presentation Slides/Poster Uploaded',
                "Final presentation slides/poster uploaded for '{$presentation->presentation_title}'. Ready for Presidential Endorsement.",
                'bi-file-earmark-slides-fill',
                'text-primary'
            ));
        }

        return redirect()->back()->with('success', 'Presentation slides / poster deck uploaded successfully!');
    }

    /**
     * Step 4: Research Director / VPREI recommends presentation to the SUC President.
     */
    public function recommendToPresident(Request $request, $id)
    {
        $presentation = ResearchPresentation::findOrFail($id);

        if (!in_array(auth()->user()->role, ['admin', 'vprei', 'president'])) {
            abort(403, 'Only Research Director or VPREI can endorse presentations to the President.');
        }

        $presentation->update([
            'director_recommended' => DB::raw('true'),
            'director_recommended_at' => now(),
            'status' => 'recommended_to_president',
        ]);

        // Notify Researcher & President
        $presidents = User::whereIn('role', ['president', 'admin'])->get();
        foreach ($presidents as $p) {
            $p->notify(new WorkflowStatusNotification(
                $presentation->proposal,
                'Presentation Endorsed to SUC President',
                "Research Director/VPREI recommended presentation '{$presentation->presentation_title}' to the SUC President for authorization.",
                'bi-hand-thumbs-up-fill',
                'text-success'
            ));
        }

        return redirect()->back()->with('success', 'Presentation successfully recommended to the University President!');
    }

    /**
     * Step 5: SUC President (or delegated Admin/VPREI) approves presentation output.
     */
    public function presidentApprove(Request $request, $id)
    {
        $presentation = ResearchPresentation::findOrFail($id);

        if (!in_array(auth()->user()->role, ['admin', 'vprei', 'president'])) {
            abort(403, 'Unauthorized presidential sign-off.');
        }

        $presentation->update([
            'president_approved' => DB::raw('true'),
            'president_approved_at' => now(),
            'status' => 'approved_by_president',
        ]);

        // Notify Researcher
        $presentation->user->notify(new WorkflowStatusNotification(
            $presentation->proposal,
            '🎉 Approved by University President!',
            "The SUC President officially approved your research output presentation '{$presentation->presentation_title}' for {$presentation->sponsoring_agency}.",
            'bi-award-fill',
            'text-success'
        ));

        return redirect()->back()->with('success', 'Oral/Poster presentation approved by SUC President for conference representation!');
    }

    /**
     * Step 6: Researcher presents & uploads Certificate of Presentation to complete.
     */
    public function uploadCertificate(Request $request, $id)
    {
        $presentation = ResearchPresentation::findOrFail($id);

        if ($presentation->user_id !== auth()->id()) {
            abort(403, 'Unauthorized operation.');
        }

        $request->validate([
            'certificate_file' => 'required|file|mimes:pdf,jpg,png,jpeg|max:20480',
        ]);

        $certPath = $request->file('certificate_file')->store('phase3/certificates', config('filesystems.default', 'public'));

        $presentation->update([
            'certificate_path' => $certPath,
            'status' => 'completed',
        ]);

        // Notify Research Office
        $directors = User::where('role', 'admin')->get();
        foreach ($directors as $dir) {
            $dir->notify(new WorkflowStatusNotification(
                $presentation->proposal,
                'Phase 3 Completed: Presentation Certificate Uploaded',
                "Certificate of Presentation uploaded for '{$presentation->presentation_title}' presented at {$presentation->sponsoring_agency}.",
                'bi-check-circle-fill',
                'text-success'
            ));
        }

        return redirect()->back()->with('success', 'Certificate of Presentation uploaded! Phase 3 Dissemination completed successfully.');
    }
}
