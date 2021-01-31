@extends('backend.index')
@section('content')
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="{{ route('backend.dashboard') }}">Control Panel</a>
        </li>
        <li class="breadcrumb-item"><a href="{{ route('backend.albums') }}">Albums</a></li>
        <li class="breadcrumb-item active"><a href="{{ route('backend.albums.edit', ['id' => $album->id]) }}">{!! $album->title !!}</a> - @foreach($album->artists as $artist)<a href="{{ route('backend.artists.edit', ['id' => $artist->id]) }}" title="{!! $artist->name !!}">{!! $artist->name !!}</a>@if(!$loop->last), @endif @endforeach</li>
    </ol>
    <div class="row">
        <div class="col-lg-12 media-info mb-3 album">
            <div class="media mb-3">
                <img class="mr-3" src="{{ $album->artwork_url }}">
                <div class="media-body">
                    <h5 class="m-0">{!! $album->title !!} - @foreach($album->artists as $artist)<a href="{{ route('backend.artists.edit', ['id' => $artist->id]) }}" title="{!! $artist->name !!}">{!! $artist->name !!}</a>@if(!$loop->last), @endif @endforeach</h5>
                    <p>Songs: {{ $album->song_count }}</p>
                    <p class="m-0"><a href="{{ $album->permalink_url }}" class="btn btn-warning" target="_blank">Preview @if(! $album->approved) (only Moderator) @endif</a> <a href="{{ route('backend.albums.tracklist', ['id' => $album->id]) }}" class="btn btn-info">Tracks List</a> <a href="{{ route('backend.albums.upload', ['id' => $album->id]) }}" class="btn btn-success">Upload</a></p>
                </div>
            </div>
        </div>
        <div class="col-lg-12">
            <form role="form" method="post" action="" enctype="multipart/form-data">
                @csrf
                <div class="form-group multi-artists">
                    <label>Artists</label>
                    <select class="form-control multi-selector" data-ajax--url="{{ route('api.search.artist') }}" name="artistIds[]" multiple="">
                        @foreach ($album->artists as $index => $artist)
                            <option value="{{ $artist->id }}" selected="selected" data-artwork="{{ $artist->artwork_url }}" data-title="{!! $artist->name !!}">{!! $artist->name !!}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Album Name</label>
                    <input name="name" class="form-control" value="{!! $album->title !!}" required>
                </div>
                <div class="form-group">
                    <label>Artwork</label>
                    <div class="input-group col-xs-12">
                        <input type="file" name="artwork" class="file-selector" accept="image/*">
                        <span class="input-group-addon"><i class="fas fa-fw fa-image"></i></span>
                        <input type="text" class="form-control input-lg" disabled placeholder="Upload Image">
                        <span class="input-group-btn"><button class="browse btn btn-primary input-lg" type="button"><i class="fas fa-fw fa-file"></i> Browse</button></span>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-12">
                        <label class="switch">
                            {!! makeCheckBox('update-song-artwork') !!}
                            <span class="slider round"></span>
                        </label>
                        <label class="pl-6 col-form-label">Also update artwork for all songs in this album</label>
                    </div>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea class="form-control" rows="3" name="description">{{ $album->description }}</textarea>
                </div>
                <div class="form-group">
                    <label>Genre(s)</label>
                    <select multiple="" class="form-control select2-active" name="genre[]">
                        {!! genreSelection(explode(',', $album->genre)) !!}
                    </select>
                </div>
                <div class="form-group">
                    <label>Mood(s)</label>
                    <select multiple="" class="form-control select2-active" name="mood[]">
                        {!! moodSelection(explode(',', $album->mood)) !!}
                    </select>
                </div>
                <div class="form-group">
                    <label>Copyright</label>
                    <input type="text" class="form-control" name="copyright" value="{{ $album->copyright }}">
                </div>
                <div class="form-group">
                    <label>Released At</label>
                    <input type="text" class="form-control datepicker" name="released_at" value="{{ \Carbon\Carbon::parse($album->released_at)->format('m/d/Y') }}" autocomplete="off">
                </div>
                <div class="form-group">
                    <label>Schedule Publish</label>
                    <input type="text" class="form-control datepicker" name="created_at" value="{{ \Carbon\Carbon::parse($album->created_at)->format('m/d/Y') }}" autocomplete="off">
                </div>
                <div class="form-group">
                    <div class="col-sm-12">
                        <label class="switch">
                            {!! makeCheckBox('selling', $album->selling ) !!}
                            <span class="slider round"></span>
                        </label>
                        <label class="pl-6 col-form-label">Allow to sell this album</label>
                    </div>
                </div>
                <div class="form-group">
                    <label>Price</label>
                    <input type="text" class="form-control" name="price" value="{{ $album->price }}">
                </div>
                <div class="form-group">
                    <div class="col-sm-12">
                        <label class="switch">
                            {!! makeCheckBox('approved', $album->approved ) !!}
                            <span class="slider round"></span>
                        </label>
                        <label class="pl-6 col-form-label">Approve this album</label>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Save</button>
                @if(! $album->approved)
                    <button type="button" class="btn btn-danger" data-toggle="collapse" data-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">Reject</button>
                @endif
            </form>
            <div class="mt-5 collapse" id="collapseExample">
                <form role="form" method="post" action="{{ route('backend.albums.edit.reject.post', ['id' => $album->id]) }}" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label>Comment</label>
                        <textarea class="form-control" rows="3" name="comment"></textarea>
                    </div>
                    <button type="submit" class="btn btn-warning">Reject & Send Email to the artist</button>
                </form>
            </div>
        </div>
    </div>
@endsection