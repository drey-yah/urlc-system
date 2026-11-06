<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProposalAnnotation extends Model
{
    use HasFactory;

    protected $fillable = [
        'research_proposal_id',
        'user_id',
        'tagged_user_id',
        'tagged_email',
        'parent_id',
        'chapter_section',
        'highlighted_text',
        'highlight_anchor',
        'comment',
        'is_resolved',
        'resolved_at',
        'resolved_by',
    ];

    protected $casts = [
        'is_resolved' => 'boolean',
        'resolved_at' => 'datetime',
    ];

    public function proposal()
    {
        return $this->belongsTo(ResearchProposal::class, 'research_proposal_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function taggedUser()
    {
        return $this->belongsTo(User::class, 'tagged_user_id');
    }

    public function resolvedByUser()
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    public function parent()
    {
        return $this->belongsTo(ProposalAnnotation::class, 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany(ProposalAnnotation::class, 'parent_id')->with('user')->orderBy('created_at', 'asc');
    }
}
