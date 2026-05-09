<?php

namespace App\Http\Controllers;

use App\Models\Praktikan;
use App\Models\Praktikum;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CompleteProfileController extends Controller
{
    public function edit(): View|RedirectResponse
    {
        $user = User::find(Auth::id());

        if (!$user) {
            return redirect()->route('login');
        }

        if ($user->profile_completed) {
            return redirect()->route('dashboard');
        }

        $praktikums = Praktikum::where('is_aktif', true)->get();

        return view('auth.complete-profile', compact('praktikums', 'user'));
    }

    public function update(Request $request): RedirectResponse
    {
        $user = User::find(Auth::id());

        if (!$user) {
            return redirect()->route('login');
        }

        $request->validate([
            'praktikum_id' => ['required', 'exists:praktikums,id'],
            'name' => ['required', 'string', 'max:255'],
            'nim' => ['required', 'string', 'max:30', 'unique:users,nim,' . $user->id],
            'prodi' => ['required', 'string', 'max:100'],
            'jenis_kelamin' => ['required', 'in:L,P'],
            'nomor_ponsel' => ['required', 'string', 'max:20'],
        ]);

        $user->update([
            'name' => $request->name,
            'nim' => $request->nim,
            'prodi' => $request->prodi,
            'jenis_kelamin' => $request->jenis_kelamin,
            'nomor_ponsel' => $request->nomor_ponsel,
            'profile_completed' => true,
        ]);

        Praktikan::firstOrCreate(
            [
                'user_id' => $user->id,
                'praktikum_id' => $request->praktikum_id,
            ],
            [
                'kelompok_id' => null,
                'qr_code' => 'QR-' . $request->praktikum_id . '-' . $request->nim . '-' . strtoupper(Str::random(8)),
            ]
        );

        return redirect()->route('dashboard');
    }
}