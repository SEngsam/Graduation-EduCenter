<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('ability:all_users')->only('index');
        $this->middleware('ability:add_user')->only(['create', 'store']);
        $this->middleware('ability:edit_user')->only('update');
        $this->middleware('ability:delete_user')->only('destroy');
    }

    public function index(Request $request)
    {
        $users = User::with('role')
            ->when($request->role_id, function ($query, $roleId) {
                $query->where('role_id', $roleId);
            })
            ->when($request->q, function ($query, $q) {
                $query->where(function ($query) use ($q) {
                    $query->where('name', 'like', '%' . $q . '%')
                        ->orWhere('email', 'like', '%' . $q . '%');
                });
            })
            ->orderByDesc('id')
            ->paginate(10);

        $roles = Role::all();

        return view('admin.users.index', compact('users', 'roles'));
    }

    public function create()
    {
        $roles = Role::all();

        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email:rfc,filter|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role_id' => 'required|exists:roles,id',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $request->role_id,
        ]);

        return redirect()->route('admin.users.index')->with('success', __('dash.saved_new'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email:rfc,filter|max:255|unique:users,email,' . $user->id,
            'role_id' => 'required|exists:roles,id',
        ]);

        $user->update($request->only('name', 'email', 'role_id'));

        return back()->with('success', __('dash.saved'));
    }

    public function destroy($id)
    {
        if ($id == auth()->id()) {
            return back()->with('error', __('dash.cannot_delete_self'));
        }

        User::findOrFail($id)->delete();

        return redirect()->route('admin.users.index')->with('success', __('dash.deleted'));
    }
}
