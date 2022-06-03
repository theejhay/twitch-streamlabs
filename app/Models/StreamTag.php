<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StreamTag extends Model
{
    protected $fillable = [
        'stream_id',
        'tag_id',
    ];
    public $timestamps = false;

    public function tag(): BelongsTo
    {
        return $this->belongsTo(Tag::class, 'tag_id', 'tag_id');
    }
}
