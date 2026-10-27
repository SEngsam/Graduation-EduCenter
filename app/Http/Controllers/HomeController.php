<?php

namespace App\Http\Controllers;

use App\Models\Role;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        if (auth()->user()->hasRole(Role::ROLE_STUDENT)) {
            return redirect()->route('student.dashboard');
        }

        if (auth()->user()->hasRole(Role::ROLE_TEACHER)) {
            return redirect()->route('teach.dashboard');
        }

        return redirect()->route('admin.index');
    }
}
