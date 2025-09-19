<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProviderApplication;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProviderApplicationController extends Controller
{
    /**
     * [ADMIN] Get all pending applications.
     */
    public function index()
    {
        $applications = ProviderApplication::with('user') // Eager load user data
            ->where('status', 'pending')
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json(['applications' => $applications]);
    }

    /**
     * [USER] Store a new application.
     */
    public function store(Request $request)
    {
        $request->validate([
            'real_name' => 'required|string|max:255',
            'document_url' => 'required|url',
        ]);

        $user = Auth::user();

        // Prevent duplicate pending applications
        if ($user->application_status === 'pending' || $user->is_verified) {
            return response()->json(['message' => 'An application is already pending or you are already verified.'], 409);
        }

        // Create the application record
        ProviderApplication::create([
            'user_id' => $user->id,
            'real_name' => $request->real_name,
            'document_url' => $request->document_url,
            'status' => 'pending',
        ]);

        // Update the user's status
        $user->application_status = 'pending';
        $user->save();

        return response()->json(['message' => 'Application submitted successfully.'], 201);
    }

    /**
     * [ADMIN] Approve an application.
     */
    public function approve(ProviderApplication $application)
    {
        // Update application status
        $application->status = 'approved';
        $application->save();

        // Update user status
        $user = $application->user;
        $user->is_verified = true;
        $user->application_status = 'approved';
        $user->save();

        // Create notification for the user
        DB::table('notifications')->insert([
            'user_id' => $user->id,
            'type' => 'application_approved',
            'message' => 'Congratulations! Your provider application has been approved. You can now create and manage services.',
            'is_read' => false,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Here you can also trigger an email notification to the user
        // Mail::to($user->email)->send(new ApplicationApproved($user));

        return response()->json(['message' => 'Application approved successfully and user notified.']);
    }

    /**
     * [ADMIN] Reject an application.
     */
    public function reject(ProviderApplication $application)
    {
        // Update application status
        $application->status = 'rejected';
        $application->save();

        // Update user status
        $user = $application->user;
        $user->application_status = 'rejected';
        $user->save();

        // Create notification for the user
        DB::table('notifications')->insert([
            'user_id' => $user->id,
            'type' => 'application_rejected',
            'message' => 'We regret to inform you that your provider application has been rejected. Please review our requirements and feel free to reapply.',
            'is_read' => false,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Here you can also trigger an email notification to the user
        // Mail::to($user->email)->send(new ApplicationRejected($user));

        return response()->json(['message' => 'Application rejected successfully and user notified.']);
    }
}
