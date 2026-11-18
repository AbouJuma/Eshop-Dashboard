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
    public static function mediaRule(bool $required = false): array
    {
        return [
            $required ? 'required' : 'nullable',
            'file',
            'mimes:'.self::MEDIA_MIMES,
            'max:'.self::MAX_UPLOAD_KB,
            function ($attribute, $value, $fail) {
                if ($value instanceof UploadedFile && ($problem = self::imageProblem($value))) {
                    $fail($problem);
                }
            },
        ];
    }

    /*
     * Billboard standard (the app banner is 2:1, so these look sharp on every phone):
     *  - Pictures: best 1600 x 800 px (2:1); at least MIN_IMAGE_LONG_SIDE px on the long side.
     *    Big pictures are scaled down to OPTIMIZE_MAX_EDGE px on upload so the app loads them fast.
     *  - Videos: best 1920 x 1080 (landscape 16:9), up to MAX_VIDEO_SECONDS seconds.
     *  - Portrait / square media is still accepted: the app shows it whole over a blurred backdrop.
     */
    const MIN_IMAGE_LONG_SIDE = 800;
    const OPTIMIZE_MAX_EDGE = 1920;
    const MAX_VIDEO_SECONDS = 30;

    /** Why an uploaded picture is below the standard, or null when it is fine (videos are not checked here). */
    public static function imageProblem(UploadedFile $file): ?string
    {
        if (in_array(strtolower($file->getClientOriginalExtension()), self::VIDEO_EXTENSIONS)) {
            return null;
        }
        $size = @getimagesize($file->getRealPath());
        if (! $size) {
            return null; // the mimes rule reports unreadable files
        }
        [$width, $height] = $size;
        if (max($width, $height) < self::MIN_IMAGE_LONG_SIDE) {
            return "This picture is too small ({$width} x {$height} px) and would look blurry. "
                .'Use at least '.self::MIN_IMAGE_LONG_SIDE.' px on the longest side - 1600 x 800 px is best.';
        }

        return null;
    }

    /** Store an upload under public/uploads/schedules. @return array [relative path, media type] */
    public static function storeMedia(UploadedFile $file): array
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $name = time().'_'.substr(md5(uniqid('', true)), 0, 6).'.'.$extension;
        $file->move(public_path('uploads/schedules'), $name);
        $isVideo = in_array($extension, self::VIDEO_EXTENSIONS);
        if (! $isVideo) {
            self::optimizeImage(public_path('uploads/schedules/'.$name), $extension);
        }

        return ['schedules/'.$name, $isVideo ? 'video' : 'image'];
    }

    /**
     * Scale a large JPG/PNG/WEBP down to OPTIMIZE_MAX_EDGE px (keeps the shape) so slides load fast.
     * GIFs are left alone (resizing would drop the animation). Any failure keeps the original file.
     */
    public static function optimizeImage(string $path, string $extension): void
    {
        try {
            if (! function_exists('imagecreatetruecolor') || ! ($size = @getimagesize($path))) {
                return;
            }
            [$width, $height] = $size;
            $longest = max($width, $height);
            if ($longest <= self::OPTIMIZE_MAX_EDGE) {
                return;
            }
            $source = match ($extension) {
                'jpg', 'jpeg' => @imagecreatefromjpeg($path),
                'png' => @imagecreatefrompng($path),
                'webp' => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($path) : false,
                default => false,
            };
            if (! $source) {
                return;
            }
            $scale = self::OPTIMIZE_MAX_EDGE / $longest;
            $newWidth = max(1, (int) round($width * $scale));
            $newHeight = max(1, (int) round($height * $scale));
            $target = imagecreatetruecolor($newWidth, $newHeight);
            imagealphablending($target, false);
            imagesavealpha($target, true);
            imagecopyresampled($target, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

            $temp = $path.'.tmp';
            $saved = match ($extension) {
                'jpg', 'jpeg' => imagejpeg($target, $temp, 85),
                'png' => imagepng($target, $temp, 8),
                'webp' => imagewebp($target, $temp, 85),
            };
            imagedestroy($source);
            imagedestroy($target);
            if ($saved && filesize($temp) > 0) {
                rename($temp, $path);
            } else {
                @unlink($temp);
            }
        } catch (\Throwable $e) {
            @unlink($path.'.tmp');
        }
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
