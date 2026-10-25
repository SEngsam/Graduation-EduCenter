<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ability;
use App\Models\Role;


class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::paginate(10);
        return view('admin.roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $abilities = Ability::all();
        return view('admin.roles.create', compact('abilities'));
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
            'name' => 'required'
        ]);

        $role = Role::create([
            'name' => $request->name
        ]);

        $role->abilities()->attach($request->ability);

        return redirect()->route('admin.roles.index')->with('msg', __('dash.saved_new'));
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
    public function edit(Role $role)
    {
        // $role = Role::findOrFail($id);
        $abilities = Ability::all();
        return view('admin.roles.edit', compact('abilities', 'role'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required'
        ]);

        $role->update([
            'name' => $request->name
        ]);

        $role->abilities()->sync($request->ability);

        return redirect()->route('admin.roles.index')->with('msg', __('dash.saved'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // public function destroy($id)
    // {
    //     $roles = Role::findOrFail($id);
    //     $roles->delete();
    //     return view('admin.roles.index', compact('roles'));
    // }

    public function destroy($id)
{
    $role = Role::find($id);

    if (!$role) {
        return redirect()->route('admin.roles.index')->with([
            'type' => 'danger',
            'msg' => 'Role not found.'
        ]);
    }

    $role->delete();

    return redirect()->route('admin.roles.index')->with([
        'type' => 'success',
        'msg' => 'Role deleted successfully.'
    ]);
}

    /**
     * Display a trashed listing of the resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function trash()
    {
        //
    }

    /**
     * Restore the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore($id)
    {
        //
    }

    /**
     * forcedelete the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function forcedelete($id)
    {
        //
    }
}
