<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        // Kullanıcının bildirimleri
        $notifications = $request->user()->notifications()->latest()->get();

        // Gerekirse sadece unread göstermek için:
        // $notifications = $request->user()->unreadNotifications()->latest()->get();

        return response()->json($notifications);
    }
}
