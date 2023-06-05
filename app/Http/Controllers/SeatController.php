<?php

namespace App\Http\Controllers;

use App\Http\Requests\SeatRequest;
use App\Models\Hall;
use App\Models\Seat;
use App\Models\Session;
use Illuminate\Http\JsonResponse;


class SeatController extends Controller
{

    public function index(): JsonResponse
    {
        return response()->json(Seat::all());
    }


    public function store(SeatRequest $request): JsonResponse
    {
        $id = $request->validated()['seats'][0]['hall_id'];
        $seats = Hall::query()->where('id', (int)$id)->first()->seats;
        $sessions = Hall::query()->where('id', (int)$id)->first()->sessions;

        foreach ($seats as $seat) {
            $seat->delete();
        }
        foreach ($sessions as $session) {
            $session->delete();
        }

            foreach ($request->validated()['seats'] as $seat) {
            Seat::query()->create($seat);
        }
        return response()->json($id);
    }


    public function show($id): JsonResponse
    {
        return response()->json(
            Seat::query()->where('hall_id', $id)->get()
        );
    }


    public function updateMany(SeatRequest $request): JsonResponse
    {
        foreach ($request->validated()['seats'] as $seat) {
            $upSeat = Seat::query()->findOrFail($seat['id']);
            $upSeat->fill($seat);
            $upSeat->save();
        }
        return response()->json(true, 201);
    }
}
