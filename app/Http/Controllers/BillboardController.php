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
            'location' => 'required|string|max:255',
            'date' => 'required|date',
            'from' => 'required',
            'to' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'visibility' => 'required|in:public,private'
        ]);

        $data = $request->except('image');
        
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('uploads/schedules'), $imageName);
            $data['image'] = 'schedules/' . $imageName;
        }

        Schedule::create($data);

        return redirect()->route('billboard.index')
            ->with('success', 'Schedule created successfully.');
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
            'location' => 'required|string|max:255',
            'date' => 'required|date',
            'from' => 'required',
            'to' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'visibility' => 'required|in:public,private'
        ]);

        $data = $request->except('image');
        
        if ($request->hasFile('image')) {
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
            ->with('success', 'Schedule updated successfully.');
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
            ->with('success', 'Schedule deleted successfully.');
    }
}
