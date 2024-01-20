<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\API\NotificationResource;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Display a listing of the resource.

     *
     * @param Request $request
     * @return ResourceCollection
     */
    public function index(Request $request): ResourceCollection
    {
        $user = User::whereId(Auth::id())->firstOrFail();

        $notifications = $user->notifications()
            ->where('seen', false)
            ->latest()
            ->get();

        return NotificationResource::collection($notifications);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Notification $notification
     * @return JsonResponse
     */
    public function update(Request $request, Notification $notification): JsonResponse
    {
        $request->validate([
            'accept' => 'required|boolean',
        ]);

        // switch ($notification->type) {
        //     case Notification::FRIEND_REQUEST:
        //         // TODO
        //         break;
        //     case Notification::GAME_INVITE:
        //         // TODO
        //         break;
        //     default:
        //         return response()->json([
        //             'message' => 'Not supported notification type',
        //         ], 400);
        // }

        $notification->update([
            'seen' => true,
        ]);

        return response()->json([
            'message' => 'Not yet implemented',
        ]);
    }
}
