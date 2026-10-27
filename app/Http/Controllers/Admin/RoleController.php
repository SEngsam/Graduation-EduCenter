<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ability;
use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    private $builtIn = [Role::ROLE_ADMIN, Role::ROLE_TEACHER, Role::ROLE_STUDENT];

    public function __construct()
    {
        $this->middleware('ability:all_roles');
    }

    public function index()
    {
        $roles = Role::withCount('abilities')->paginate(10);

        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
        $abilities = Ability::all();

        return view('admin.roles.create', compact('abilities'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'ability' => 'nullable|array',
            'ability.*' => 'exists:abilities,id',
        ]);

        $role = Role::create(['name' => $request->name]);
        $role->abilities()->attach($request->input('ability', []));

        return redirect()->route('admin.roles.index')->with('success', __('dash.saved_new'));
    }

    public function edit(Role $role)
    {
        $abilities = Ability::all();

        return view('admin.roles.edit', compact('abilities', 'role'));
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'ability' => 'nullable|array',
            'ability.*' => 'exists:abilities,id',
        ]);

        $role->update(['name' => $request->name]);
        $role->abilities()->sync($request->input('ability', []));

        return redirect()->route('admin.roles.index')->with('success', __('dash.saved'));
    }

    public function destroy(Role $role)
    {
        if (in_array($role->id, $this->builtIn)) {
            return back()->with('error', __('dash.role_built_in'));
        }

        if ($role->users()->exists()) {
            return back()->with('error', __('dash.role_in_use'));
        }

        $role->delete();

        return redirect()->route('admin.roles.index')->with('success', __('dash.deleted'));
    }
}
