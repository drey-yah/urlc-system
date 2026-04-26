<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResearchProposal extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'abstract',
        'research_field',
        'budget_requested',
        'budget_spent',
        'status',
        'review_comments',
        'review_suggestions',
        'document_path',
    ];

    //Relationship: Proposal belongs to a User (Researcher)
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}