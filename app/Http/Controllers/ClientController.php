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

        // �������� ���� � �������� �  ��������� ����
        $halls = Hall::query() ->where('opened', 1)->whereHas('sessions', function (Builder $query) use ($dateFormatted) {
            $query->whereDate('datetime', $dateFormatted);
        })->select('id', 'name')->get();

        // ������ � �������� ����� � ��������� ����
        $sessions = Session::query()->whereDate('datetime', $dateFormatted)->whereHas('hall', function (Builder $query) {
            $query->where('opened', 1);
        })->get();

        // ��� ������ � �������� �����  � ��������� ����
        $films = Film::all()->whereIn('id', $sessions->pluck('film_id'))->flatten();

        return response()->json( ["halls" => $halls, "sessions" => $sessions, "films" => $films]
        );
    }


    public function seatsAvailable(int $sessionId): JsonResponse
    {
        // ���������� � ������
        $session = Session::query()->where('sessions.id', $sessionId)
            ->leftJoin('halls', 'sessions.hall_id', '=', 'halls.id')
            ->leftJoin('films', 'sessions.film_id', '=', 'films.id')
            ->select('sessions.id', 'sessions.datetime', 'films.title', 'sessions.hall_id', 'halls.name', 'halls.row', 'halls.price_standard', 'halls.price_vip')->first();

        // ��������� �����
        $tickets = Seat::query()->has('tickets')->whereHas('tickets', function (Builder $query) use ($sessionId) {
            $query->where('session_id', $sessionId);
        })->get();

        // ��� ����� �� �����
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
