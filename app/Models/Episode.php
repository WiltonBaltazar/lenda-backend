<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Episode extends Model
{
    use HasFactory;

    protected $fillable = [
        'podcast_id',
        'cover_image',
        'title',
        'slug',
        'description',
        'guest',
        'audio_file',
        'release_date',
        'duration',
    ];

    protected $casts = [
        'id' => 'integer',
        'release_date' => 'datetime',
        'podcast_id' => 'integer',
    ];

    public function podcast()
    {
        return $this->belongsTo(Podcast::class);
    }


    public function progress(): MorphMany
    {
        return $this->morphMany(UserProgress::class, 'content');
    }
}
