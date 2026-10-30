<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class BudgetAllocation extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'budget_allocations';

    protected $fillable = [
        'fiscal_year',
        'campus',
        'college',
        'program_name',
        'is_allotment_row',
        'sequence_no',
        'implementation_pct',
        'traveling_pct',
        'traveling_local_gaa',
        'traveling_local_tf',
        'traveling_foreign_tf',
        'implementation_tf',
        'total_allocation',
        'source_of_fund',
        'remarks',
        'created_by',
    ];

    protected $casts = [
        'fiscal_year' => 'integer',
        'is_allotment_row' => 'boolean',
        'sequence_no' => 'integer',
        'implementation_pct' => 'decimal:5',
        'traveling_pct' => 'decimal:5',
        'traveling_local_gaa' => 'decimal:2',
        'traveling_local_tf' => 'decimal:2',
        'traveling_foreign_tf' => 'decimal:2',
        'implementation_tf' => 'decimal:2',
        'total_allocation' => 'decimal:2',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'fiscal_year',
                'campus',
                'college',
                'program_name',
                'total_allocation',
                'source_of_fund',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    protected static function booted()
    {
        static::saving(function ($model) {
            // Auto compute total if not explicitly provided
            if (empty($model->total_allocation) || (float)$model->total_allocation == 0) {
                $sum = (float)$model->traveling_local_gaa +
                       (float)$model->traveling_local_tf +
                       (float)$model->traveling_foreign_tf +
                       (float)$model->implementation_tf;
                if ($sum > 0) {
                    $model->total_allocation = $sum;
                }
            }
        });
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeForYear($query, $year)
    {
        return $query->where('fiscal_year', $year);
    }

    public function scopeForCampus($query, $campus)
    {
        return $query->where('campus', $campus);
    }

    public function scopeForCollege($query, $college)
    {
        return $query->where('college', $college);
    }
}
