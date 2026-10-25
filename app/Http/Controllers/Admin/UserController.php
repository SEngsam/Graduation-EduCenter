<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $data = [];
        $data['users'] = User::with('role')
            ->when(request('role_id'), function ($query, $roleId) {
                $query->where('role_id', $roleId);
            })
            ->when(request('q'), function ($query, $q) {
                $query->where(function ($w) use ($q) {
                    $w->where('name', 'like', "%{$q}%")->orWhere('email', 'like', "%{$q}%");
                });
            })
            ->orderByDesc('id')
            ->paginate(10);
        $data['roles'] = Role::all();
        
        return view('admin.users.index',$data);
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
        'email' => 'required|email|unique:users,email',
        'password' => 'required|string|min:8|confirmed',
        'role_id' => 'required|exists:roles,id',
    ]);

    // Create the new user
    User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'role_id' => $request->role_id,
    ]);

    return redirect()->route('admin.users.index')->with('msg', __('dash.saved_new'))->with('type', 'success');
}

    public function update(Request $request, $id)
    {
       
        $user = User::findOrFail($id);
        $user->update($request->only('name', 'email', 'role_id'));
        return back()->with('msg', __('dash.saved'))->with('type', 'success');
    }

    public function destroy($id)
    {
        User::destroy($id);

        return redirect()->route('admin.users.index')->with('msg', __('dash.deleted'))->with('type', 'danger');
    }
}
