<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Schedule;

/**
 * Billboard: the slides (images, animated GIFs, short videos) at the top of the customer app's home screen.
 * Slides can be dragged into the order they should play and hidden without being deleted.
 */
class BillboardController extends Controller
{
    public function index()
    {
        $schedules = Schedule::ordered()->get();
        $appLimit = Schedule::APP_LIMIT;

        return view('billboard.index', compact('schedules', 'appLimit'));
    }

    public function create()
    {
        return view('billboard.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'date' => 'nullable|date',
            'from' => 'nullable',
            'to' => 'nullable',
            'image' => Schedule::mediaRule(true),
        ], ['image.required' => 'Choose an image, GIF or short video for the slide.']);

        $data = $request->only(['title', 'location', 'date', 'from', 'to']);
        $data['location'] = $data['location'] ?? 'default';
        $data['date'] = $data['date'] ?? date('Y-m-d');
        $data['from'] = $data['from'] ?? '00:00';
        $data['to'] = $data['to'] ?? '23:59';
        $data['visibility'] = $request->boolean('visibility', true) ? 1 : 0;
        $data['sort_order'] = Schedule::firstPosition();
        [$data['image'], $data['media_type']] = Schedule::storeMedia($request->file('image'));

        Schedule::create($data);

        return redirect()->route('billboard.index')
            ->with('success', 'Slide added. It shows first in the app; drag it to change the order.');
    }

    public function edit($id)
    {
        $schedule = Schedule::findOrFail($id);
        return view('billboard.edit', compact('schedule'));
    }

    public function update(Request $request, $id)
    {
        $schedule = Schedule::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'date' => 'nullable|date',
            'from' => 'nullable',
            'to' => 'nullable',
            'image' => Schedule::mediaRule(),
        ]);

        $data = $request->only(['title', 'location', 'date', 'from', 'to']);
        $data['location'] = $data['location'] ?? $schedule->location ?? 'default';
        $data['date'] = $data['date'] ?? $schedule->date ?? date('Y-m-d');
        $data['from'] = $data['from'] ?? $schedule->from ?? '00:00';
        $data['to'] = $data['to'] ?? $schedule->to ?? '23:59';
        $data['visibility'] = $request->boolean('visibility') ? 1 : 0;

        if ($request->hasFile('image')) {
            $schedule->deleteMedia();
            [$data['image'], $data['media_type']] = Schedule::storeMedia($request->file('image'));
        }

        $schedule->update($data);

        return redirect()->route('billboard.index')
            ->with('success', 'Billboard updated successfully.');
    }

    public function destroy($id)
    {
        $schedule = Schedule::findOrFail($id);
        $schedule->deleteMedia();
        $schedule->delete();

        return redirect()->route('billboard.index')
            ->with('success', 'Billboard deleted successfully.');
    }

    /** Drag-and-drop order from the gallery (AJAX): ids in display order. */
    public function reorder(Request $request)
    {
        $request->validate(['ids' => 'required|array', 'ids.*' => 'integer']);
        Schedule::reorder($request->ids);

        return response()->json(['success' => true, 'message' => 'Order saved']);
    }

    /** Show / hide a slide in the app (AJAX). */
    public function toggleVisibility(Request $request, $id)
    {
        $schedule = Schedule::findOrFail($id);
        $schedule->update(['visibility' => $request->boolean('visible') ? 1 : 0]);

        return response()->json(['success' => true, 'visible' => (bool) $schedule->visibility]);
    }
}
