<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScheduleAttendee extends Model
{
    use HasFactory;

    protected $fillable = [
        'research_schedule_id',
        'user_id',
        'status', // 'confirmed', 'declined', 'tentative'
        'remarks',
    ];

    public function schedule()
    {
        return $this->belongsTo(ResearchSchedule::class, 'research_schedule_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
