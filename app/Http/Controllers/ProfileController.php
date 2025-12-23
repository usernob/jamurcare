<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function show(): View
    {
        $user = Auth::user();
        return view("profile.index", ["user" => $user]);
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'username' => ['required', 'ascii', 'min:3', 'max:255'],
            'profile-photo' => ['nullable', 'image', 'max:2048'],
        ]);

        /** @var User $user */
        $user = $request->user();

        $data = [
            'name' => $validated['username'],
        ];

        if ($request->hasFile('profile-photo')) {
            $file = $request->file('profile-photo');

            $filename = $user->id . '.' . $file->getClientOriginalExtension();

            $path = $file->storeAs(
                'avatars',
                $filename,
                'public'
            );
            $data['avatar'] = asset('storage/' . $path);
        }

        $user->update($data);

        return redirect()->route('profile.edit');
    }
}
