<?php
/**
 * Created by NiNaCoder.
 * Date: 2019-05-25
 * Time: 21:22
 */

namespace App\Models;

use App\Traits\SanitizedRequest;
use Illuminate\Database\Eloquent\Model;
use DB;

class Channel extends Model
{
    use SanitizedRequest;

    protected $appends = ['objects'];

    public function getObjectsAttribute($value)
    {
        if ($this->attributes['object_type'] == "artist") {
            if($this->attributes['object_ids'])
                return Artist::whereIn('id', explode(',', $this->attributes['object_ids']))->orderBy(DB::raw('FIELD(id, ' .  $this->attributes['object_ids']. ')', 'FIELD'))->paginate(20);
            else
                return Artist::latest()->paginate(20);
        } elseif ($this->attributes['object_type'] == "song") {
            if($this->attributes['object_ids'])
                return Song::whereIn('id', explode(',', $this->attributes['object_ids']))->orderBy(DB::raw('FIELD(id, ' .  $this->attributes['object_ids']. ')', 'FIELD'))->paginate(20);
            else
                return Song::latest()->paginate(20);
        } elseif ($this->attributes['object_type'] == "playlist") {
            if($this->attributes['object_ids'])
                return Playlist::whereIn('id', explode(',', $this->attributes['object_ids']))->orderBy(DB::raw('FIELD(id, ' .  $this->attributes['object_ids']. ')', 'FIELD'))->paginate(20);
            else
                return Playlist::latest()->paginate(20);
        } elseif ($this->attributes['object_type'] == "album") {
            if($this->attributes['object_ids'])
                return Album::whereIn('id', explode(',', $this->attributes['object_ids']))->orderBy(DB::raw('FIELD(id, ' .  $this->attributes['object_ids']. ')', 'FIELD'))->paginate(20);
            else
                return Album::latest()->paginate(20);
        } elseif ($this->attributes['object_type'] == "station") {
            if($this->attributes['object_ids'])
                return Station::whereIn('id', explode(',', $this->attributes['object_ids']))->orderBy(DB::raw('FIELD(id, ' .  $this->attributes['object_ids']. ')', 'FIELD'))->paginate(20);
            else
                return Station::latest()->paginate(20);
        } elseif ($this->attributes['object_type'] == "user") {
            if($this->attributes['object_ids'])
                return User::whereIn('id', explode(',', $this->attributes['object_ids']))->orderBy(DB::raw('FIELD(id, ' .  $this->attributes['object_ids']. ')', 'FIELD'))->paginate(20);
            else
                return User::latest()->paginate(20);
        } elseif ($this->attributes['object_type'] == "podcast") {
            if($this->attributes['object_ids'])
                return Podcast::with('artist')->whereIn('id', explode(',', $this->attributes['object_ids']))->orderBy(DB::raw('FIELD(id, ' .  $this->attributes['object_ids']. ')', 'FIELD'))->paginate(20);
            else
                return Podcast::with('artist')->latest()->paginate(20);
        }
    }

}