<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Schedule;

class BillboardController extends Controller
{
    public function index()
    {
        $schedules = Schedule::latest()->get();
        return view('billboard.index', compact('schedules'));
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
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'visibility' => 'nullable|in:public,private'
        ]);

        $data = $request->except('image');
        
        // Set default values for optional fields
        $data['location'] = $data['location'] ?? 'default';
        $data['date'] = $data['date'] ?? date('Y-m-d');
        $data['from'] = $data['from'] ?? '00:00';
        $data['to'] = $data['to'] ?? '23:59';
        $data['visibility'] = $data['visibility'] ?? 'public';
        
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('uploads/schedules'), $imageName);
            $data['image'] = 'schedules/' . $imageName;
        }

        Schedule::create($data);

        return redirect()->route('billboard.index')
            ->with('success', 'Billboard created successfully.');
    }

    public function edit($id)
    {
        $schedule = Schedule::findOrFail($id);
        return view('billboard.edit', compact('schedule'));
    }

    public function update(Request $request, $id)
    {
        $schedule = Schedule::findOrFail($id);

        // Debug: Log incoming request data
        \Log::info('Billboard update request data:', $request->all());

        $request->validate([
            'title' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'date' => 'nullable|date',
            'from' => 'nullable',
            'to' => 'nullable',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'visibility' => 'nullable|in:public,private'
        ]);

        $data = $request->except('image');
        
        // Set default values for optional fields
        $data['location'] = $data['location'] ?? $schedule->location ?? 'default';
        $data['date'] = $data['date'] ?? $schedule->date ?? date('Y-m-d');
        $data['from'] = $data['from'] ?? $schedule->from ?? '00:00';
        $data['to'] = $data['to'] ?? $schedule->to ?? '23:59';
        $data['visibility'] = $data['visibility'] ?? $schedule->visibility ?? 'public';
        
        // Debug: Log processed data
        \Log::info('Billboard update processed data:', $data);
        
        if ($request->hasFile('image')) {
            // Debug: Log file upload
            \Log::info('Image file detected:', [
                'original_name' => $request->file('image')->getClientOriginalName(),
                'size' => $request->file('image')->getSize()
            ]);
            
            // Delete old image
            if ($schedule->image && file_exists(public_path('uploads/' . $schedule->image))) {
                unlink(public_path('uploads/' . $schedule->image));
            }
            
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('uploads/schedules'), $imageName);
            $data['image'] = 'schedules/' . $imageName;
        }

        $schedule->update($data);

        return redirect()->route('billboard.index')
            ->with('success', 'Billboard updated successfully.');
    }

    public function destroy($id)
    {
        $schedule = Schedule::findOrFail($id);
        
        // Delete image if exists
        if ($schedule->image && file_exists(public_path('uploads/' . $schedule->image))) {
            unlink(public_path('uploads/' . $schedule->image));
        }
        
        $schedule->delete();

        return redirect()->route('billboard.index')
            ->with('success', 'Billboard deleted successfully.');
    }
}
