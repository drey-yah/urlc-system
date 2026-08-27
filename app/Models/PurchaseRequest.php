<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'research_proposal_id',
        'user_id',
        'pr_number',
        'purpose',
        'total_amount',
        'document_path',
        'director_countersigned',
        'director_countersigned_at',
        'finance_approved',
        'finance_approved_at',
        'status',
    ];

    protected $casts = [
        'director_countersigned' => 'boolean',
        'finance_approved' => 'boolean',
        'director_countersigned_at' => 'datetime',
        'finance_approved_at' => 'datetime',
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
