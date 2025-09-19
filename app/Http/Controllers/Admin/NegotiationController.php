<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Negotiation;
use Illuminate\Http\Request;

class NegotiationController extends Controller {
    // 列出所有議價
    public function index() {
        $negotiations = Negotiation::with(['item', 'buyer', 'seller'])
            ->latest()
            ->paginate(20);

        return view('admin.negotiations.index', compact('negotiations'));
    }

    // 管理員同意
    public function agree(Negotiation $negotiation) {
        $negotiation->update(['status' => 'agreed']);
        return back()->with('success', '管理員已同意此議價');
    }

    // 管理員拒絕
    public function reject(Negotiation $negotiation) {
        $negotiation->update(['status' => 'rejected']);
        return back()->with('info', '管理員已拒絕此議價');
    }
}
