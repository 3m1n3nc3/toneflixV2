@extends('backend.index')
@section('content')
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="{{ route('backend.dashboard') }}">Control Panel</a>
        </li>
        <li class="breadcrumb-item active"><a href="{{ url('/admin/songs') }}">Songs</a></li>
        <li class="breadcrumb-item active"> {!! $song->title !!} - @foreach($song->artists as $artist)<a href="{{ route('backend.artists.edit', ['id' => $artist->id]) }}" title="{!! $artist->name !!}">{!! $artist->name !!}</a>@if(!$loop->last), @endif @endforeach</li>
    </ol>
    <div class="row">
        <div class="col-lg-12 media-info mb-3 album">
            <div class="media mb-3">
                <img class="mr-3" src="{{ $song->artwork_url }}">
                <div class="media-body">
                    <h5 class="m-0">{!! $song->title !!} - @foreach($song->artists as $artist)<a href="{{ url('admin/artists/edit/' . $artist->id) }}" title="{!! $artist->name !!}">{!! $artist->name !!}</a>@if(!$loop->last), @endif @endforeach</h5>
                    <h5>
                        @if($song->mp3)
                            <span class="badge badge-pill badge-dark">MP3</span>
                        @endif
                        @if($song->hd)
                            <span class="badge badge-pill badge-danger">HD</span>
                        @endif
                        @if($song->hls)
                            <span class="badge badge-pill badge-warning">HLS</span>
                        @endif
                    </h5>
                    <p class="m-0"><a href="{{ $song->permalink_url }}" class="btn btn-warning" target="_blank">Preview @if(! $song->approved) (only Moderator) @endif</a></p>
                </div>
            </div>
        </div>
        <div class="col-lg-12 media-info mb-3 song">
            <iframe width="100%" height="60" frameborder="0" src="{{ asset('share/embed/dark/song/' . $song->id) }}"></iframe>
        </div>


        <div class="col-lg-12">
            <form role="form" action="" enctype="multipart/form-data" method="post">

                <div class="card">
                    <div class="card-header p-0 position-relative">
                        <ul class="nav">
                            <li class="nav-item"><a class="nav-link active" href="#overview" data-toggle="pill"><i class="fas fa-fw fa-newspaper"></i> Overview</a></li>
                            <li class="nav-item"><a href="#streamable" class="nav-link" data-toggle="pill"><i class="fas fa-fw fa-lock"></i> Advanced</a></li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content mt-2" id="myTabContent">
                            <div id="overview" class="tab-pane fade show active">
                                @csrf
                                <div class="form-group">
                                    <label>Track Name</label>
                                    <input class="form-control" name="title" value="{!! $song->title !!}" required>
                                </div>
                                <div class="form-group multi-artists">
                                    <label>Artist(s)</label>
                                    <select class="form-control multi-selector" data-ajax--url="{{ route('api.search.artist') }}" name="artistIds[]" multiple="">
                                        @foreach ($song->artists as $index => $artist)
                                            <option value="{{ $artist->id }}" selected="selected" data-artwork="{{ $artist->artwork_url }}" data-title="{!! $artist->name !!}">{!! $artist->name !!}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group select2-artwork">
                                    <label>Album(s)</label>
                                    <select class="form-control select-ajax" data-ajax--url="/api/search/album" name="albumIds[]">
                                        @if($song->album)
                                            <option value="{{ $song->album->id }}" selected="selected" data-artwork="{{ $song->album->artwork_url }}"  data-title="{{ $song->album->title }}">{{ $song->album->title }}</option>
                                        @endif
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Edit artwork</label>
                                    <div class="input-group col-xs-12">
                                        <input type="file" name="artwork" class="file-selector" accept="image/*">
                                        <span class="input-group-addon"><i class="fas fa-fw fa-image"></i></span>
                                        <input type="text" class="form-control input-lg" disabled placeholder="Upload Image">
                                        <span class="input-group-btn">
                                            <button class="browse btn btn-primary input-lg" type="button"><i class="fas fa-fw fa-file"></i> Browse</button>
                                        </span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Description</label>
                                    <textarea rows="4" class="form-control" name="description">{{ $song->description }}</textarea>
                                </div>
                                <div class="form-group">
                                    <label>Genres</label>
                                    <select multiple="" class="form-control select2-active" name="genre[]">
                                        {!! genreSelection(explode(',', $song->genre)) !!}
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Moods</label>
                                    <select multiple="" class="form-control select2-active" name="mood[]">
                                        {!! moodSelection(explode(',', $song->mood)) !!}
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Tags</label>
                                    {!! makeTagSelector('tags[]', isset($song) && ! old('tags') ? array_column($song->tags->toArray(), 'tag')  : old('tags')) !!}
                                </div>
                                <div class="form-group">
                                    <label>Released At</label>
                                    <input type="text" class="form-control datepicker" name="released_at" value="{{ \Carbon\Carbon::parse($song->released_at)->format('m/d/Y') }}" autocomplete="off">
                                </div>
                                <div class="form-group">
                                    <label>Copyright</label>
                                    <input type="text" class="form-control" name="copyright" value="{{ $song->copyright }}">
                                </div>
                                @if(isset($song->bpm))
                                    <div class="form-group">
                                        <label>BPM</label>
                                        <input type="text" class="form-control" name="bpm" value="{{ $song->bpm }}">
                                    </div>
                                @endif
                                <div class="form-group">
                                    <label>Youtube ID</label>
                                    <input type="text" class="form-control" name="youtube_id" value="{{ isset($song->log->youtube) ? $song->log->youtube : '' }}">
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-12">
                                        <label class="switch">
                                            {!! makeCheckBox('selling', $song->selling ) !!}
                                            <span class="slider round"></span>
                                        </label>
                                        <label class="pl-6 col-form-label">Allow to sell this song</label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Price</label>
                                    <input type="text" class="form-control" name="price" value="{{ $song->price }}">
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-12">
                                        <label class="switch">
                                            {!! makeCheckBox('allow_comments', $song->allow_comments ) !!}
                                            <span class="slider round"></span>
                                        </label>
                                        <label class="pl-6 col-form-label">Allow to comment</label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-12">
                                        <label class="switch">
                                            {!! makeCheckBox('approved', $song->approved ) !!}
                                            <span class="slider round"></span>
                                        </label>
                                        <label class="pl-6 col-form-label">Approve this song</label>
                                    </div>
                                </div>
                            </div>
                            <div id="streamable" class="tab-pane fade">
                                <div class="alert alert-info">Note: You can configure additional song playable and downloadable parameters for different groups in this section.</div>
                                @if(cache()->has('usergroup'))
                                    @foreach(cache()->get('usergroup') as $group)
                                        <div class="form-group row">
                                            <label class="col-sm-3 col-form-label">{{ $group->name }}</label>
                                            <div class="col-sm-9">
                                                {!! makeDropDown([
                                                        0 => 'Group Settings',
                                                        1 => 'Playable',
                                                        2 => 'Playable And Downloadable',
                                                        3 => 'Play And Download Denied'
                                                    ], 'group_extra[' . $group->id . ']', isset($options) && isset($options[$group->id]) ? $options[$group->id] : 0)
                                                !!}
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                        <input type="hidden" name="file_id" value="{{ $song->file_id }}">
                        <button type="submit" class="btn btn-primary">Save</button>
                        @if(! $song->approved)
                            <button type="button" class="btn btn-danger" data-toggle="collapse" data-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">Reject</button>
                        @endif
                        <button type="reset" class="btn btn-secondary">Reset</button>
                        <a class="btn btn-danger"  href="{{ route('backend.songs.delete', ['id' => $song->id]) }}" onclick="return confirm('Are you sure want to delete this song?')" data-toggle="tooltip" data-placement="left" title="Delete this song"><i class="fas fa-fw fa-trash"></i></a>
                    </div>
                </div>
            </form>
        </div>
        <div class="col-lg-12">
            <div class="mt-5 collapse" id="collapseExample">
                <form role="form" method="post" action="{{ route('backend.songs.edit.reject.post', ['id' => $song->id]) }}" enctype="multipart/form-data">
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