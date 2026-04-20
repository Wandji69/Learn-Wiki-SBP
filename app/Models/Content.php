<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['topic_id', 'type', 'body', 'order'])]
class Content extends Model
{
    public function topic(): BelongsTo
    {
        return $this->belongsTo(Topic::class);
    }
}
