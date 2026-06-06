<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Report extends Model
{
    protected $fillable = ['user_id', 'title', 'subject', 'language', 'outline', 'status', 'error_message', 'progress', 'pdf_path'];

    protected $casts = [
        'outline' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function sections(): HasMany
    {
        return $this->hasMany(ReportSection::class)->orderBy('order');
    }

    public function images(): HasMany
    {
        return $this->hasMany(ReportImage::class, 'report_section_id');
    }
}
