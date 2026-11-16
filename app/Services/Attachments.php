<?php

namespace App\Services;

use App\Models\MediaAttachment;
use Illuminate\Http\Request;

/**
 * Reference photos / videos API shared by car service records and roadside jobs (customer and staff).
 * Every method returns [payload, http status].
 */
class Attachments
{
    public static function list(string $kind, int $id, int $viewerId, bool $staff): array
    {
        $items = MediaAttachment::forItem($kind, $id)->get()
            ->map(fn ($a) => $a->toApiArray($viewerId, $staff))->values();

        return [['success' => true, 'data' => $items, 'max_upload_mb' => (int) (MediaAttachment::MAX_UPLOAD_KB / 1024)], 200];
    }

    public static function upload(string $kind, int $id, int $userId, bool $staff, Request $request): array
    {
        $request->validate(MediaAttachment::rules(), [
            'file.max' => 'The file is too large (max '.(int) (MediaAttachment::MAX_UPLOAD_KB / 1024).' MB).',
            'file.mimes' => 'Only photos (jpg, png, gif, webp, heic) and videos (mp4, mov, webm, 3gp) can be attached.',
            'file.uploaded' => 'The upload failed. The file may be larger than the server allows.',
        ]);

        if (MediaAttachment::countFor($kind, $id) >= MediaAttachment::MAX_PER_ITEM) {
            return [['success' => false, 'message' => 'This item already has '.MediaAttachment::MAX_PER_ITEM.' files. Delete some first.'], 422];
        }

        $attachment = MediaAttachment::store($request->file('file'), $kind, $id, $userId, $staff ? 'staff' : 'customer', $request->input('caption'));

        return [['success' => true, 'message' => 'File attached', 'data' => $attachment->load('uploader')->toApiArray($userId, $staff)], 201];
    }

    public static function delete(string $kind, int $id, $attachmentId, int $userId, bool $staff): array
    {
        $attachment = MediaAttachment::where('attachable_type', $kind)->where('attachable_id', $id)->findOrFail($attachmentId);

        if (! $staff && $attachment->uploaded_by != $userId) {
            return [['success' => false, 'message' => 'You can only delete files you added.'], 422];
        }
        $attachment->remove();

        return [['success' => true, 'message' => 'File deleted'], 200];
    }
}
