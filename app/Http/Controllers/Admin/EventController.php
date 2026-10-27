<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function __construct()
    {
        $this->middleware('ability:all_events')->only('index');
        $this->middleware('ability:add_event')->only(['create', 'store']);
        $this->middleware('ability:edit_event')->only(['edit', 'update']);
        $this->middleware('ability:delete_event')->only('destroy');
    }

    public function index()
    {
        $events = Event::orderByDesc('id')->paginate(10);

        return view('admin.events.index', compact('events'));
    }

    public function create()
    {
        return view('admin.events.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'location' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'image' => 'required|image|max:2048',
        ]);

        Event::create([
            'location' => $request->location,
            'description' => $request->description,
            'image' => $this->saveImage($request, 'events'),
        ]);

        return redirect()->route('admin.events.index')->with('success', __('dash.saved_new'));
    }

    public function edit($id)
    {
        $event = Event::findOrFail($id);

        return view('admin.events.edit', compact('event'));
    }

    public function update(Request $request, $id)
    {
        $event = Event::findOrFail($id);

        $request->validate([
            'location' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'image' => 'nullable|image|max:2048',
        ]);

        $event->update([
            'location' => $request->location,
            'description' => $request->description,
            'image' => $this->saveImage($request, 'events', $event->image),
        ]);

        return redirect()->route('admin.events.index')->with('success', __('dash.saved'));
    }

    public function destroy($id)
    {
        $event = Event::findOrFail($id);
        $this->deleteImage('events', $event->image);
        $event->delete();

        return redirect()->route('admin.events.index')->with('success', __('dash.deleted'));
    }
}
