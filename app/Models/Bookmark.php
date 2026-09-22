<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Bookmark extends Model
{
    protected $fillable = [
        'user_id',
        'url',
        'name',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    /**
     * A short, readable version of the URL for display — strips the
     * scheme and any trailing slash so the card doesn't get cluttered
     * with "https://" on every line.
     */
    public function displayUrl(): string
    {
        return preg_replace('#^https?://#', '', rtrim($this->url, '/'));
    }
}
