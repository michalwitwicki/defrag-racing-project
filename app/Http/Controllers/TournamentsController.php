<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Tournament;
use App\Models\Organizer;
use App\Models\Record;
use App\Models\Round;
use Carbon\Carbon;

class TournamentsController extends Controller {
    public function index(Request $request) {
        if (! $request->user()) {
            $records = 0;
        } else {
            $records = Record::where('user_id', $request->user()->id)->count();
        }

        $tournaments = Tournament::query()
            ->where('published', true)
            ->orWhere('creator', $request->user()?->id)
            ->with('rounds')
            ->get();

        $activeTournaments = Tournament::whereHas('rounds', function ($query) {
            $query->where('start_date', '<=', Carbon::now())
                ->where('end_date', '>=', Carbon::now());
        })
        ->where('published', true)
        ->orWhere('creator', $request->user()?->id)
        ->with('rounds')
        ->get();

        $upcomingTournaments = Tournament::whereHas('rounds', function ($query) {
            $query->where('start_date', '>', Carbon::now());
        })
        ->where('published', true)
        ->orWhere('creator', $request->user()?->id)
        ->with('rounds')
        ->get();

        $pastTournaments = Tournament::whereDoesntHave('rounds', function ($query) {
            $query->where('end_date', '>', Carbon::now());
        })
        ->where('published', true)
        ->orWhere('creator', $request->user()?->id)
        ->with('rounds')
        ->get();

        return Inertia::render('Tournaments/Index')
            ->with('tournaments', $tournaments)
            ->with('activeTournaments', $activeTournaments)
            ->with('upcomingTournaments', $upcomingTournaments)
            ->with('pastTournaments', $pastTournaments)
            ->with('records', $records);
    }

    public function show(Tournament $tournament, Request $request) {
        $tournament->isOrganizer = $tournament->isOrganizer($request->user()?->id);
        $tournament->isValidator = $tournament->isValidator($request->user()?->id);

        $latestNews = $tournament->news()
            ->orderBy('created_at', 'desc')
            ->with('round')
            ->limit(2)
            ->get();
        
        $organizers = Organizer::query()
            ->where('tournament_id', $tournament->id)
            ->with('user:id,name,plain_name,country')
            ->get()
            ->groupBy('role');

        $tournament->load('streamers.user:id,name,plain_name,country');

        $tournament->load('relatedTournaments.tournament');

        return Inertia::render('Tournaments/Tournament/Overview')
            ->with('tournament', $tournament)
            ->with('organizers', $organizers)
            ->with('latestNews', $latestNews);
    }

    public function rules(Tournament $tournament, Request $request) {
        return Inertia::render('Tournaments/Tournament/Rules')
            ->with('tournament', $tournament);
    }

    public function donations(Tournament $tournament, Request $request) {
        $tournament->load('donations.user:id,name,plain_name,country');

        return Inertia::render('Tournaments/Tournament/Donations')
            ->with('tournament', $tournament);
    }

    public function faqs(Tournament $tournament, Request $request) {
        $tournament->load('faqs');

        return Inertia::render('Tournaments/Tournament/Faqs')
            ->with('tournament', $tournament);
    }

    public function delete(Tournament $tournament, Request $request) {
        if ($tournament->creator !== $request->user()?->id) {
            return back()->withDanger('You are not the admin of the tournament !!');
        }

        return Inertia::render('Tournaments/Tournament/Delete')
            ->with('tournament', $tournament);
    }

    public function destroy(Tournament $tournament, Request $request) {
        $request->validate([
            'name' => 'required|in:'.$tournament->name,
        ]);

        if ($tournament->creator !== $request->user()?->id) {
            return back()->withDanger('You are not the admin of the tournament !!');
        }
        
        $tournament->delete();

        return redirect('/tournaments')->withSuccess('Tournament deleted successfully !!');
    }
}
