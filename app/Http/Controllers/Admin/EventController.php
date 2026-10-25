<?php

namespace App\Http\Controllers\Admin;

use App\Models\Event;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Gate;


class EventController extends Controller
{

    public function index()
    {
        Gate::authorize('all_events');
        $events = Event::orderByDesc('id')->paginate(5);
        return view('admin.events.index', compact('events'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        $events = Event::all();
        return view('admin.events.create', compact('events'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required',
            'location' => 'required',
            'description' => 'required',
        ]);

        $img = $request->file('image');
       $img_name = rand() . time() . $img->getClientOriginalName();
       $img->move(public_path('uploads/events'), $img_name);

       Event::create([
        'image' => $img_name,
        'location' => $request->location,
        'description' => $request->description,

    ]);

    return redirect()->route('admin.events.index')->with('msg', __('dash.saved_new'))->with('type', 'success');



    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

        $events = Event::all();
        $event = Event::findOrFail($id);
        return view('admin.events.edit', compact('events', 'event'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'location' => 'required',
            'description' => 'required',
        ]);


        $event = Event::findOrFail($id);

        $img_name = $event->image;
        if($request->hasFile('image')) {
           $img_name = rand() . time() . $request->file('image')->getClientOriginalName();
          $request->file('image')->move(public_path('uploads/events'), $img_name);
       }

       $event->update([
        'image' => $img_name,
        'location' => $request->location,
        'description' => $request->description,


    ]);

    return redirect()->route('admin.events.index')->with('msg', __('dash.saved'))->with('type', 'info');



    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $event = Event::findOrFail($id);
        File::delete(public_path('uploads/events/'.$event->image));

        $event->delete();

        return redirect()->route('admin.events.index')->with('msg', __('dash.deleted'))->with('type', 'danger');
    }



}
