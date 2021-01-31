<?php
/**
 * Created by NiNaCoder.
 * Date: 2019-05-28
 * Time: 15:13
 */

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;

class AdController extends Controller
{
    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;

    }
    public function audio()
    {
        $ad = Banner::where('type', 1)->inRandomOrder()->first();

        if(!isset($ad->id)) {
            abort(500, 'No ad to serve.');
        }

        $ad->stream_url = $ad->getFirstMediaUrl('file');

        return response()->json($ad);
    }
}