<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReportImage extends Model
{
    protected $fillable = ['report_section_id', 'prompt', 'image_url', 'source', 'order'];

    public function section(): BelongsTo
    {
        return $this->belongsTo(ReportSection::class, 'report_section_id');
    }
}
