<?php

namespace App\Http\Controllers\API;

use App\Helpers\Managers\QuestManager;
use App\Helpers\Managers\UserLevelManager;
use App\Http\Controllers\Controller;
use App\Http\Resources\API\QuestResource;
use App\Models\Quest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

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
