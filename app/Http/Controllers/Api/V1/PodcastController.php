<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Podcast;
use App\Http\Controllers\Controller;
use App\Http\Resources\EpisodeResource;
use App\Http\Resources\PodcastResource;
use App\Http\Requests\StorePodcastRequest;
use App\Http\Requests\UpdatePodcastRequest;

class PodcastController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //return episodes from podcast with name os rumores da lenda
        //return the latest episode from podcast with name os rumores da lenda limit by 1
        return PodcastResource::collection(Podcast::where('name', 'Rumores da Lenda')->with('episodes')->limit(1)->get());
    }

    public function getEpisodesForSlider()
    {
        // return the latest 3 episodes from podcast with name os rumores da lenda

        //get episodes where the podcast name is os rumores da lenda and order them from newest to oldest
        $podcast = Podcast::where('name', 'Rumores da Lenda')
            ->with(['episodes' => function ($query) {
                $query->latest('created_at')
                    ->offset(1)
                    ->limit(3);
            }])
            ->first();

        return EpisodeResource::collection($podcast->episodes);
    }

    //get the latest podcast episode from the podcast with name os rumores da lenda
    public function getLatestPodcastEpisode()
    {
        $podcast = Podcast::where('name', 'Rumores da Lenda')
            ->with(['episodes' => function ($query) {
                $query->latest('created_at')->limit(1); // Get the latest episode
            }])
            ->first();

        return EpisodeResource::collection($podcast->episodes);
    }

    // public function getRumoresDaLenda(): JsonResponse
    // {
    //     // Find the category by name
    //     $podcast = Podcast::where('name', 'Rumores da Lenda')
    //         ->with(['episodes' => function ($query) {
    //             $query->orderBy('release_date', 'desc');
    //         }])
    //         ->firstOrFail();

    //     return response()->json([
    //         'podcast' => $podcast->name,
    //         'episodes' => EpisodeResource::collection($podcast->episodes)
    //     ]);
    // }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePodcastRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Podcast $categoryPodcast)
    {
        //show the category episode

        return new PodcastResource($categoryPodcast);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePodcastRequest $request, Podcast $categoryPodcast)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Podcast $categoryPodcast)
    {
        //
    }
}
