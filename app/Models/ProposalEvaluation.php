<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProposalEvaluation extends Model
{
    use HasFactory;

    protected $fillable = [
        'research_proposal_id',
        'user_id',
        'score_a',
        'score_b',
        'score_c',
        'score_d',
        'score_e',
        'total_score',
        'meets_threshold',
        'decision',
        'comments',
        'suggestions',
        'document_path',
        'evaluated_at',
    ];

    protected $casts = [
        'score_a' => 'float',
        'score_b' => 'float',
        'score_c' => 'float',
        'score_d' => 'float',
        'score_e' => 'float',
        'total_score' => 'float',
        'meets_threshold' => 'boolean',
        'evaluated_at' => 'datetime',
    ];

    public function proposal()
    {
        return $this->belongsTo(ResearchProposal::class, 'research_proposal_id');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
