<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'content',
        'image_path',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function interactions()
    {
        return $this->hasMany(AnnouncementInteraction::class);
    }

    public function comments()
    {
        return $this->hasMany(AnnouncementInteraction::class)->where('type', 'comment');
    }

    public function likes()
    {
        return $this->hasMany(AnnouncementInteraction::class)->where('type', 'like');
    }
}
