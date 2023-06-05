<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilmRequest;
use App\Models\Film;
use Illuminate\Http\JsonResponse;
use Storage;


class FilmController extends Controller
{

    public function index(): JsonResponse
    {
        return response()->json(Film::all());
    }


    public function store(FilmRequest $request): JsonResponse
    {
        $film = new Film;
        $film->fill($request->validated());
        $film->poster = $request->poster->store('public');
        $film->save();
        return response()->json(
            $film->poster
        );
    }

    public function show($id): JsonResponse
    {
        return response()->json(
            Film::query()->findOrFail($id)
        );
    }


    public function update(FilmRequest $request, $id): JsonResponse
    {
        $film = Film::query()->findOrFail($id);
        if ($request->has('poster')) {
           Storage::delete($film->poster);
            $film->poster = $request->poster->store('public');
        }
        $film->fill($request->safe()->except('poster'));
        $film->save();
        return response()->json(
            $film );
    }

   public function destroy($id): JsonResponse
    {
        $sessions = Film::query()->where('id', (int)$id)->first()->sessions;
        foreach ($sessions as $session) {
            $session->delete();
        }
        $deleted = Film::query()->where('id', $id)->delete();

        if ($deleted) {
            return response()->json($id);
        }
        return response()->json('not ok');
    }

}
