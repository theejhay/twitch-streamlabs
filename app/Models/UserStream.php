<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UserStream extends Model
{
    protected $fillable = [
        'stream_id',
        'user_id',
    ];
    public $timestamps = false;

    public function tags(): HasMany
    {
        return $this->hasMany(Tag::class);
    }
}
