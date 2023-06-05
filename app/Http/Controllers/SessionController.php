<?php

namespace App\Http\Controllers;

use App\Http\Requests\SessionRequest;
use App\Models\Hall;
use App\Models\Session;
use DateTime;
use Illuminate\Http\JsonResponse;


class SessionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        return response()->json(Session::all());
    }


    public function store(SessionRequest $request): JsonResponse
    {
        return response()->json(
            Session::query()->create($request->validated())
        );
     }


    public function show($datetime): JsonResponse
    {
        $dateFormatted = DateTime::createFromFormat('Y-m-d', $datetime)->format('Y-m-d');
        Session::query()->where('datetime', $dateFormatted)->get();
        return response()->json(
            Session::query()->whereDate('datetime', $dateFormatted)->get()
        );
    }


    public function update(SessionRequest $request,  $id): JsonResponse
    {
        $session = Session::query()->findOrFail($id);
        $session->fill($request->validated());
        $session->save();
        return response()->json(
            $session
        );
    }

    public function destroy($id): JsonResponse
    {
        $tickets = Session::query()->where('id', (int)$id)->first()-> tickets;


        foreach ($tickets as $ticket) {
            $ticket->delete();
        }


          $deleted = Session::query()->where('id', $id)->delete();

        if ($deleted->delete()) {
            return response()->json($id);
        }
        return response()->json('not_ok');
    }
}