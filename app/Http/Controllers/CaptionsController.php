<?php

namespace App\Http\Controllers;



use Illuminate\Database\Eloquent\Builder;
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
    protected string $query;
    protected array $collection;


    public function __construct(Request $request)
    {
        if ($request->filled('query')) {
            $this->page = $request->input('page', 1);
            $this->resultsPerPage = $request->input('resultsPerPage', 10);
            $this->query = $request->input('query');
        } else {
            throw new \Exception('No query');
        }
    }
    private function render($collection)
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

    public function search(Request $request)
    {
        $query = $request->input('query');
        
        if(Cache::has($query)) {
            return $this->render(Cache::get($query));
        }

        $collection = Caption::search($query)
            ->orderBy('id')
            ->orderBy('start')
            ->get();

        $collection = $collection->groupBy('video_id');

        $newCollection = new Collection();

        $collection->map(function ($index, $value) use (&$collection, &$newCollection) {
            $newArray = [
                "captions" => $collection[$value]->all(),
                "video" => Video::where('video_id', $value)->first()
            ];
            $newCollection->put($value, $newArray);
        });

        Cache::put($query, $newCollection);

        return $this->render($newCollection);
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
