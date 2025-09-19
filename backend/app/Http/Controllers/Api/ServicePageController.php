<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;

class ServicePageController extends Controller
{
    // Show single service
    public function show($services_id)
    {
        $service = DB::selectOne(
            "SELECT * FROM services WHERE services_id = ?",
            [$services_id]
        );

        if (!$service) {
            return response()->json([
                'success' => false,
                'message' => 'Service not found'
            ], 404);
        }

        $image = DB::selectOne(
            "SELECT path FROM images WHERE user_id = ? LIMIT 1",
            [$service->user_id]
        );

        $service->profile_picture = $image ? $image->path : 'default.jpeg';

        // Booking availability
        $availability = DB::selectOne(
            "SELECT is_booked FROM service_availabilities WHERE services_id = ?",
            [$service->services_id]
        );
        $service->is_booked = $availability ? (bool)$availability->is_booked : false;

        return response()->json([
            'success' => true,
            'service' => $service
        ]);
    }

    // Add booking
    public function addBookings(Request $request)
    {
        $request->validate([
            'services_id' => 'required|integer',
            'user_id' => 'required|integer',
            'booking_time' => 'required|string',
        ]);

        // Get the provider ID for this service
        $service = DB::selectOne(
            "SELECT user_id FROM services WHERE services_id = ?",
            [$request->services_id]
        );

        if (!$service) {
            return response()->json([
                'success' => false,
                'message' => 'Service not found'
            ], 404);
        }

        // Ensure all provider's services have availability records
        $this->ensureServiceAvailabilityRecords($service->user_id);

        $bookingId = DB::table('bookings')->insertGetId([
            'services_id' => $request->services_id,
            'user_id' => $request->user_id,
            'booking_time' => $request->booking_time,
            'status' => false,
            'payment_status' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Mark ALL services by this provider as unavailable
        DB::update(
            "UPDATE service_availabilities 
             SET is_booked = 1, updated_at = NOW() 
             WHERE services_id IN (
                 SELECT services_id FROM services WHERE user_id = ?
             )",
            [$service->user_id]
        );

        $booking = DB::selectOne(
            "SELECT * FROM bookings WHERE booking_id = ?",
            [$bookingId]
        );

        return response()->json([
            'success' => true,
            'message' => 'Booking completed successfully! All services by this provider are now unavailable.',
            'booking' => $booking
        ]);
    }

    // Get bookings for logged-in user
    public function getUserBookings()
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $userId = $user->id;

            $bookings = DB::select("
                SELECT 
                    b.booking_id,
                    b.services_id,
                    b.user_id,
                    b.booking_time,
                    CASE 
                        WHEN b.status = 2 THEN 'completed'
                        WHEN b.status = 1 THEN 'confirmed'
                        ELSE 'pending'
                    END as status,
                    CASE 
                        WHEN b.payment_status = 1 THEN 'paid'
                        ELSE 'unpaid'
                    END as payment_status,
                    s.name as service_name,
                    s.price,
                    u.name as provider_name,
                    i.path as profile_picture,
                    b.created_at,
                    b.updated_at
                FROM bookings b
                INNER JOIN services s ON b.services_id = s.services_id
                INNER JOIN users u ON s.user_id = u.id
                LEFT JOIN images i ON s.user_id = i.user_id
                WHERE b.user_id = ?
                ORDER BY b.booking_id DESC
            ", [$userId]);

            return response()->json([
                'success' => true,
                'bookings' => $bookings
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated or token invalid'
            ], 401);
        }
    }

    // Helper method to ensure availability records exist
    private function ensureServiceAvailabilityRecords($userId)
    {
        // Get all services for this user that don't have availability records
        $servicesWithoutAvailability = DB::select("
            SELECT s.services_id 
            FROM services s 
            LEFT JOIN service_availabilities sa ON s.services_id = sa.services_id 
            WHERE s.user_id = ? AND sa.services_id IS NULL
        ", [$userId]);

        // Create availability records for services that don't have them
        foreach ($servicesWithoutAvailability as $service) {
            DB::table('service_availabilities')->insert([
                'services_id' => $service->services_id,
                'is_booked' => false,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }
}
