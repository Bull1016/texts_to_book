<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReportSection extends Model
{
    protected $fillable = ['report_id', 'title', 'content', 'order'];

    public function report(): BelongsTo
    {
        return $this->belongsTo(Report::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ReportImage::class)->orderBy('order');
    }
}
