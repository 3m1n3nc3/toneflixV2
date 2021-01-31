<?php

namespace App\Jobs;

use App\Models\Album;
use App\Models\Artist;
use App\Models\Song;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Spotify;
use DB;

class ImportAlbum implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $id;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($id)
    {
        $this->id = $id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if(! \App\Models\AlbumLog::where('album_id', $this->id)->exists()) {
            $data = Spotify::album($this->id)->get();

            $album = new Album();
            $album->title = $data['name'];
            foreach ($data['artists'] as $artist_item) {
                $row = Artist::where('name', $artist_item['name'])->first();
                if (isset($row->id)) {
                    $artists[] = $row->id;
                } else {
                    $artist = new Artist();
                    $artist->name = $artist_item['name'];
                    $artist->save();
                    $artists[] = $artist->id;

                }
            }

            $album->released_at = Carbon::parse($data['release_date']);
            $album->artistIds = implode(',', $artists);
            $album->approved = 0;

            if(isset($data['images'][1]['url'])) {
                $album->addMediaFromUrl($data['images'][1]['url'])
                    ->usingFileName(time(). '.jpg')
                    ->toMediaCollection('artwork', config('settings.storage_artwork_location', 'public'));

            }

            $album->save();

            DB::table('album_spotify_logs')->insertOrIgnore([
                [
                    'spotify_id' => $data['id'],
                    'album_id' => $album->id,
                    'artwork_url' => $data['images'][1]['url'],
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
            ]);


            foreach ($data['tracks']['items'] as $item) {
                dispatch(new ImportSong($item['id'], $album->id));
            }

        }
    }
}
