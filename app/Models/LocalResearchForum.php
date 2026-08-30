<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LocalResearchForum extends Model
{
    use HasFactory;

    protected $fillable = [
        'created_by',
        'title',
        'theme',
        'event_date',
        'venue',
        'submission_deadline',
        'guidelines',
        'status',
    ];

    protected $casts = [
        'event_date' => 'date',
        'submission_deadline' => 'date',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function submissions()
    {
        return $this->hasMany(LocalForumSubmission::class, 'local_research_forum_id');
    }
}
