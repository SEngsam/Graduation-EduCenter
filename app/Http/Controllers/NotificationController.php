<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    private $groups = [
        'grades' => ['grade'],
        'assignments' => ['assignment', 'reminder', 'submission'],
        'attendance' => ['absent'],
    ];

    public function index(Request $request)
    {
        $user = auth()->user();
        $filter = $request->input('show', 'all');

        $query = $user->notifications();

        if ($filter == 'unread') {
            $query = $user->unreadNotifications();
        }

        if (isset($this->groups[$filter])) {
            $query->whereIn('data->type', $this->groups[$filter]);
        }

        $notifications = $query->latest()->paginate(20)->withQueryString();

        $settingTypes = User::EMAIL_TYPES;
        if ($user->hasRole(Role::ROLE_STUDENT)) {
            unset($settingTypes['submission'], $settingTypes['question']);
        } else {
            $settingTypes = ['submission' => false, 'question' => false];
        }

        return view('notifications.index', compact('notifications', 'filter', 'settingTypes'));
    }

    public function open($id)
    {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        return redirect($notification->data['url'] ?? route('notifications.index'));
    }

    public function readAll()
    {
        auth()->user()->unreadNotifications->markAsRead();

        return back()->with('success', __('notify.all_read'));
    }

    public function settings(Request $request)
    {
        $settings = [];
        foreach (array_keys(User::EMAIL_TYPES) as $type) {
            $settings[$type] = $request->has('email.' . $type);
        }

        $user = auth()->user();
        $user->email_settings = $settings;
        $user->save();

        return back()->with('success', __('notify.settings_saved'));
    }
}
