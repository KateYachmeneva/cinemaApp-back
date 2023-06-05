<?php

namespace App\Http\Controllers;

use App\Models\Film;
use App\Models\Hall;
use App\Models\Seat;
use App\Models\Session;
use DateTime;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;

class ClientController extends Controller
{
    /**
     * All films with their schedule.
     *
     * @return array
     */
    public function scheduleAvailable(string $datetime):JsonResponse
    {
        $dateFormatted = DateTime::createFromFormat('Y-m-d', $datetime)->format('Y-m-d');

        // Îòêğûòûå çàëû ñ ñåàíñàìè â  óêàçàííóş äàòó
        $halls = Hall::query() ->where('opened', 1)->whereHas('sessions', function (Builder $query) use ($dateFormatted) {
            $query->whereDate('datetime', $dateFormatted);
        })->select('id', 'name')->get();

        // Ñåàíñû â îòêğûòûõ çàëàõ â óêàçàííóş äàòó
        $sessions = Session::query()->whereDate('datetime', $dateFormatted)->whereHas('hall', function (Builder $query) {
            $query->where('opened', 1);
        })->get();

        // Âñå ôèëüìû â îòêğûòûõ çàëàõ  â óêàçàííóş äàòó
        $films = Film::all()->whereIn('id', $sessions->pluck('film_id'))->flatten();

        return response()->json( ["halls" => $halls, "sessions" => $sessions, "films" => $films]
        );
    }


    public function seatsAvailable(int $sessionId): JsonResponse
    {
        // Èíôîğìàöèÿ î ñåàíñå
        $session = Session::query()->where('sessions.id', $sessionId)
            ->leftJoin('halls', 'sessions.hall_id', '=', 'halls.id')
            ->leftJoin('films', 'sessions.film_id', '=', 'films.id')
            ->select('sessions.id', 'sessions.datetime', 'films.title', 'sessions.hall_id', 'halls.name', 'halls.row', 'halls.price_standard', 'halls.price_vip')->first();

        // Êóïëåííûå ìåñòà
        $tickets = Seat::query()->has('tickets')->whereHas('tickets', function (Builder $query) use ($sessionId) {
            $query->where('session_id', $sessionId);
        })->get();

        // Âñå ìåñòà íà ñåàíñ
        $seats = Seat::query()->where('hall_id', $session->hall_id)->select('id', 'number', 'status')->get();
        foreach ($seats as $seat) {
            if ($tickets->contains($seat)) {
                $seat->status = 'sold';
            }
        }

        return response() ->json( ["session" => $session, "seats" => $seats]
        );
    }
}
