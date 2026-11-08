<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResearchAccomplishmentReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'form_code',
        'revision_no',
        'college',
        'quarter',
        'school_year',
        'fiscal_year',
        'report_date',
        'prepared_by',
        'noted_by',
        'noted_at',
        'received_by',
        'received_at',
        'status',
        'section_a',
        'section_b',
        'section_c',
        'section_d',
        'section_e',
        'section_f',
        'section_g',
        'section_h',
        'remarks',
    ];

    protected $casts = [
        'report_date' => 'date',
        'noted_at' => 'datetime',
        'received_at' => 'datetime',
        'fiscal_year' => 'integer',
        'section_a' => 'array',
        'section_b' => 'array',
        'section_c' => 'array',
        'section_d' => 'array',
        'section_e' => 'array',
        'section_f' => 'array',
        'section_g' => 'array',
        'section_h' => 'array',
    ];

    public function preparer()
    {
        return $this->belongsTo(User::class, 'prepared_by');
    }

    public function noter()
    {
        return $this->belongsTo(User::class, 'noted_by');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'received_by');
    }
}
