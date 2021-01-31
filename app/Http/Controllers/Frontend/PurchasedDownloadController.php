<?php
/**
 * Created by NiNaCoder.
 * Date: 2019-05-28
 * Time: 15:44
 */

namespace App\Http\Controllers\Frontend;

use Illuminate\Http\Request;
use App\Models\Song;
use App\Models\Order;
use App\Models\Download;
use App\Models\Role;

class PurchasedDownloadController
{
    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function song()
    {
        if(Order::where('orderable_type', (new Song)->getMorphClass())->where('user_id', auth()->user()->id)->exists()) {
            $song = Song::withoutGlobalScopes()->findOrFail($this->request->route('id'));
            $format = $this->request->route('format');
            if($format == 'mp3' && $song->mp3) {
                $file = new Download (
                    $song->getFirstMedia('hd_audio') ? $song->getFirstMedia('hd_audio') : $song->getFirstMedia('audio'),
                    $song->title . '.mp3',
                    intval(Role::getValue('option_download_resume')),
                    intval(Role::getValue('option_download_speed'))
                );
                $song->increment('download_count');
                session_write_close();
                $file->downloadFile();
                die();
            }
        } else {
            abort(403, 'You have to buy the song before download it.');
        }
    }
}