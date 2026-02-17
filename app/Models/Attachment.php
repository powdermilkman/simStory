<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Attachment extends Model
{
    protected $fillable = [
        'post_id',
        'filename',
        'original_filename',
        'mime_type',
        'file_size',
        'path',
        'display_type',
        'caption',
        'sort_order',
    ];

    public const DISPLAY_INLINE = 'inline';
    public const DISPLAY_ATTACHMENT = 'attachment';
    public const DISPLAY_GALLERY = 'gallery';

    public const DISPLAY_TYPES = [
        self::DISPLAY_INLINE => 'Inline (in post content)',
        self::DISPLAY_ATTACHMENT => 'Attachment (download link)',
        self::DISPLAY_GALLERY => 'Gallery (image gallery)',
    ];

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    public function getUrlAttribute(): string
    {
        return Storage::url($this->path);
    }

    public function isImage(): bool
    {
        return str_starts_with($this->mime_type, 'image/');
    }

    public function shouldDisplayInline(): bool
    {
        return $this->display_type === self::DISPLAY_INLINE && $this->isImage();
    }

    public function shouldDisplayAsGallery(): bool
    {
        return $this->display_type === self::DISPLAY_GALLERY && $this->isImage();
    }

    public function shouldDisplayAsAttachment(): bool
    {
        return $this->display_type === self::DISPLAY_ATTACHMENT || !$this->isImage();
    }

    public function getFileIcon(): string
    {
        return match (true) {
            str_starts_with($this->mime_type, 'image/') => '🖼️',
            str_starts_with($this->mime_type, 'video/') => '🎬',
            str_starts_with($this->mime_type, 'audio/') => '🎵',
            $this->mime_type === 'application/pdf' => '📄',
            str_contains($this->mime_type, 'zip') || str_contains($this->mime_type, 'compressed') => '📦',
            str_contains($this->mime_type, 'text') => '📝',
            default => '📎',
        };
    }

    public function getHumanFileSizeAttribute(): string
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }
}
