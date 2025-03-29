<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Response;
use App\Models\Division;
use App\Models\User;

class ProfileController extends Controller
{
    public function edit(Request $request)
    {
        return view('profile.edit', [
            'user' => $request->user(),
            'divisions' => Division::all(),
        ]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return back()->withErrors(['error' => 'User not found.']);
        }

        $validator = Validator::make($request->all(), [
            'division_id' => 'required|exists:divisions,id',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $user->division_id = $request->input('division_id');

            if ($request->hasFile('photo') && $request->file('photo')->isValid()) {
                if ($user->photo && Storage::exists('private/profile_pictures/' . $user->photo)) {
                    Storage::delete('private/profile_pictures/' . $user->photo);
                }

                $filename = time() . '_' . str_replace(' ', '_', strtolower($user->name)) . '.' . $request->file('photo')->extension();
                
                $path = $request->file('photo')->storeAs('private/profile_pictures', $filename, 'local');
                
                $user->photo = $filename;
            }

            $user->save();

            return redirect()->route('profile.edit')->with('status', 'Profile updated successfully!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to update profile: ' . $e->getMessage()]);
        }
    }

    public function showPhoto($filename)
    {
        $path = 'private/profile_pictures/' . $filename;
        
        if (!Storage::exists($path)) {
            abort(404);
        }
        
        return response()->file(Storage::path($path));
    }
}
