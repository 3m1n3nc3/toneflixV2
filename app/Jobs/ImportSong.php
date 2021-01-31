<?php

namespace App\Jobs;

use App\Models\AlbumSong;
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

class ImportSong implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $id;
    protected $album_id;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($id, $album_id)
    {
        $this->id = $id;
        $this->album_id = $album_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if(! \App\Models\SongLog::where('song_id', $this->id)->exists()) {
            $data = Spotify::track($this->id)->get();
            $song = new Song();
            $song->title = $data['name'];

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

            $song->artistIds = implode(',', $artists);

            $song->explicit = boolval($data['explicit']);

            if(isset($data['album']['images'][1])) {
                $song->addMediaFromUrl($data['album']['images'][1]['url'])
                    ->usingFileName(time(). '.jpg')
                    ->toMediaCollection('artwork', config('settings.storage_artwork_location', 'public'));
            }

            $song->save();

            DB::table('album_songs')->insert(
                ['song_id' => $song->id, 'album_id' => $this->album_id]
            );

            DB::table('song_spotify_logs')->insertOrIgnore([
                [
                    'spotify_id' => $data['id'],
                    'song_id' => $song->id,
                    'artwork_url' => isset($data['album']['images'][1]) ? $data['album']['images'][1]['url'] : null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
            ]);
        }
    }
}
