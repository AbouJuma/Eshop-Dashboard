<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use Illuminate\Http\Request;

/**
 * Billboard (customer app home slider) from the mobile admin app: post, reorder, hide, delete.
 * Requires the billboard.manage permission.
 */
class BillboardAdminController extends Controller
{
    public function index()
    {
        return response()->json([
            'success' => true,
            'app_limit' => Schedule::APP_LIMIT,
            'data' => Schedule::ordered()->get()->map(fn ($s) => $this->payload($s)),
        ]);
    }

    /** multipart: title, media (image/GIF/video), visible (0/1, default 1) */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'media' => Schedule::mediaRule(true),
            'visible' => 'nullable|boolean',
        ], ['media.max' => 'The file is too large (max 20 MB).']);

        [$path, $type] = Schedule::storeMedia($request->file('media'));
        $schedule = Schedule::create([
            'title' => $request->title,
            'location' => 'default',
            'date' => date('Y-m-d'),
            'from' => '00:00',
            'to' => '23:59',
            'image' => $path,
            'media_type' => $type,
            'visibility' => $request->boolean('visible', true) ? 1 : 0,
            'sort_order' => Schedule::firstPosition(),
        ]);

        return response()->json(['success' => true, 'message' => 'Slide posted. It now plays first.', 'data' => $this->payload($schedule)], 201);
    }

    /** title, visible and/or a replacement media file (multipart, POST) */
    public function update(Request $request, $id)
    {
        $schedule = Schedule::findOrFail($id);
        $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'media' => Schedule::mediaRule(),
            'visible' => 'nullable|boolean',
        ]);

        if ($request->has('title')) {
            $schedule->title = $request->title;
        }
        if ($request->has('visible')) {
            $schedule->visibility = $request->boolean('visible') ? 1 : 0;
        }
        if ($request->hasFile('media')) {
            $schedule->deleteMedia();
            [$schedule->image, $schedule->media_type] = Schedule::storeMedia($request->file('media'));
        }
        $schedule->save();

        return response()->json(['success' => true, 'message' => 'Slide updated', 'data' => $this->payload($schedule)]);
    }

    public function reorder(Request $request)
    {
        $request->validate(['ids' => 'required|array', 'ids.*' => 'integer']);
        Schedule::reorder($request->ids);

        return response()->json(['success' => true, 'message' => 'Order saved']);
    }

    public function destroy($id)
    {
        $schedule = Schedule::findOrFail($id);
        $schedule->deleteMedia();
        $schedule->delete();

        return response()->json(['success' => true, 'message' => 'Slide deleted']);
    }

    private function payload(Schedule $s): array
    {
        return [
            'id' => $s->id,
            'title' => $s->title,
            'media_url' => $s->media_url,
            'media_type' => $s->media_type ?? 'image',
            'visible' => (bool) $s->visibility,
            'sort_order' => (int) $s->sort_order,
            'created_at' => optional($s->created_at)->toIso8601String(),
        ];
    }
}
