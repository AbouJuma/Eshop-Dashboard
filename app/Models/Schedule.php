<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;

/**
 * Billboard slide on the customer app's home screen (image, animated GIF or short video).
 * visibility: 1 = shown in the app, 0 = hidden. sort_order: lowest shows first.
 */
class Schedule extends Model
{
    use HasFactory;

    /** Images (incl. animated GIF) and short videos. */
    const MEDIA_MIMES = 'jpeg,png,jpg,gif,webp,mp4,webm,mov';
    const VIDEO_EXTENSIONS = ['mp4', 'webm', 'mov'];
    const MAX_UPLOAD_KB = 20480; // 20 MB

    /** Most slides the app shows. */
    const APP_LIMIT = 10;

    protected $fillable = [
        'title',
        'location',
        'date',
        'from',
        'image',
        'media_type',
        'visibility',
        'to',
        'sort_order',
    ];

    protected $casts = ['visibility' => 'integer', 'sort_order' => 'integer'];

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderByDesc('id');
    }

    public function scopeVisible($query)
    {
        return $query->where('visibility', 1);
    }

    public function getMediaUrlAttribute(): ?string
    {
        return $this->image ? url('/uploads/'.$this->image) : null;
    }

    public function isVideo(): bool
    {
        return $this->media_type === 'video';
    }

    /** Validation for the uploaded file. */
    public static function mediaRule(bool $required = false): string
    {
        return ($required ? 'required' : 'nullable').'|file|mimes:'.self::MEDIA_MIMES.'|max:'.self::MAX_UPLOAD_KB;
    }

    /** Store an upload under public/uploads/schedules. @return array [relative path, media type] */
    public static function storeMedia(UploadedFile $file): array
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $name = time().'_'.substr(md5(uniqid('', true)), 0, 6).'.'.$extension;
        $file->move(public_path('uploads/schedules'), $name);

        return ['schedules/'.$name, in_array($extension, self::VIDEO_EXTENSIONS) ? 'video' : 'image'];
    }

    public function deleteMedia(): void
    {
        if ($this->image && file_exists(public_path('uploads/'.$this->image))) {
            @unlink(public_path('uploads/'.$this->image));
        }
    }

    /** New slides go first; they can be dragged elsewhere afterwards. */
    public static function firstPosition(): int
    {
        return ((int) self::min('sort_order')) - 1;
    }

    /** Save a new order: ids in display order. Unknown ids are ignored. */
    public static function reorder(array $ids): void
    {
        $position = 0;
        foreach ($ids as $id) {
            self::where('id', (int) $id)->update(['sort_order' => $position++]);
        }
    }
}
