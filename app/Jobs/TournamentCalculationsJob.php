<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use \App\Models\Round;
use \App\Models\TournamentNew;


class TournamentCalculationsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $force;

    /**
     * Create a new job instance.
     */
    public function __construct($force = false)
    {
        $this->force = $force;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->calculateNews();

        $this->calculateRounds();
    }

    public function calculateRounds() {
        $rounds = Round::where('published', true)
            ->where('end_date', '<', now());

        if (! $this->force) {
            $rounds = $rounds->where('results', false);
        }

        $rounds = $rounds->get();

        foreach ($rounds as $round) {
            $round->calculateResults();
        }
    }

    public function calculateNews() {
        $rounds = Round::where('published', true)->get();

        foreach ($rounds as $round) {
            if ($round->start_date < now()) {
                $news = TournamentNew::where('round_id', $round->id)
                    ->where('type', 'round_start')
                    ->exists();

                if (! $news) {
                    $news = new TournamentNew();
                    $news->tournament_id = $round->tournament_id;
                    $news->round_id = $round->id;
                    $news->title = $round->name;
                    $news->content = $round->mapname;
                    $news->type = 'round_start';
                    $news->save();
                }
            }

            if ($round->end_date < now()) {
                $news = TournamentNew::where('round_id', $round->id)
                    ->where('type', 'round_end')
                    ->exists();

                if (! $news) {
                    $news = new TournamentNew();
                    $news->tournament_id = $round->tournament_id;
                    $news->round_id = $round->id;
                    $news->title = $round->name;
                    $news->content = $round->mapname;
                    $news->type = 'round_end';
                    $news->save();
                }
            }
        }
    }
}
