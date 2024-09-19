<?php

namespace App\Http\Controllers;

use App\Models\Broadcast;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
// Jika menggunakan Pusher untuk notifikasi real-time
use Pusher\Pusher;

class BroadcastController extends Controller
{
    // Method untuk mengirim pesan broadcast
    public function sendBroadcast(Request $request)
    {
        // Validasi input
        $request->validate([
            'message' => 'required|string|max:255',
        ]);

        // Cek apakah pengguna sedang login dan memiliki peran admin
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Simpan pesan broadcast ke database
        $broadcast = Broadcast::create([
            'message' => $request->message,
        ]);

        // Jika menggunakan Pusher untuk notifikasi real-time
        $pusher = new Pusher(
            config('broadcasting.connections.pusher.key'),
            config('broadcasting.connections.pusher.secret'),
            config('broadcasting.connections.pusher.app_id'),
            ['cluster' => config('broadcasting.connections.pusher.options.cluster')]
        );

        $pusher->trigger('broadcast-channel', 'new-broadcast', ['message' => $broadcast]);

        return response()->json(['status' => 'success', 'broadcast' => $broadcast]);
    }

    // Method untuk mengambil semua pesan broadcast
    public function fetchBroadcasts()
    {
        // Cek apakah pengguna sedang login
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $broadcasts = Broadcast::orderBy('created_at', 'DESC')->get();
        return response()->json($broadcasts);
    }
}
