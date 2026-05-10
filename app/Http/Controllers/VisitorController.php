<?php

namespace App\Http\Controllers;

use App\Models\Visitor;
use Illuminate\Http\Request;

class VisitorController extends Controller
{
    /**
     * Track a visitor
     */
    public function track(Request $request)
    {
        try {
            // Cek apakah IP sudah dicatat dalam 1 jam terakhir
            $existingVisitor = Visitor::where('ip_address', $request->ip())
                                       ->where('visited_at', '>=', now()->subHour())
                                       ->first();

            // Jika belum, catat visitor baru
            if (!$existingVisitor) {
                Visitor::create([
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->header('User-Agent'),
                ]);
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
}
