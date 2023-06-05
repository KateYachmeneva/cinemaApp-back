<?php

namespace App\Http\Controllers;

use App\Http\Requests\TicketRequest;
use App\Models\Seat;
use App\Models\Ticket;
use Illuminate\Http\JsonResponse;


class TicketController extends Controller
{
       public function store(TicketRequest $request): JsonResponse
    {
        $ticket = Ticket::query() -> create($request->validated());
        foreach ($request->validated()['seats'] as $seatId) {
            $seat = Seat::query() ->findOrFail($seatId);
            $ticket->seats()->save($seat);
        }

        return response()->json($ticket->whereId($ticket->id)->with('session')->with('seats')->first(), 201);
    }

    public function show($id): JsonResponse
    {
        return response()->json(Ticket::query() -> whereId($id)->with('session')->with('seats')->first());
    }
}