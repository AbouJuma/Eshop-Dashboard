<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;

/**
 * Reference photo or video attached to a car service record or a roadside job.
 * Files live in public/uploads/attachments/<kind>/<id>/.
 */
class MediaAttachment extends Model
{
    protected $table = 'media_attachments';

    protected $guarded = ['id'];

    protected $casts = ['size' => 'integer'];

    const IMAGE_EXTENSIONS = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'heic', 'heif'];
    const VIDEO_EXTENSIONS = ['mp4', 'mov', 'webm', '3gp', 'mkv'];
    const MAX_UPLOAD_KB = 51200; // 50 MB
    const MAX_PER_ITEM = 30;

    /** Short names used in URLs/folders => model class. */
    const KINDS = [
        'car_maintenance' => CarMaintenance::class,
        'service_request' => ServiceRequest::class,
    ];

    public static function rules(): array
    {
        return [
            'file' => 'required|file|max:'.self::MAX_UPLOAD_KB.'|mimes:'.implode(',', array_merge(self::IMAGE_EXTENSIONS, self::VIDEO_EXTENSIONS)),
            'caption' => 'nullable|string|max:255',
        ];
    }

    public function uploader()
    {
        return $this->belongsTo(\App\User::class, 'uploaded_by');
    }

    public static function forItem(string $kind, int $id)
    {
        return self::with('uploader')
            ->where('attachable_type', $kind)
            ->where('attachable_id', $id)
            ->orderBy('id');
    }

    public static function countFor(string $kind, int $id): int
    {
        return self::where('attachable_type', $kind)->where('attachable_id', $id)->count();
    }

    public static function store(UploadedFile $file, string $kind, int $id, ?int $userId, string $uploaderType, ?string $caption = null): self
    {
        $extension = strtolower($file->getClientOriginalExtension() ?: $file->guessExtension() ?: 'bin');
        $mime = $file->getMimeType();
        $size = $file->getSize();
        $folder = "attachments/{$kind}/{$id}";
        $name = date('Ymd_His').'_'.substr(md5(uniqid('', true)), 0, 8).'.'.$extension;
        $file->move(public_path('uploads/'.$folder), $name);

        return self::create([
            'attachable_type' => $kind,
            'attachable_id' => $id,
            'file_path' => $folder.'/'.$name,
            'media_type' => in_array($extension, self::VIDEO_EXTENSIONS) || str_starts_with((string) $mime, 'video/') ? 'video' : 'image',
            'mime' => $mime,
            'size' => $size ?: 0,
            'caption' => $caption ?: null,
            'uploaded_by' => $userId,
            'uploader_type' => $uploaderType,
        ]);
    }

    public static function deleteAllFor(string $kind, int $id): void
    {
        self::where('attachable_type', $kind)->where('attachable_id', $id)->get()->each->remove();
    }

    public function remove(): void
    {
        $path = public_path('uploads/'.$this->file_path);
        if (is_file($path)) {
            @unlink($path);
        }
        $this->delete();
    }

    public function getUrlAttribute(): string
    {
        return url('/uploads/'.$this->file_path);
    }

    public function toApiArray(?int $viewerId = null, bool $viewerIsStaff = false): array
    {
        $uploader = $this->uploader;
        $name = $uploader ? (trim($uploader->first_name.' '.$uploader->last_name) ?: $uploader->username) : null;

        return [
            'id' => $this->id,
            'url' => $this->url,
            'media_type' => $this->media_type,
            'mime' => $this->mime,
            'size' => $this->size,
            'caption' => $this->caption,
            'uploaded_by' => $name,
            'uploader_type' => $this->uploader_type,
            'created_at' => optional($this->created_at)->format('Y-m-d H:i'),
            // staff may remove any file on items they manage; customers only their own
            'can_delete' => $viewerIsStaff || ($viewerId && $viewerId == $this->uploaded_by),
        ];
    }
}
