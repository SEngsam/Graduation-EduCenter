<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Course;
use App\Models\Role;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class TeacherController extends Controller
{
    public function __construct()
    {
        $this->middleware('ability:all_teachers')->only('index');
        $this->middleware('ability:add_teacher')->only(['create', 'store']);
        $this->middleware('ability:edit_teacher')->only(['edit', 'update']);
        $this->middleware('ability:delete_teacher')->only('destroy');
    }

    public function index(Request $request)
    {
        $categories = Category::all();
        $teachers = Teacher::with('category')
            ->searchByCategory($request->category_name)
            ->orderByDesc('id')
            ->paginate(10);

        return view('admin.teachers.index', compact('teachers', 'categories'));
    }

    public function create()
    {
        $categories = Category::all();

        return view('admin.teachers.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'major' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'email' => 'required|email:rfc,filter|unique:users',
            'password' => 'required|min:8|confirmed',
            'image' => 'required|image|max:2048',
        ]);

        $image = $this->saveImage($request, 'teachers');

        DB::transaction(function () use ($request, $image) {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role_id' => Role::ROLE_TEACHER,
            ]);

            Teacher::create([
                'user_id' => $user->id,
                'name' => $request->name,
                'major' => $request->major,
                'categorie_id' => $request->category_id,
                'image' => $image,
            ]);
        });

        return redirect()->route('admin.teachers.index')->with('success', __('dash.saved_new'));
    }

    public function edit($id)
    {
        $teacher = Teacher::findOrFail($id);
        $categories = Category::all();

        return view('admin.teachers.edit', compact('teacher', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $teacher = Teacher::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'major' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|max:2048',
        ]);

        $teacher->update([
            'name' => $request->name,
            'major' => $request->major,
            'categorie_id' => $request->category_id,
            'image' => $this->saveImage($request, 'teachers', $teacher->image),
        ]);

        return redirect()->route('admin.teachers.index')->with('success', __('dash.saved'));
    }

    public function destroy($id)
    {
        if (Course::where('teacher_id', $id)->exists()) {
            return back()->with('error', __('dash.teacher_in_use'));
        }

        $teacher = Teacher::findOrFail($id);
        $this->deleteImage('teachers', $teacher->image);
        $teacher->delete();

        return redirect()->route('admin.teachers.index')->with('success', __('dash.deleted'));
    }
}
