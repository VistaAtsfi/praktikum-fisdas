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

        if ($user->role === 'praktikan' && !$user->profile_completed) {
            return redirect()->route('profile.complete');
        }

        if ($user->role === 'koordinator') {
            return redirect()->route('dashboard.koordinator');
        }

        if ($user->role === 'asisten') {
            return redirect()->route('dashboard.asisten');
        }

        return redirect()->route('dashboard.praktikan');
    }

    public function praktikan()
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        if (!$user->profile_completed) {
            return redirect()->route('profile.complete');
        }

        $praktikan = Praktikan::with(['praktikum', 'kelompok'])
            ->where('user_id', $user->id)
            ->latest()
            ->first();

        return view('dashboard.praktikan', compact('praktikan'));
    }

    public function asisten()
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

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