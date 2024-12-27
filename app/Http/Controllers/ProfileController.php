<?php

namespace App\Http\Controllers;

use App\Models\Mahasiswa;
use App\Models\Phone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;


class ProfileController extends Controller
{
    public function showProfile()
    {
        // Get the currently authenticated user
        $user = Auth::user()->load('phones');

        // Pass the user data to the view
        return view('profile', compact('user'));
    }

    public function edit()
    {
        $user = Auth::user()->load('phones'); // Get the logged-in user
        return view('edit', compact('user'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,',
            'age' => 'required|integer',
            'phone' => 'required|max:255',
            'profilePicture' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $user = Auth::user()->load('phones');
        if ($request->hasFile('profilePicture')) {
            $cloudinaryImage = $request->file('profilePicture')->storeOnCloudinary('profilePicture');
            $url = $cloudinaryImage->getSecurePath();

            // Save the file path in the database
            $user->profilePicture = $url;
            $user->save();
        }

        $user->update($request->only('name', 'email', 'age', 'phone', 'profilePicture')); // Add other fields as needed

        return redirect()->route('update.update')->with('success', 'Profile updated successfully.');
    }
}
