<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProposalDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'research_proposal_id',
        'document_tag',
        'document_type',
        'phase',
        'version',
        'file_path',
    ];

    public function proposal()
    {
        return $this->belongsTo(ResearchProposal::class, 'research_proposal_id');
    }
}
