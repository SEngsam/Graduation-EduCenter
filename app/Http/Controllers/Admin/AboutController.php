<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\About;
use Illuminate\Http\Request;

class AboutController extends Controller
{
    public function __construct()
    {
        $this->middleware('ability:all_abouts')->only('index');
        $this->middleware('ability:add_about')->only(['create', 'store']);
        $this->middleware('ability:edit_about')->only(['edit', 'update']);
        $this->middleware('ability:delete_about')->only('destroy');
    }

    public function index()
    {
        $abouts = About::orderByDesc('id')->paginate(10);

        return view('admin.abouts.index', compact('abouts'));
    }

    public function create()
    {
        return view('admin.abouts.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'btn_text' => 'required|string|max:255',
            'btn_link' => 'required|string|max:255',
            'image' => 'required|image|max:2048',
        ]);

        About::create([
            'title' => $request->title,
            'description' => $request->description,
            'btn_text' => $request->btn_text,
            'btn_link' => $request->btn_link,
            'image' => $this->saveImage($request, 'abouts'),
        ]);

        return redirect()->route('admin.abouts.index')->with('success', __('dash.saved_new'));
    }

    public function edit($id)
    {
        $about = About::findOrFail($id);

        return view('admin.abouts.edit', compact('about'));
    }

    public function update(Request $request, $id)
    {
        $about = About::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'btn_text' => 'required|string|max:255',
            'btn_link' => 'required|string|max:255',
            'image' => 'nullable|image|max:2048',
        ]);

        $about->update([
            'title' => $request->title,
            'description' => $request->description,
            'btn_text' => $request->btn_text,
            'btn_link' => $request->btn_link,
            'image' => $this->saveImage($request, 'abouts', $about->image),
        ]);

        return redirect()->route('admin.abouts.index')->with('success', __('dash.saved'));
    }

    public function destroy($id)
    {
        $about = About::findOrFail($id);
        $this->deleteImage('abouts', $about->image);
        $about->delete();

        return redirect()->route('admin.abouts.index')->with('success', __('dash.deleted'));
    }
}
