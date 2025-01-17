<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\NumberStoreRequest;
use App\Models\Number;
use App\Repositories\BaseRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class NumberController extends Controller
{
    private BaseRepository $repo;

    public function __construct()
    {
        $this->repo = new BaseRepository(new Number());
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        return \Illuminate\Http\Resources\Json\JsonResource::collection(
            $this->repo->paginate(
                $request->get('limit', 10),
                $request->get('sort', 'desc'),
                $request->get('page', 1)
            )
        );
    }

    public function store(NumberStoreRequest $request): JsonResponse
    {
        $attributes = $request->validated();
        $number = $this->repo->create($attributes);

        return response()->json([
            'id' => $number->id,
        ], 201);
    }

    public function destroy($id): JsonResponse
    {
        Number::destroy($id);

        return response()->json([
            'id' => $id,
        ]);
    }
}
