<?php

namespace App\Http\Controllers;

use App\Models\Asisten;
use App\Models\Praktikan;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        if ($user->role === 'praktikan') {
            if (!$user->profile_completed) {
                return redirect()->route('profile.complete');
            }

            return redirect()->route('dashboard.praktikan');
        }

        if ($user->role === 'asisten') {
            return redirect()->route('dashboard.asisten');
        }

        if ($user->role === 'koordinator') {
            return redirect()->route('dashboard.koordinator');
        }

        abort(403, 'Role pengguna tidak dikenali.');
    }

    public function asisten()
    {
        $user = Auth::user();

        $asisten = Asisten::with('praktikum')
            ->where('user_id', $user->id)
            ->latest()
            ->first();

        return view('dashboard.asisten', compact('asisten'));
    }

    public function koordinator()
    {
        return view('dashboard.koordinator');
    }
}