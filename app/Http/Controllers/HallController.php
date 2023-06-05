<?php

namespace App\Http\Controllers;

use App\Http\Requests\HallRequest;
use App\Models\Hall;
use Illuminate\Http\JsonResponse;



class HallController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        return response()->json(Hall::all());
    }

     public function store(HallRequest $request): JsonResponse
    {
        return response()->json(
            Hall::query()->create($request->validated())
        );
    }

    public function show($id): JsonResponse
    {
        return response()->json(
            Hall::query()->findOrFail($id)
        );
    }

    public function update(HallRequest $request,  $id): JsonResponse
    {
        $updateHallData = $request->all();
        $hall = Hall::query()->findOrFail($id);
        $hall->fill($updateHallData);
        $hall->save();
        return response()->json(
            $hall
        );
    }

    public function destroy($id): JsonResponse
    {
          $seats = Hall::query()->where('id', (int)$id)->first()->seats;
          $sessions = Hall::query()->where('id', (int)$id)->first()->sessions;

       foreach ($seats as $seat) {
           $seat->delete();
        }
       foreach ($sessions as $session) {
            $session->delete();
        }
        $deleted = Hall::query()->where('id', $id)->delete();

        if ($deleted) {
            return response()->json($id);
     }
        return response()->json('not ok');
   }
}
