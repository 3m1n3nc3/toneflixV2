<?php
/**
 * Created by NiNaCoder.
 * Date: 2019-05-24
 * Time: 20:12
 */

namespace App\Http\Controllers\Backend;

use App\Jobs\ImportArtist;
use App\Models\Email;
use App\Models\SongLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Auth;
use View;
use App\Models\Song;
use App\Models\Album;
use Storage;
use Image;
use App\ModelFilters\SongFilter;
use Spotify;

class ImportController
{
    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function index(Request $request)
    {
        $view = View::make('backend.import.index');

        if($this->request->input('type') == 'song') {
            $data = Spotify::searchTracks($this->request->input('term'))->get();
        } else if($this->request->input('type') == 'album') {
            $data = Spotify::searchAlbums($this->request->input('term'))->get();
        } else if($this->request->input('type') == 'artist') {
            $data = Spotify::searchArtists($this->request->input('term'))->get();
        }

        if(isset($data)) {
            $view->with('data', $data)->with('type', $this->request->input('type'));
        }

        return $view;
    }

    public function massAction()
    {
        $this->request->validate([
            'action' => 'required|string',
            'type' => 'required|string',
            'ids' => 'required|array',
        ]);

        if($this->request->input('action') == 'import') {

            $ids = $this->request->input('ids');
            foreach($ids as $id) {
                dispatch(new ImportArtist($id));
            }

            die('success');

            return redirect()->route('backend.import')->with('status', 'success')->with('message', 'Successfully added to import queue!');
        }
    }
}