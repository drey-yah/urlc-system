<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnnouncementInteraction extends Model
{
    protected $fillable = [
        'user_id',
        'announcement_id',
        'type',
        'body',
        'parent_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function announcement()
    {
        return $this->belongsTo(Announcement::class);
    }

    public function parent()
    {
        return $this->belongsTo(AnnouncementInteraction::class, 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany(AnnouncementInteraction::class, 'parent_id');
    }
}
