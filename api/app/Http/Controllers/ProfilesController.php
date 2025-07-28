<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UsersProfiles;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Models\File;

class ProfilesController extends Controller
{
    function getProfile(Request $request){
        $validated = $request->validate([
            'user_id' => 'required|integer'
        ]);

        $user = User::find($validated['user_id']);
        $profile = UsersProfiles::where('user_id',$user->id)->get()->first();
        if(!$profile) {
            return response()->json(['message' => 'Profile not found'], 404);
        }

        return $profile;
    }

    function updateProfile(Request $request){
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . auth()->id(),
            'last_name' => 'required|string|max:255',
            'country' => 'required|string|max:100',
            'city' => 'nullable|string|max:100',
            'bio' => 'nullable|string|max:500',
            'phone' => 'nullable|string|max:20',
            'facebook' => 'nullable|url|max:255',
            'twitter' => 'nullable|url|max:255',
            'instagram' => 'nullable|url|max:255',
            'linkedId' => 'nullable|url|max:255',
            'postal_code' => 'nullable|string|max:20',
            'facebook' => 'nullable|url|max:255',
        ]);

        $user = auth()->user();
        $profile = UsersProfiles::where('user_id', $user->id)->first();
        Log::info($profile);

        if(!$profile) {
            $profile = new UsersProfiles();
            $profile->user_id = $user->id;
        }

        if ($profile && $user) {
            $user->name = $validated['name'];
            $user->email = $validated['email'];
            $user->last_name = $validated['last_name'];

            $profile->postal_code = $validated['postal_code'] ?? null;
            $profile->facebook = $validated['facebook'] ?? null;
            $profile->twitter = $validated['twitter'] ?? null;
            $profile->instagram = $validated['instagram'] ?? null;
            $profile->linkedId = $validated['linkedId'] ?? null;
            $profile->phone = $validated['phone'];
            $profile->bio = $validated['bio'] ?? null;
            $profile->country = $validated['country'];
            $profile->city = $validated['city'] ?? null;

            $profile->save();
            $user->save();

            Log::info($user);
            return response()->json(['message' => 'Profile updated successfully!'], 200);
        }
        return response()->json(['message' => 'Profile didn\'t exists'], 404);
    }

    function updateProfileImage(Request $request){
        $validated = $request->validate([
            'image' => 'required|string', // Assuming base64 image string
        ]);

        $imageData = $validated['image'];
        $decodedFile = null;
        $extension = '';
        if (preg_match('/^data:image\/(\w+);base64,/', $imageData, $type)) {
            $extension = $type[1];
            if (!in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])) {
                return response()->json(['error' => 'Invalid image type'], 400);
            }
            $imageData = substr($imageData, strpos($imageData, ',') + 1);
            $decodedFile = base64_decode($imageData);
        } else {
            return response()->json(['error' => 'Invalid image data'], 400);
        }

        $user = auth()->user();
        $profile = UsersProfiles::where('user_id', $user->id)->first();

        if(!$profile) {
            return response()->json(['error' => 'Profile is not completed yet!'], 404);
        }

        $filename = 'images/profile_images/profile_image_' . $user->id . '_' . time().'.' . $extension;

        if ($profile) {
            Storage::disk('public')->put($filename, $decodedFile);
            $profile->image = $filename;
            $profile->save();
            return response()->json(['message' => 'Profile image updated successfully!'], 200);
        }
        return response()->json(['error' => 'Profile didn\'t exists'], 404);
    }
}
