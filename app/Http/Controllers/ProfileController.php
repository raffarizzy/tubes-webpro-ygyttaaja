<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();

        // ambil profile dari Node.js
        $profile = Http::get('http://localhost:3001/api/profile/'.$user->id)->json();

        return view('profile.edit', [
            'user'    => $user,
            'profile' => $profile,
        ]);
    }

    /*
    * Update user's profile picture
    */
    public function update_pfp(Request $request) 
    {
        $request->validate([
            'pfpPath' => 'required|image|mimes:jpg,jpeg,png'
        ]);

        $user = $request->user();

         // 2. HAPUS FOTO LAMA (JIKA ADA)
        if ($user->pfpPath && str_contains($user->pfpPath, asset('storage'))) {
            $oldPath = str_replace(asset('storage') . '/', '', $user->pfpPath);
            Storage::disk('public')->delete($oldPath);
        }

        // 3. SIMPAN FOTO BARU
        $path = $request->file('pfpPath')->store('avatars', 'public');

        // 4. BUAT URL UNTUK BROWSER
        $avatarUrl = asset('storage/' . $path);

        // 5. UPDATE USER
        Http::patch("http://localhost:3001/api/profile/{$user->id}", [
            'pfpPath' => $avatarUrl
        ]);

        // 6. RESPONSE
        return back()->with('status', 'avatar-updated');
    }

    /**
    * Update the user's profile information.
    */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();

        /* 1. UPDATE USER (BREEZE) */
        $userData = $request->only([
            'name',
            'email',
        ]);

        if ($request->filled('password')) {
            $userData['password'] = Hash::make($request->password);
        }

        if ($user->email !== $request->email) {
            $userData['email_verified_at'] = null;
        }

        $user->update($userData);

        /* 2. UPDATE PROFILE (NODE.JS) */
        Http::patch('http://localhost:3001/api/profile/'.$user->id, [
            'phone'     => $request->phone,
            'birthDate' => $request->birthDate,
            'gender'    => $request->gender,
        ]);

        return Redirect::route('profile.edit')
            ->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
