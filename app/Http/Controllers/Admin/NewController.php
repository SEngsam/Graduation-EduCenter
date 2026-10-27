<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\News;
use Illuminate\Http\Request;

class NewController extends Controller
{
    public function __construct()
    {
        $this->middleware('ability:all_newss')->only('index');
        $this->middleware('ability:add_news')->only(['create', 'store']);
        $this->middleware('ability:edit_news')->only(['edit', 'update']);
        $this->middleware('ability:delete_news')->only('destroy');
    }

    public function index()
    {
        $n = News::orderByDesc('id')->paginate(10);

        return view('admin.news.index', compact('n'));
    }

    public function create()
    {
        return view('admin.news.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'auther' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'btn_text' => 'required|string|max:255',
            'btn_link' => 'required|string|max:255',
            'image' => 'required|image|max:2048',
        ]);

        News::create([
            'date' => $request->date,
            'auther' => $request->auther,
            'title' => $request->title,
            'description' => $request->description,
            'btn_text' => $request->btn_text,
            'btn_link' => $request->btn_link,
            'image' => $this->saveImage($request, 'news'),
        ]);

        return redirect()->route('admin.news.index')->with('success', __('dash.saved_new'));
    }

    public function edit($id)
    {
        $new = News::findOrFail($id);

        return view('admin.news.edit', compact('new'));
    }

    public function update(Request $request, $id)
    {
        $new = News::findOrFail($id);

        $request->validate([
            'date' => 'required|date',
            'auther' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'btn_text' => 'required|string|max:255',
            'btn_link' => 'required|string|max:255',
            'image' => 'nullable|image|max:2048',
        ]);

        $new->update([
            'date' => $request->date,
            'auther' => $request->auther,
            'title' => $request->title,
            'description' => $request->description,
            'btn_text' => $request->btn_text,
            'btn_link' => $request->btn_link,
            'image' => $this->saveImage($request, 'news', $new->image),
        ]);

        return redirect()->route('admin.news.index')->with('success', __('dash.saved'));
    }

    public function destroy($id)
    {
        $new = News::findOrFail($id);
        $this->deleteImage('news', $new->image);
        $new->delete();

        return redirect()->route('admin.news.index')->with('success', __('dash.deleted'));
    }
}
