<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProposalBudgetItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'research_proposal_id',
        'category_type',
        'category_group',
        'item_name',
        'funding_agency',
        'equivalent_teaching_unit',
        'existing_resources',
        'amount',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function proposal()
    {
        return $this->belongsTo(ResearchProposal::class, 'research_proposal_id');
    }
}
