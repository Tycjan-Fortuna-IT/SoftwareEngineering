<?php

namespace App\Http\Controllers\API;

use App\Helpers\Managers\QuestManager;
use App\Helpers\Managers\UserLevelManager;
use App\Http\Controllers\Controller;
use App\Http\Resources\API\QuestResource;
use App\Models\Quest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class QuestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return ResourceCollection
     */
    public function index(Request $request): ResourceCollection
    {
        $quests = QuestManager::GetOrGenerateQuestsForUser();

        return QuestResource::collection($quests);
    }

    // /**
    //  * Store a newly created resource in storage.
    //  *
    //  * @param Request $request
    //  * @return JsonResponse
    //  */
    // public function store(Request $request): JsonResponse
    // {
    //     $request->validate([
    //         'type' => 'required|integer',
    //         'required' => 'required|integer',
    //         'reward' => 'required|integer',
    //         'user_uuid' => 'required|uuid|exists:users,uuid',
    //     ]);

    //     $user = User::whereUuid($request->user_uuid)->first();

    //     $quest = Quest::create([
    //         'type' => $request->type,
    //         'status' => Quest::STATUS_IN_PROGRESS,
    //         'required' => $request->required,
    //         'collected' => 0,
    //         'reward' => $request->reward,
    //         'user_id' => $user->id,
    //     ]);

    //     return response()->json([
    //         'message' => 'Quest created successfully',
    //         'data' => new QuestResource($quest),
    //     ], 201);
    // }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Quest $quest
     * @return JsonResponse
     */
    public function update(Request $request, Quest $quest): JsonResponse
    {
        $request->validate([
            'collected' => 'nullable|integer',
        ]);

        $quest->collected = $request->collected ?? $quest->collected;

        if ($quest->collected >= $quest->required) {
            $quest->status = Quest::STATUS_COMPLETED;

            UserLevelManager::AddExp($quest->user, $quest->reward);
        }

        $quest->save();

        return response()->json([
            'message' => 'Quest updated successfully',
        ]);
    }
}
