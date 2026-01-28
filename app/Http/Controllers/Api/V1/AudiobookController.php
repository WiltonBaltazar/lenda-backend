<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAudiobookRequest;
use App\Http\Requests\UpdateAudiobookRequest;
use App\Http\Resources\AudiobookResource;
use App\Http\Resources\AudiobookSerieResource;
use App\Models\Audiobook;
use App\Models\AudiobookSerie;
use Illuminate\Support\Facades\Auth;

class AudiobookController extends Controller
{

    public function index()
    {
        return AudiobookResource::collection(Audiobook::latest()->get());
    }
    // getLatestAudiobook

    public function getLatestAudiobook()
    {
        // get latest audiobook limit by 3 order by date desc and return as a collection
        return AudiobookResource::collection(Audiobook::latest()->limit(1)->get());
    }

    // display audiobook by slug a show it's chapters and other details
    public function showBySlug($slug)
    {
        $audiobook = Audiobook::where('slug', $slug)->firstOrFail();

        return (new AudiobookResource($audiobook));
    }

    /**
     * Get audiobook series by slug
     */
    public function getSeriesBySlug(string $slug)
    {
        $serie = AudiobookSerie::where('slug', $slug)->firstOrFail();

        return (new AudiobookSerieResource($serie));
    }

    /**
     * Get audiobooks by series slug. Guests receive only free audiobooks.
     */
    public function getBooksBySeriesSlug(string $slug)
    {
        $serie = AudiobookSerie::where('slug', $slug)->firstOrFail();

        // DEBUG: Check if we found the series
        // dd($serie->id, $serie->title); 

        $userAuthenticated = auth('sanctum')->check();

        $query = Audiobook::where('audiobook_serie_id', $serie->id)->latest();

        // DEBUG: See the raw SQL query to check for mistakes
        // dd($query->toSql(), $query->getBindings());

        $audiobooks = $query->get();

        // DEBUG: Count results
        // dd($audiobooks);

        return response()->json([
            'serie' => AudiobookSerieResource::make($serie),
            'audiobooks' => AudiobookResource::collection($audiobooks)
        ]);
    }
}
