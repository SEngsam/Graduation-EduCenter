<?php

namespace App\Http\Controllers;

use App\Models\Role;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // Old /home link: send everyone to the page that fits their role
    public function index()
    {
        if (auth()->user()->hasRole(Role::ROLE_STUDENT)) {
            return redirect()->route('student.dashboard');
        }

        return redirect()->route('admin.index');
    }
}
