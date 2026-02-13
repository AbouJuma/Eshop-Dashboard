<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;

class ServicesController extends Controller
{
    public function index()
    {
        $services = Service::latest()->get();
        return view('services.index', compact('services'));
    }

    public function create()
    {
        return view('services.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:active,inactive'
        ]);

        $data = $request->except('image');
        $data['status'] = $request->status == 'active' ? 1 : 0;
        
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('uploads/services'), $imageName);
            $data['image'] = 'services/' . $imageName;
        }

        // Ensure we don't have any ID field when creating
        unset($data['id']);
        unset($data['created_at']);
        unset($data['updated_at']);

        // Create service with explicit null check
        $service = new Service();
        $service->title = $data['title'];
        $service->description = $data['description'] ?? null;
        $service->status = $data['status'];
        $service->image = $data['image'] ?? null;
        $service->save();

        return redirect()->route('services.index')
            ->with('success', 'Service created successfully.');
    }

    public function edit($id)
    {
        $service = Service::findOrFail($id);
        return view('services.edit', compact('service'));
    }

    public function update(Request $request, $id)
    {
        $service = Service::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:active,inactive'
        ]);

        $data = $request->except('image');
        $data['status'] = $request->status == 'active' ? 1 : 0;
        
        if ($request->hasFile('image')) {
            // Delete old image
            if ($service->image && file_exists(public_path('uploads/' . $service->image))) {
                unlink(public_path('uploads/' . $service->image));
            }
            
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('uploads/services'), $imageName);
            $data['image'] = 'services/' . $imageName;
        }

        $service->update($data);

        return redirect()->route('services.index')
            ->with('success', 'Service updated successfully.');
    }

    public function destroy($id)
    {
        $service = Service::findOrFail($id);
        
        // Delete image if exists
        if ($service->image && file_exists(public_path('uploads/' . $service->image))) {
            unlink(public_path('uploads/' . $service->image));
        }
        
        $service->delete();

        return redirect()->route('services.index')
            ->with('success', 'Service deleted successfully.');
    }
}
