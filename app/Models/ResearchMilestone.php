<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResearchMilestone extends Model
{
    use HasFactory;

    protected $fillable = [
        'research_proposal_id',
        'title',
        'description',
        'document_path',
        'status',
    ];

    public function proposal()
    {
        return $this->belongsTo(ResearchProposal::class, 'research_proposal_id');
    }
}
