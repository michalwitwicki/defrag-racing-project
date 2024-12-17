<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

use App\Models\PlayerRating;

class RankingController extends Controller
{
    const PAGINATION_LIMIT = 50;
    const ACTIVE_PLAYERS_MONTHS = 3;

    public function index(Request $request) {

        // get VQ3 and CPM ratings
        $gametype = $request->input('gametype', 'run');
        $rankingtype = $request->input('rankingtype', 'active_players');

        $vq3Ratings = PlayerRating::query();
        $cpmRatings = PlayerRating::query();

        if ($rankingtype === 'active_players') {
            $vq3Ratings->where('last_activity', '>=', now()->subMonths(ACTIVE_PLAYERS_MONTHS));
            $cpmRatings->where('last_activity', '>=', now()->subMonths(ACTIVE_PLAYERS_MONTHS));
        }

        $vq3Ratings = $vq3Ratings
            ->with('user')
            ->where('physics', 'vq3')
            ->where('mode', $gametype);

        $cpmRatings = $cpmRatings
            ->with('user')
            ->where('physics', 'cpm')
            ->where('mode', $gametype);

        $vq3Ratings = $this->addCategoryRank($vq3Ratings);
        $cpmRatings = $this->addCategoryRank($cpmRatings);

        $vq3Ratings = $vq3Ratings
            ->orderBy('player_rating', 'DESC')
            ->paginate(PAGINATION_LIMIT, ['*'], 'vq3Page')
            ->withQueryString();

        $cpmRatings = $cpmRatings
            ->orderBy('player_rating', 'DESC')
            ->paginate(PAGINATION_LIMIT, ['*'], 'cpmPage')
            ->withQueryString();

        // get VQ3 and CPM ratings for the current user
        if ($request->user() && $request->user()->mdd_id) {
            $myVq3Rating = PlayerRating::where('mdd_id', $request->user()->mdd_id)
                ->where('physics', 'vq3')
                ->where('mode', $gametype)
                ->with('user')
                ->first();

            $myCpmRating = PlayerRating::where('mdd_id', $request->user()->mdd_id)
                ->where('physics', 'cpm')
                ->where('mode', $gametype)
                ->with('user')
                ->first();
        } else {
            $myVq3Rating = null;
            $myCpmRating = null;
        }

        // handle pagination
        $cpmPage = ($request->has('cpmPage')) ? min($request->cpmPage, $cpmRatings->lastPage()) : 1;
        $vq3Page = ($request->has('vq3Page')) ? min($request->vq3Page, $vq3Ratings->lastPage()) : 1;

        if ($request->has('vq3Page') && $request->get('vq3Page') > $vq3Ratings->lastPage()) {
            return redirect()->route('ranking', ['vq3Page' => $vq3Ratings->lastPage()]);
        }

        if ($request->has('cpmPage') && $request->get('cpmPage') > $cpmRatings->lastPage()) {
            return redirect()->route('ranking', ['cpmPage' => $cpmRatings->lastPage()]);
        }

        // render the view
        return Inertia::render('RankingView')
            ->with('vq3Ratings', $vq3Ratings)
            ->with('cpmRatings', $cpmRatings)
            ->with('myVq3Rating', $myVq3Rating)
            ->with('myCpmRating', $myCpmRating);
    }

    protected function addCategoryRank($query)
    {
        $query = $query->toBase(); // Convert Eloquent builder to query builder

        return DB::table(DB::raw("({$query->toSql()}) as sub"))
            ->mergeBindings($query)
            ->addSelect('*')
            ->addSelect(DB::raw('
                DENSE_RANK()
                OVER (PARTITION BY physics, mode ORDER BY player_rating DESC)
                AS category_rank
            '));
    }
}
