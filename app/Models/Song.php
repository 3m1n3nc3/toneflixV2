<?php
/**
 * Created by NiNaCoder.
 * Date: 2019-05-24
 * Time: 13:24
 */

namespace App\Models;

use App\Scopes\ApprovedScope;
use App\Scopes\PublishedScope;
use App\Scopes\VisibilityScope;
use App\Traits\SanitizedRequest;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Support\Facades\URL;
use DB;
use Auth;
use Module;

class Song extends Model implements HasMedia
{
    use InteractsWithMedia, SanitizedRequest;

    protected $casts = [
        'released_at' => 'datetime:m/d/Y',
    ];

    protected $table = 'songs';

    protected $fillable = [
        'title', 'genre', 'mood', 'album_id', 'artworkId', 'releasedOn', 'copyright', 'approve'
    ];

    protected $appends = ['artwork_url', 'artists', 'permalink_url', 'stream_url', 'favorite', 'library', 'streamable'];

    protected $hidden = ['media', 'artistIds', 'description', 'user_id', 'user'];

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new VisibilityScope);
        static::addGlobalScope(new ApprovedScope);
        static::addGlobalScope(new PublishedScope);
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('sm')
            ->width(60)
            ->height(60)
            ->performOnCollections('artwork')->nonOptimized()->nonQueued();

        $this->addMediaConversion('md')
            ->width(120)
            ->height(120)
            ->performOnCollections('artwork')->nonOptimized()->nonQueued();

        $this->addMediaConversion('lg')
            ->width(300)
            ->height(300)
            ->performOnCollections('artwork')->nonOptimized()->nonQueued();
    }

    public function getArtworkUrlAttribute($value)
    {
        $media = $this->getFirstMedia('artwork');
        if(! $media) {
            if(config('settings.automate')) {
                $row = \App\Models\SongLog::where('song_id', $this->attributes['id'])->first();
                if(isset($row->id)) {
                    return $row->artwork_url;
                } else {
                    return asset( 'common/default/song.png');
                }
            } else {
                return asset( 'common/default/song.png');
            }
        } else {
            if($media->disk == 's3') {
                return $media->getTemporaryUrl(Carbon::now()->addMinutes(intval(config('settings.s3_signed_time', 5))),'lg');
            } else {
                return $media->getFullUrl('lg');
            }
        }
    }

    public function getArtistsAttribute()
    {
        return $this->attributes['artistIds'] ? Artist::whereIn('id', explode(',', $this->attributes['artistIds']))->orderBy(DB::raw('FIELD(id, ' .  $this->attributes['artistIds']. ')', 'FIELD'))->get() : array();
    }

    public function getMoodsAttribute()
    {
        $this->attributes['moods'] = Mood::whereIn('id', explode(',', $this->attributes['mood']))->limit(4)->get();
        return $this->attributes['moods'];
    }

    public function getGenresAttribute($value)
    {
        $this->attributes['genres'] = Genre::whereIn('id', explode(',', $this->attributes['genre']))->limit(4)->get();
        return $this->attributes['genres'];
    }

    public function getMinutesAttribute($value)
    {

        $this->attributes['minutes'] = date('i:s', $this->attributes['duration']);

        return $this->attributes['minutes'];
    }

    public function getPermalinkUrlAttribute($value)
    {
        return route('frontend.song', ['id' => $this->attributes['id'], 'slug' => str_slug($this->attributes['title']) ? str_slug($this->attributes['title']) : str_replace(' ', '-', $this->attributes['title'])]);
    }

    public function getStreamAbleAttribute($value)
    {
        if(isset($this->attributes['access']))
        {
            $options = groupPermission($this->attributes['access']);
            if($this->attributes['access'] && isset($options[Role::groupId()])) {
                $permission = $options[Role::groupId()];
                switch ($permission) {
                    case 1:
                        return true;
                        break;
                    case 2:
                        return true;
                        break;
                    case 3:
                        return false;
                        break;
                }
            }
        }

        return Role::getValue('option_stream') ? true : false;
    }

    public function getStreamUrlAttribute($value)
    {
        if(! Role::getValue('option_stream') ) {
            $options = array();

            if(isset($this->attributes['access'])) {
                $options = groupPermission($this->attributes['access']);
            }

            if(isset($this->attributes['access']) && $this->attributes['access'] && isset($options[Role::groupId()])) {

                $permission = $options[Role::groupId()];
                switch ($permission) {
                    case 1:
                        if(isset($this->attributes['hls']) && $this->attributes['hls']) {
                            return route('frontend.song.stream.hls', ['id' => $this->attributes['id']]);
                        } else {
                            return URL::temporarySignedRoute('frontend.song.stream.mp3', now()->addDay(), [
                                'id' => $this->attributes['id']
                            ]);
                        }
                        break;
                    case 2:
                        if(isset($this->attributes['hls']) && $this->attributes['hls']) {
                            return route('frontend.song.stream.hls', ['id' => $this->attributes['id']]);
                        } else {
                            return URL::temporarySignedRoute('frontend.song.stream.mp3', now()->addDay(), [
                                'id' => $this->attributes['id']
                            ]);
                        }
                        break;
                    case 3:
                        return false;
                        break;
                }
            } else {
                if(isset($this->attributes['preview']) && $this->attributes['preview']) {
                    return $this->getFirstMediaUrl('preview');
                } else {
                    return false;
                }
            }
        } else {
            if(isset($this->attributes['hls']) && $this->attributes['hls']) {
                return route('frontend.song.stream.hls', ['id' => $this->attributes['id']]);
            } else {
                return URL::temporarySignedRoute('frontend.song.stream.mp3', now()->addDay(), [
                    'id' => $this->attributes['id']
                ]);
            }
        }
    }

    public function getFavoriteAttribute($value) {
        if(auth()->check()){
            return Love::where('user_id', auth()->user()->id)->where('loveable_id', $this->id)->where('loveable_type', $this->getMorphClass())->exists();
        } else {
            return false;
        }
    }

    public function getPurchasedAttribute($value) {
        if(auth()->check() && $this->selling){
            return Order::where('user_id', auth()->user()->id)->where('orderable_id', $this->id)->where('orderable_type', $this->getMorphClass())->exists();
        } else {
            return false;
        }
    }

    public function getLibraryAttribute($value) {
        if(auth()->check()){
            return Collection::where('user_id', auth()->user()->id)->where('collectionable_id', $this->id)->where('collectionable_type', $this->getMorphClass())->exists();
        } else {
            return false;
        }
    }

    public function getAlbumAttribute()
    {
        return Album::withoutGlobalScopes()->leftJoin('album_songs', 'album_songs.album_id', '=', 'albums.id')
            ->select('albums.*', 'album_songs.id AS host_id')
            ->where('album_songs.song_id', '=', $this->id)
            ->first();
    }

    public function getSalesAttribute()
    {
        return Order::groupBy('amount')->where('orderable_type', $this->getMorphClass())->where('orderable_id', $this->id)->count();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function log()
    {
        return $this->hasOne(SongLog::class);
    }

    public function tags()
    {
        return $this->hasMany(SongTag::class);
    }

    public function delete()
    {
        DB::table('playlist_songs')->where('song_id', $this->id)->delete();
        DB::table('album_songs')->where('song_id', $this->id)->delete();
        Comment::where('commentable_type', $this->getMorphClass())->where('commentable_id', $this->id)->delete();
        Love::where('loveable_type', $this->getMorphClass())->where('loveable_id', $this->id)->delete();
        Notification::where('notificationable_type', $this->getMorphClass())->where('notificationable_id', $this->id)->delete();
        Activity::where('activityable_type', $this->getMorphClass())->where('activityable_id', $this->id)->delete();
        Report::where('reportable_type', $this->getMorphClass())->where('reportable_id', $this->id)->delete();

        return parent::delete();
    }
}

