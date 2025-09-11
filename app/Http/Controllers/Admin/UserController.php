<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller {
    // 使用者清單
    public function index() {
        $users = User::paginate(10);
        return view('admin.users.index', compact('users'));
    }

    // 封禁使用者 30 天
    public function ban(User $user) {
        $user->user_status = 'banned';
        $user->banned_until = now()->addDays(30);
        $user->save();

        return back()->with(
            'success',
            "已封禁 {$user->nickname}，直到 " . $user->banned_until->format('Y-m-d H:i')
        );
    }

    // 解除封禁
    public function unban(User $user) {
        $user->user_status = 'active';
        $user->banned_until = null;
        $user->save();

        return back()->with(
            'success',
            "已解除封禁 {$user->nickname}"
        );
    }
}
