<?php

namespace App\Http\Controllers\API;

use App\Helpers\Managers\UserFriendManager;
use App\Http\Controllers\Controller;
use App\Http\Resources\API\NotificationResource;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

        $notification->update([
            'seen' => true,
        ]);

        if ($request->accept) {
            $payload = json_decode($notification->payload, true);
            switch ($notification->type) {
                case Notification::FRIEND_REQUEST:
                    $friend = User::whereUuid($payload['friend_uuid'])->firstOrFail();
                    $user = User::whereUuid($payload['user_uuid'])->firstOrFail();

                    UserFriendManager::AddFriend($user, $friend);

                    break;
                case Notification::GAME_INVITE:
                    return response()->json([
                        'message' => 'Notification updated.',
                        'game_uuid' => $payload['game_uuid'],
                    ]);

                    break;
                default:
                    return response()->json([
                        'message' => 'Not supported notification type',
                    ], 400);
            }
        }

        return response()->json([
            'message' => 'Notification updated.',
        ]);
    }
}
