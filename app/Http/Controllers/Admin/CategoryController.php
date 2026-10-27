<?php

namespace App\Http\Controllers\Admin;

use App\Support\Notifier;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Role;
use App\Models\Teacher;
use App\Models\User;
use App\Notifications\CourseNotification;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('ability:all_categories')->only('index');
        $this->middleware('ability:add_category')->only(['create', 'store']);
        $this->middleware('ability:edit_category')->only(['edit', 'update']);
        $this->middleware('ability:delete_category')->only('destroy');
    }

    public function index()
    {
        $categories = Category::orderByDesc('id')->paginate(10);

        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name_major' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'image' => 'required|image|max:2048',
        ]);

        Category::create([
            'name_major' => $request->name_major,
            'title' => $request->title,
            'description' => $request->description,
            'image' => $this->saveImage($request, 'categories'),
        ]);

        $staff = User::where('role_id', '!=', Role::ROLE_STUDENT)->where('id', '!=', auth()->id())->get();
        Notifier::send($staff, new CourseNotification('category', ['name' => $request->name_major], null, route('admin.categories.index')));

        return redirect()->route('admin.categories.index')->with('success', __('dash.saved_new'));
    }

    public function edit($id)
    {
        $category = Category::findOrFail($id);

        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $request->validate([
            'name_major' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'image' => 'nullable|image|max:2048',
        ]);

        $category->update([
            'name_major' => $request->name_major,
            'title' => $request->title,
            'description' => $request->description,
            'image' => $this->saveImage($request, 'categories', $category->image),
        ]);

        return redirect()->route('admin.categories.index')->with('success', __('dash.saved'));
    }

    public function destroy($id)
    {
        if (Teacher::where('categorie_id', $id)->exists()) {
            return back()->with('error', __('dash.category_in_use'));
        }

        $category = Category::findOrFail($id);
        $this->deleteImage('categories', $category->image);
        $category->delete();

        return redirect()->route('admin.categories.index')->with('success', __('dash.deleted'));
    }
}
