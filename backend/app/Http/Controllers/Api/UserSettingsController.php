<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserSettingsController extends Controller
{
    // Update username
    public function updateName(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);

        $user = JWTAuth::parseToken()->authenticate();
        $user->update(['name' => $request->name]);

        return response()->json([
            'success' => true,
            'msg' => 'Name updated successfully',
            'user' => $user
        ]);
    }

    // Update email
    public function updateEmail(Request $request)
    {
        $request->validate(['email' => 'required|email|max:255|unique:users,email']);

        $user = JWTAuth::parseToken()->authenticate();
        $user->update(['email' => $request->email]);

        return response()->json([
            'success' => true,
            'msg' => 'Email updated successfully',
            'user' => $user
        ]);
    }

    // Change password
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:8|confirmed'
        ]);

        $user = JWTAuth::parseToken()->authenticate();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['message' => 'Current password is incorrect'], 422);
        }

        $user->password = bcrypt($request->new_password);
        $user->save();

        return response()->json(['message' => 'Password updated successfully']);
    }

    // Delete profile
    public function deleteProfile()
    {
        $user = JWTAuth::parseToken()->authenticate();
        $user->delete();

        return response()->json(['success' => true, 'msg' => 'User deleted successfully']);
    }
}
