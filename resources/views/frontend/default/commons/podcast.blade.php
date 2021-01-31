@foreach ($podcasts as $index => $podcast)
    <script>var podcast_data_{{ $podcast->id }} = {!! json_encode($podcast) !!}</script>
    @if($element == "carousel")
        <div class="module module-cell playlist block swiper-slide draggable" data-toggle="contextmenu" data-trigger="right" data-type="podcast" data-id="{{ $podcast->id }}">
            <div class="img-container">
                <img class="img" src="{{ $podcast->artwork_url }}">
                <a class="overlay-link" href="{{$podcast->permalink_url}}"></a>
                <div class="actions primary">
                    <a class="btn play play-lg play-scale play-object" data-type="podcast" data-id="{{ $podcast->id }}">
                        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" height="24" width="24"><path d="M8 5v14l11-7z"></path><path d="M0 0h24v24H0z" fill="none"></path></svg>
                    </a>
                </div>
            </div>
            <div class="module-inner">
                <a href="{{ $podcast->permalink_url }}" class="podcast-link title">{!! $podcast->title !!}</a>
                @if(isset($podcast->artist))
                    <span class="byline">by <a href="{{$podcast->artist->permalink_url}}" class="artist-link artist" title="{{ $podcast->artist->name }}">{{$podcast->artist->name}}</a></span>
                @endif
            </div>
        </div>
    @elseif($element == "search")
        <div class="module module-row tall album" data-index="{{ $index }}">
            <div class="img-container">
                <img class="img" src="{{ $podcast->artwork_url }}" alt="{!! $podcast->title !!}">
            </div>
            <div class="metadata album">
                <a href="{{ $podcast->permalink_url }}" class="title podcast-link">{!! $podcast->title !!}</a>
                <div class="meta-inner">
                    @if(isset($podcast->artist))
                        <span class="byline">by <a href="{{$podcast->artist->permalink_url}}" class="artist-link artist" title="{{ $podcast->artist->name }}">{{$podcast->artist->name}}</a></span>
                    @endif
                </div>
            </div>
        </div>
    @elseif($element == "grid")
        <div class="module module-cell grid-item">
            <div class="img-container">
                <img class="img" src="{{ $podcast->artwork_url }}" alt="{!! $podcast->title !!}">
                <a class="overlay-link" href="{{ $podcast->permalink_url }}"></a>
                <div class="actions primary">
                    <a class="btn btn-secondary btn-icon-only btn-options" data-toggle="contextmenu" data-trigger="left" data-type="podcast" data-id="{{ $podcast->id }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M0 0h24v24H0z" fill="none"/><path d="M6 10c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm12 0c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm-6 0c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/></svg>
                    </a>
                    <a class="btn btn-secondary btn-icon-only btn-rounded btn-play play-or-add play-object" data-type="podcast" data-id="{{ $podcast->id }}">
                        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" height="26" width="20"><path d="M8 5v14l11-7z"></path><path d="M0 0h24v24H0z" fill="none"></path></svg>
                    </a>
                </div>
            </div>
            <div class="module-inner podcast">
                <a href="{{ $podcast->permalink_url }}" class="headline title">{{ $podcast->title }}</a>
                @if(isset($podcast->artist))
                    <span class="byline">by <a href="{{$podcast->artist->permalink_url}}" class="artist-link artist" title="{{ $podcast->artist->name }}">{{$podcast->artist->name}}</a></span>
                @endif
            </div>
        </div>
    @elseif($element == "genre")
        <div class="module module-row station tall" data-index="{{ $index }}">
            <div class="img-container">
                <img class="img" src="{{$podcast->artwork_url}}" alt="{{$podcast->title}}">
                <a class="overlay-link" href="{{ $podcast->permalink_url }}"></a>
                <div class="row-actions primary">
                    <a class="btn play-lg play-object" data-type="podcast" data-id="{{ $podcast->id }}">
                        <svg class="icon-play" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/><path d="M0 0h24v24H0z" fill="none"/></svg>
                    </a>
                </div>
            </div>
            <div class="metadata station">
                <div class="title">
                    <a href="{{ $podcast->permalink_url }}">{{ $podcast->title }}</a>
                </div>
                <div class="description">
                    @if(isset($podcast->artist))
                        <span class="byline">by <a href="{{$podcast->artist->permalink_url}}" class="artist-link artist" title="{{ $podcast->artist->name }}">{{$podcast->artist->name}}</a></span>
                    @endif
                </div>
            </div>
        </div>
    @elseif($element == "activity")
        @if (count($podcasts) > 1)
            <a href="{{ $podcast->permalink_url }}" class="feed-item-img small " data-toggle="contextmenu" data-trigger="right" data-type="podcast" data-id="{{ $podcast->id }}">
                <img src="{{ $podcast->artwork_url }}" class="row-feed-image">
            </a>
        @else
            <div class="feed-item">
                <a href="{{ $podcast->permalink_url }}" class="feed-item-img " data-toggle="contextmenu" data-trigger="right" data-type="playlist" data-id="{{ $podcast->id }}">
                    <img class="feed-img-medium" src="{{ $podcast->artwork_url }}" width="80" height="80">
                </a>
                <div class="inner">
                    <a href="{{ $podcast->permalink_url }}" class="item-title podcast-link">{{ $podcast->title }}</a>
                    @if(isset($podcast->artist))
                        <a href="{{$podcast->artist->permalink_url}}" class="item-subtitle artist-link" title="{{ $podcast->artist->name }}">{{$podcast->artist->name}}</a>
                    @endif
                    <a class="btn play play-object" data-type="podcast" data-id="{{ $podcast->id }}">
                        <svg height="26" viewBox="0 0 24 24" width="18" xmlns="http://www.w3.org/2000/svg"><path d="M8 5v14l11-7z"/><path d="M0 0h24v24H0z" fill="none"/></svg>
                        <span data-translate-text="PLAY_ALBUM">Play Album</span>
                    </a>
                </div>
            </div>
        @endif
    @endif
@endforeach