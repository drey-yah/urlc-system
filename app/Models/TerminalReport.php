<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TerminalReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'research_proposal_id',
        'user_id',
        'executive_summary',
        'terminal_report_file',
        'full_paper_file',
        'supporting_docs_file',
        'evaluator_score',
        'evaluator_comments',
        'evaluation_form_file',
        'final_report_file',
        'certificate_completion_file',
        'status',
    ];

    public function proposal()
    {
        return $this->belongsTo(ResearchProposal::class, 'research_proposal_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
