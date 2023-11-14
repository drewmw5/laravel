<?php

namespace App\Http\Controllers;



use Illuminate\Http\Response;
use App\Models\Caption;
use App\Models\Video;
use App\Events\VideoUpdate;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;


class CaptionsController extends Controller
{
    protected int $page;
    protected int $resultsPerPage;
    protected array $query;
    protected string $queryId;
    protected array $collection;


    public function __construct(Request $request)
    {
        if ($request->filled('query')) {
            $this->page = $request->input('page', 1);
            $this->resultsPerPage = $request->input('resultsPerPage', 10);
            $this->query = explode(' ', $request->input('query'));
            $this->queryId = implode('.', $this->query);
        } else {
            throw new \Exception('No query');
        }
    }


    private function render(Collection $collection)
    {
        return Inertia::render('Welcome', [
            'canLogin' => Route::has('login'),
            'canRegister' => Route::has('register'),
            'results' => $collection->forPage((int)$this->page, $this->resultsPerPage),
            'query' => $this->query,
            'page' => (int)$this->page,
            'pageCount' => ceil($collection->count() / $this->resultsPerPage),
        ]);
    }

    private function getCaptions(): Collection
    {
        $caption = Caption::query();

        foreach($this->query as $key => $value) {
            $caption->orWhere('text', 'LIKE', '%' . $value . '%');
        }

        $caption
        ->orderBy('video_id')
        ->orderBy('start');

        return $caption->get();

    }

    private function addQueriedCaptionToResults(Collection $captions): void
    {
        foreach ($captions as $caption) {
            $videoId = $caption->video_id;
            $this->collection[$videoId]['captions'][]['queriedCaption'] = $caption;
        }
    }

    private function addPrevAndNextCaptionsToResults()
    {
        foreach ($this->collection as $videoId => $videos) {
            [$this->collection[$videoId]['video']] = Video::where('video_id', '=', $videoId)
                ->get();

            foreach ($this->collection[$videoId]['captions'] as $index => $caption) {
                $caption = $caption['queriedCaption'];
                $nextCaption = Caption::where('video_id', '=', $caption->video_id)
                    ->where('start', '>', $caption->start)
                    ->orderBy('start', 'ASC')
                    ->first();

                $prevCaption = Caption::where('video_id', '=', $caption->video_id)
                    ->where('start', '<', $caption->start)
                    ->orderBy('start', 'DESC')
                    ->first();

                $this->collection[$videoId]['captions'][$index]['prevCaption'] = $prevCaption;
                $this->collection[$videoId]['captions'][$index]['nextCaption'] = $nextCaption;
            }
        }
    }

    private function returnCollection()
    {
        $collection = collect($this->collection);

        Cache::put($this->queryId, $collection, 900);

        return $this->render($collection);
    }

    public function search(Request $request)
    {
        if (Cache::has($this->queryId)) {
            return $this->render(Cache::get($this->queryId));
        } else {

        $captions = $this->getCaptions();

        if (!$captions->count() > 0) {
            throw new \Exception("No Captions Found");
        }

        $this->addQueriedCaptionToResults($captions);
        $this->addPrevAndNextCaptionsToResults();

        return $this->returnCollection();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Caption  $captions
     * @return \Illuminate\Http\Response
     */
    public function show(Caption $captions, Request $request)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Caption  $captions
     * @return \Illuminate\Http\Response
     */
    public function edit(Caption $captions)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Models\Caption  $captions
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Caption $captions)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Caption  $captions
     * @return \Illuminate\Http\Response
     */
    public function destroy(Caption $captions)
    {
        //
    }
}
