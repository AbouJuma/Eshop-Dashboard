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

    /*
     * Billboard standard: every picture and video is exactly the shape of the app banner,
     * STANDARD_WIDTH x STANDARD_HEIGHT px (1800 x 800, 9:4).
     *   - Pictures (JPG/PNG/WEBP) are saved at exactly 1800 x 800 (as JPG). The portal and the
     *     app crop a picture to that shape before sending it.
     *   - GIFs and videos can't be cropped here, so they must already have the 9:4 shape.
     *   - Videos: up to MAX_VIDEO_SECONDS seconds (checked by the portal and the app).
     */
    const STANDARD_WIDTH = 1800;
    const STANDARD_HEIGHT = 800;
    const MIN_WIDTH = 1200; // smaller looks soft on the banner
    const RATIO_TOLERANCE = 0.03; // 3%
    const MAX_VIDEO_SECONDS = 30;

    /** "1800 x 800 px" */
    public static function standardLabel(): string
    {
        return self::STANDARD_WIDTH.' x '.self::STANDARD_HEIGHT.' px';
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
                if ($value instanceof UploadedFile && ($problem = self::mediaProblem($value))) {
                    $fail($problem);
                }
            },
        ];
    }

    /** Whether width x height has the standard 9:4 shape (within RATIO_TOLERANCE). */
    public static function hasStandardShape(int $width, int $height): bool
    {
        if ($width <= 0 || $height <= 0) {
            return false;
        }
        $standard = self::STANDARD_WIDTH / self::STANDARD_HEIGHT;

        return abs(($width / $height) / $standard - 1) <= self::RATIO_TOLERANCE;
    }

    /** Why an upload doesn't meet the billboard standard, or null when it does. */
    public static function mediaProblem(UploadedFile $file): ?string
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $isVideo = in_array($extension, self::VIDEO_EXTENSIONS);
        if ($isVideo) {
            $size = self::videoSize($file->getRealPath());
            if (! $size) {
                return null; // e.g. WEBM: the portal / app checked it before sending
            }
        } else {
            $info = @getimagesize($file->getRealPath());
            if (! $info) {
                return null; // the mimes rule reports unreadable files
            }
            $size = [$info[0], $info[1]];
        }
        [$width, $height] = $size;
        $fixed = $isVideo || $extension === 'gif';
        $what = $isVideo ? 'video' : ($extension === 'gif' ? 'GIF' : 'picture');
        if (! self::hasStandardShape($width, $height)) {
            return "This {$what} is {$width} x {$height} px. Billboard {$what}s must be ".self::standardLabel()." (9:4 shape)"
                .($fixed ? ' - please export it at that size.' : ' - please crop it to that shape.');
        }
        if ($width < self::MIN_WIDTH) {
            return "This {$what} is only {$width} x {$height} px and would look blurry. Use ".self::standardLabel().'.';
        }

        return null;
    }

    /**
     * Width and height of an MP4 / MOV video as it plays (rotation applied), read from its track
     * header, or null when it can't be read. No ffmpeg needed.
     */
    public static function videoSize(string $path): ?array
    {
        $handle = @fopen($path, 'rb');
        if (! $handle) {
            return null;
        }
        $best = null;
        try {
            $walk = function (int $start, int $end, int $depth) use (&$walk, $handle, &$best) {
                $position = $start;
                while ($position + 8 <= $end && $depth < 6) {
                    fseek($handle, $position);
                    $header = fread($handle, 8);
                    if (strlen($header) < 8) {
                        return;
                    }
                    $size = unpack('N', substr($header, 0, 4))[1];
                    $type = substr($header, 4, 4);
                    $headerLength = 8;
                    if ($size === 1) {
                        $size = unpack('J', fread($handle, 8))[1];
                        $headerLength = 16;
                    } elseif ($size === 0) {
                        $size = $end - $position;
                    }
                    if ($size < $headerLength) {
                        return;
                    }
                    if ($type === 'moov' || $type === 'trak') {
                        $walk($position + $headerLength, min($end, $position + $size), $depth + 1);
                    } elseif ($type === 'tkhd') {
                        fseek($handle, $position + $headerLength);
                        $body = fread($handle, (int) min($size - $headerLength, 120));
                        // version/flags 4, then times + track id + reserved + duration (20 or 32),
                        // reserved 8, layer 2, group 2, volume 2, reserved 2 -> matrix (36), width, height
                        $matrixAt = ord($body[0]) === 1 ? 4 + 32 + 16 : 4 + 20 + 16;
                        if (strlen($body) >= $matrixAt + 44) {
                            $a = unpack('N', substr($body, $matrixAt, 4))[1];
                            $width = unpack('N', substr($body, $matrixAt + 36, 4))[1] >> 16;
                            $height = unpack('N', substr($body, $matrixAt + 40, 4))[1] >> 16;
                            if ($width > 0 && $height > 0) {
                                if ($a === 0) { // rotated 90 / 270 degrees: plays the other way round
                                    [$width, $height] = [$height, $width];
                                }
                                if (! $best || $width * $height > $best[0] * $best[1]) {
                                    $best = [$width, $height];
                                }
                            }
                        }
                    }
                    $position += $size;
                }
            };
            $walk(0, (int) filesize($path), 0);
        } catch (\Throwable $e) {
            $best = null;
        } finally {
            fclose($handle);
        }

        return $best;
    }

    /** Store an upload under public/uploads/schedules. @return array [relative path, media type] */
    public static function storeMedia(UploadedFile $file): array
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $name = time().'_'.substr(md5(uniqid('', true)), 0, 6).'.'.$extension;
        $file->move(public_path('uploads/schedules'), $name);
        if (in_array($extension, self::VIDEO_EXTENSIONS)) {
            return ['schedules/'.$name, 'video'];
        }
        if ($extension !== 'gif' && ($standard = self::toStandardPicture(public_path('uploads/schedules/'.$name), $extension))) {
            if ($standard !== $name) {
                @unlink(public_path('uploads/schedules/'.$name));
            }
            $name = $standard;
        }

        return ['schedules/'.$name, 'image'];
    }

    /**
     * Save a JPG/PNG/WEBP as exactly STANDARD_WIDTH x STANDARD_HEIGHT JPG (centre-trims the tiny
     * shape difference the tolerance allows). Returns the new file name, or null to keep the
     * original (GD missing, unreadable file...).
     */
    public static function toStandardPicture(string $path, string $extension): ?string
    {
        try {
            if (! function_exists('imagecreatetruecolor') || ! ($size = @getimagesize($path))) {
                return null;
            }
            [$width, $height] = $size;
            $source = match ($extension) {
                'jpg', 'jpeg' => @imagecreatefromjpeg($path),
                'png' => @imagecreatefrompng($path),
                'webp' => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($path) : false,
                default => false,
            };
            if (! $source) {
                return null;
            }
            // largest centred 9:4 area of the source
            $standard = self::STANDARD_WIDTH / self::STANDARD_HEIGHT;
            $cropWidth = $width;
            $cropHeight = (int) round($width / $standard);
            if ($cropHeight > $height) {
                $cropHeight = $height;
                $cropWidth = (int) round($height * $standard);
            }
            $x = (int) floor(($width - $cropWidth) / 2);
            $y = (int) floor(($height - $cropHeight) / 2);

            $target = imagecreatetruecolor(self::STANDARD_WIDTH, self::STANDARD_HEIGHT);
            imagefill($target, 0, 0, imagecolorallocate($target, 255, 255, 255)); // transparent areas -> white
            imagecopyresampled($target, $source, 0, 0, $x, $y, self::STANDARD_WIDTH, self::STANDARD_HEIGHT, $cropWidth, $cropHeight);
            $newName = pathinfo($path, PATHINFO_FILENAME).'.jpg';
            $newPath = dirname($path).'/'.$newName;
            $temp = $newPath.'.tmp';
            $saved = imagejpeg($target, $temp, 88);
            imagedestroy($source);
            imagedestroy($target);
            if (! $saved || ! filesize($temp)) {
                @unlink($temp);

                return null;
            }
            rename($temp, $newPath);

            return $newName;
        } catch (\Throwable $e) {
            return null;
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
