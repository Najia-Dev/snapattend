<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
// Jika menggunakan Pusher untuk notifikasi real-time
use Pusher\Pusher;

class ChatController extends Controller
{
    // Method untuk mengambil daftar karyawan
    public function getEmployees()
    {
        // Pastikan hanya admin yang bisa melihat daftar karyawan
        if (Auth::user()->role !== 'admin' && Auth::user()->role !== 'karyawan') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $employees = User::where('role', 'karyawan')->get(['id', 'name']);
        return response()->json($employees);
    }

    // Method untuk mengirim pesan
    public function sendMessage(Request $request)
    {
        // Validasi input
        $request->validate([
            'message' => 'required|string|max:255',
        ]);

        // Cek apakah pengguna sedang login
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Buat pesan baru
        $chat = Chat::create([
            'sender_id' => Auth::user()->id,
            'receiver_id' => $request->receiver_id,
            'message' => $request->message,
        ]);

        // Jika menggunakan Pusher untuk notifikasi real-time
        $pusher = new Pusher(
            config('broadcasting.connections.pusher.key'),
            config('broadcasting.connections.pusher.secret'),
            config('broadcasting.connections.pusher.app_id'),
            ['cluster' => config('broadcasting.connections.pusher.options.cluster')]
        );

        $pusher->trigger('chat-channel', 'new-message', ['message' => $chat]);

        return response()->json(['status' => 'success', 'chat' => $chat]);
    }

    // Method untuk mengambil pesan antara dua user
    public function fetchMessages($employeeId)
    {
        // Cek apakah pengguna sedang login
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $messages = Chat::where(function ($query) use ($employeeId) {
            $query->where('sender_id', Auth::user()->id)
                  ->where('receiver_id', $employeeId);
        })->orWhere(function ($query) use ($employeeId) {
            $query->where('sender_id', $employeeId)
                  ->where('receiver_id', Auth::user()->id);
        })->orderBy('created_at', 'ASC')->get();

        return response()->json($messages);
    }
}
