<?php
/**
 * Created by NiNaCoder.
 * Date: 2019-07-06
 * Time: 15:50
 */

namespace App\Http\Controllers\Frontend;

use Illuminate\Http\Request;
use App\Models\Station;
use View;
use App\Models\Comment;
use App\Models\Genre;

class StationController
{
    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function index()
    {
        $station = Station::findOrFail($this->request->route('id'));

        if( $this->request->is('api*') )
        {
            if($this->request->get('callback'))
            {
                $station->artists = [['name' => __('web.LIVE')]];

                return response()->jsonp($this->request->get('callback'), [$station])->header('Content-Type', 'application/javascript');
            }

            return response()->json($station);
        }

        $station->setRelation('related', Station::where('category', 'REGEXP', '(^|,)(' . $station->category . ')(,|$)')->where('id', '!=', $station->id)->paginate(5));

        $view = View::make('station.index')->with('station', $station);

        if ($this->request->ajax()) {
            $sections = $view->renderSections();
            return $sections['content'];
        }

        getMetatags($station);

        return $view;
    }

    public function report()
    {
        $station = Station::findOrFail($this->request->input('id'));
        $station->increment('failed_count');
        return response()->json(['success' => true]);
    }

    public function played()
    {
        $station = Station::findOrFail($this->request->input('id'));
        $station->increment('play_count');
        return response()->json(['success' => true]);
    }
}