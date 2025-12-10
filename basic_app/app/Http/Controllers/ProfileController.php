<?php
// app/Http/Controllers/ProfileController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function edit(Request $request)
    {
        $user = $request->user();

        // Keep the app locale in sync for the view
        app()->setLocale($user->locale ?? config('app.locale'));

        return view('profile', compact('user'));
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name'              => ['required','string','max:255'],
            'email'             => ['required','email','max:255', Rule::unique('users')->ignore($user->id)],
            'phone'             => ['nullable','string','max:30'],
            'locale'            => ['required','in:en,ar'],
            'current_password'  => ['nullable','current_password'],
            'password'          => ['nullable','confirmed','min:8'],
            'avatar_path'            => ['nullable','image','max:2048'], // 2MB
        ], [], [
            'locale' => __('Language'),
        ]);

        // Basic profile fields
        $user->name   = $validated['name'];
        $user->email  = $validated['email'];
        $user->language = $validated['locale'];

        // Avatar
        if ($request->hasFile('avatar')) {
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar_path  = asset('storage/' . $path);
;
        }

        // Password (only if provided)
        if (!empty($validated['password'])) {
            // 'current_password' rule already verified current password if provided
            if (!$request->filled('current_password')) {
                return back()->withErrors(['current_password' => __('Please enter your current password to change it.')]);
            }
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        // Persist locale for the session so RTL/LTR flips immediately
        session(['locale' => $user->locale]);
        app()->setLocale($user->locale);

        return back()->with('status', __('Profile updated successfully.'));
    }
}
?>
