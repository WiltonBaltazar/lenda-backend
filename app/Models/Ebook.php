<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ebook extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'ebook_serie_id',
        'title',
        'slug',
        'cover_image',
        'chapters',
        'file',
        'year',
        'is_free',
        'ebook_serie_id',
        'description',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'cover_image' => 'string',
        'description' => 'string',
        'chapters' => 'integer',
        'year' => 'datetime',
        'is_free' => 'boolean',
        'ebook_serie_id' => 'integer',
    ];

    //one ebook belongs to a serie
    public function ebookSerie()
    {
        return $this->belongsTo(EbookSerie::class);
    }

}
