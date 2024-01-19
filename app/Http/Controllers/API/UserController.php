<?php

namespace App\Http\Controllers\API;

use App\Events\MessageSentSocketEvent;
use App\Events\MyEvent;
use App\Helpers\PaginationHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\API\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Support\Facades\Hash;
use App\Helpers\Managers\UserFriendManager;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\AllowedFilter;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return ResourceCollection
     */
    public function index(Request $request): ResourceCollection
    {
        $users = QueryBuilder::for(User::class)
            ->allowedFilters(
                AllowedFilter::scope('search'),
                AllowedFilter::scope('search_not_friend'),
                AllowedFilter::scope('not_friend'),
            );

        PaginationHelper::Paginate($users, $request);

        return UserResource::collection($users);
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @param User $user
     * @return UserResource
     */
    public function show(Request $request, User $user): UserResource
    {
        $request->validate([
            'withFriends' => 'nullable',
        ]);

        $withFriends = $request->withFriends === 'true' ? true : false;

        if ($withFriends) {
            $user->load('friends');
        }

        return new UserResource($user);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param User $user
     * @return UserResource
     */
    public function update(Request $request, User $user): JsonResponse
    {
        $request->validate([
            'name' => 'nullable|string|max:255',
            'avatar' => 'nullable|string',
            'about' => 'nullable|string',
            'email' => 'nullable|email|unique:users|max:255',
            'anonymous' => 'nullable|boolean',
            'password' => 'nullable|confirmed|min:8',
        ]);

        $user->name = $request->name ?? $user->name;
        $user->email = $request->email ?? $user->email;
        $user->avatar = $request->avatar ?? $user->avatar;
        $user->about = $request->about ?? $user->about;
        $user->anonymous = $request->anonymous ?? $user->anonymous;

        if (isset($request->password)) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return response()->json(['message' => 'User updated.'], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param User $user
     * @return JsonResponse
     */
    public function destroy(Request $request, User $user): JsonResponse
    {
        DB::beginTransaction();

        $user->comments()->delete();
        $user->posts()->delete();
        $user->tutorials()->delete();
        $user->quests()->delete();
        $user->quizzes()->delete();
        $user->notifications()->delete();

        $user->delete();

        DB::commit();

        return response()->json(['message' => 'User deleted.'], 200);
    }

    /**
     * Add a friend to the specified user.
     *
     * @param Request $request
     * @param User $user
     * @return JsonResponse
     */
    public function addFriend(Request $request, User $user): JsonResponse
    {
        $request->validate([
            'friend_uuid' => 'required|uuid|exists:users,uuid',
        ]);

        $friend = User::where('uuid', $request->friend_uuid)->first();

        if (UserFriendManager::HasFriend($user, $friend)) {
            return response()->json(['message' => 'This user is already your friend!'], 400);
        }

        // TODO: Add notification to the friend.
        UserFriendManager::AddFriend($user, $friend);

        return response()->json(['message' => 'Friend added.'], 200);
    }

    /**
     * Remove a friend from the specified user.
     *
     * @param Request $request
     * @param User $user
     * @return JsonResponse
     */
    public function removeFriend(Request $request, User $user): JsonResponse
    {
        $request->validate([
            'friend_uuid' => 'required|uuid|exists:users,uuid',
        ]);

        $friend = User::where('uuid', $request->friend_uuid)->first();

        if (!UserFriendManager::HasFriend($user, $friend)) {
            return response()->json(['message' => 'This user is not your friend!'], 400);
        }

        UserFriendManager::RemoveFriend($user, $friend);

        return response()->json(['message' => 'Friend removed.'], 200);
    }

    /**
     * Update favourite status of a friend of the specified user.
     *
     * @param Request $request
     * @param User $user
     * @return JsonResponse
     */
    public function updateFavourite(Request $request, User $user): JsonResponse
    {
        $request->validate([
            'friend_uuid' => 'required|uuid|exists:users,uuid',
            'favourite' => 'required|boolean',
        ]);

        $friend = User::where('uuid', $request->friend_uuid)->first();

        if (!UserFriendManager::HasFriend($user, $friend)) {
            return response()->json(['message' => 'This user is not your friend!'], 400);
        }

        UserFriendManager::UpdateFavourite($user, $friend, $request->favourite);

        return response()->json(['message' => 'Favourite status updated.'], 200);
    }

    /**
     * Send a message to the specified user.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function sendMessage(Request $request): JsonResponse
    {
        $request->validate([
            'receiver_uuid' => 'required|uuid|exists:users,uuid',
            'message' => 'required|string',
        ]);

        $sender = User::whereId(Auth::id())->first();
        $receiver = User::where('uuid', $request->receiver_uuid)->first();

        event(new MessageSentSocketEvent($sender->uuid, $receiver->uuid, $request->message));

        return response()->json(['message' => 'Message sent.'], 200);
    }
}
