<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileUpdateRequest;
use App\Http\Requests\UpdatePasswordRequest;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    /**
     * Display the profile edit form.
     */
    public function edit()
    {
        $user = auth()->user();
        return view('admin.profile.edit', compact('user'));
    }

    /**
     * Update the profile.
     */
    public function update(ProfileUpdateRequest $request)
    {
        $user = auth()->user();
        $validated = $request->validated();

        $user->name = $validated['name'];
        $user->username = $validated['username'];
        $user->save();

        return redirect()->back()->with('success', 'Profil berhasil diperbarui!');
    }

    /**
     * Update the password.
     */
    public function updatePassword(UpdatePasswordRequest $request)
    {
        $user = auth()->user();
        $validated = $request->validated();

        // Verify current password
        if (!Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors(['current_password' => 'Password saat ini tidak cocok']);
        }

        // Update password
        $user->password = Hash::make($validated['password']);
        $user->save();

        return redirect()->back()->with('success', 'Password berhasil diperbarui!');
    }
}
