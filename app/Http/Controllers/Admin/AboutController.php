<?php

namespace App\Http\Controllers\Admin;

use App\Models\About;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;


class AboutController extends Controller
{
    public function index()
    {
        Gate::authorize('all_abouts');
        $abouts = About::orderByDesc('id')->paginate(5);
        return view('admin.abouts.index', compact('abouts'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $abouts = About::all();
        return view('admin.abouts.create', compact('abouts'));
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
            'title' => 'required',
            'description' => 'required',
            'btn_text'=> 'required',
            'btn_link'=> 'required',
        ]);

        $img = $request->file('image');
       $img_name = rand() . time() . $img->getClientOriginalName();
       $img->move(public_path('uploads/abouts'), $img_name);

       About::create([
        'image' => $img_name,
        'title' => $request->title,
        'description' => $request->description,
        'btn_text' => $request->btn_text,
        'btn_link' => $request->btn_link,

    ]);

    return redirect()->route('admin.abouts.index')->with('msg', __('dash.saved_new'))->with('type', 'success');



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

        $abouts = About::all();
        $about= About::findOrFail($id);
        return view('admin.abouts.edit', compact('abouts', 'about'));
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
            //'image' => 'required',
            'title' => 'required',
            'description' => 'required',
            'btn_text'=> 'required',
            'btn_link'=> 'required',
        ]);


        $about= About::findOrFail($id);

        $img_name = $about->image;
        if($request->hasFile('image')) {
           $img_name = rand() . time() . $request->file('image')->getClientOriginalName();
          $request->file('image')->move(public_path('uploads/abouts'), $img_name);
       }

       $about->update([
        'image' => $img_name,
        'title' => $request->title,
        'description' => $request->description,
        'btn_text' => $request->btn_text,
        'btn_link' => $request->btn_link,

    ]);

    return redirect()->route('admin.abouts.index')->with('msg', __('dash.saved'))->with('type', 'info');



    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $about= About::findOrFail($id);

        File::delete(public_path('uploads/abouts/'.$about->image));

        $about->delete();

        return redirect()->route('admin.abouts.index')->with('msg', __('dash.deleted'))->with('type', 'danger');
    }
}
