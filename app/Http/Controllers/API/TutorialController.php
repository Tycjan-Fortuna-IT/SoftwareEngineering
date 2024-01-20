<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\API\TutorialResource;
use App\Models\Tutorial;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\Auth;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class TutorialController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return ResourceCollection|JsonResponse
     */
    public function index(Request $request): ResourceCollection|JsonResponse
    {
        $user = User::whereId(Auth::id())->firstOrFail();

        $tutorials = $user->tutorials()->get();

        return TutorialResource::collection($tutorials);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Tutorial $tutorial
     * @return JsonResponse
     */
    public function update(Request $request, Tutorial $tutorial): JsonResponse
    {
        $request->validate([
            'completed' => 'nullable|boolean',
        ]);

        $tutorial->completed = $request->completed ?? $tutorial->completed;
        $tutorial->save();

        return response()->json([
            'message' => 'Tutorial updated successfully',
        ]);
    }
}
