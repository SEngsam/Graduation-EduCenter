<?php

namespace App\Http\Controllers\Admin;

use App\Models\News;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Gate;

class NewController extends Controller
{
    public function index()
    {
        Gate::authorize('all_newss');
        $n = News::orderByDesc('id')->paginate(5);
        return view('admin.news.index', compact('n'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
       $n = News::all();
        return view('admin.news.create', compact('n'));
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
            'date' => 'required',
            'auther' => 'required',
            'title' => 'required',
            'description' => 'required',
            'btn_text' => 'required',
            'btn_link' => 'required',
        ]);

        $img = $request->file('image');
       $img_name = rand() . time() . $img->getClientOriginalName();
       $img->move(public_path('uploads/news'), $img_name);

      News::create([
        'image' => $img_name,
       'date' => $request->date,
       'auther' => $request->auther,
        'title' => $request->title,
        'description' => $request->description,
        'btn_text' => $request->btn_text,
        'btn_link' => $request->btn_link,

    ]);

    return redirect()->route('admin.news.index')->with('msg', __('dash.saved_new'))->with('type', 'success');



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

        $n = News::all();
        $new = News::findOrFail($id);
        return view('admin.news.edit', compact('n','new'));
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
            'date' => 'required',
            'auther' => 'required',
            'title' => 'required',
            'description' => 'required',
            'btn_text' => 'required',
            'btn_link' => 'required',
        ]);


        $new = News::findOrFail($id);

        $img_name = $new->image;
        if($request->hasFile('image')) {
           $img_name = rand() . time() . $request->file('image')->getClientOriginalName();
          $request->file('image')->move(public_path('uploads/news'), $img_name);
       }

       $new->update([
        'image' => $img_name,
       'date' => $request->date,
       'auther' => $request->auther,
        'title' => $request->title,
        'description' => $request->description,
        'btn_text' => $request->btn_text,
        'btn_link' => $request->btn_link,


    ]);

    return redirect()->route('admin.news.index')->with('msg', __('dash.saved'))->with('type', 'info');



    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $new = News::findOrFail($id);
        File::delete(public_path('uploads/news/'.$new->image));

        $new->delete();

        return redirect()->route('admin.news.index')->with('msg', __('dash.deleted'))->with('type', 'danger');
    }
}
