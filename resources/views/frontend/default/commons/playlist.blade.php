@foreach ($playlists as $index => $playlist)
    <script>var playlist_data_{{ $playlist->id }} = {!! json_encode($playlist) !!}</script>
    @if($element == "carousel")
        <div class="module module-cell playlist block swiper-slide draggable" data-toggle="contextmenu" data-trigger="right" data-type="playlist" data-id="{{ $playlist->id }}">
            <div class="img-container" data-type="playlist" data-id="{{ $playlist->id }}">
                <img class="img" src="{{ $playlist->artwork_url }}">
                <a class="overlay-link" href="{{$playlist->permalink_url}}"></a>
                <div class="actions primary">
                    <a class="btn play play-lg play-scale play-object" data-type="playlist" data-id="{{ $playlist->id }}">
                        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" height="24" width="24"><path d="M8 5v14l11-7z"></path><path d="M0 0h24v24H0z" fill="none"></path></svg>
                    </a>
                </div>
            </div>
            <div class="module-inner">
                <a class="title" href="{{ $playlist->permalink_url }}" title="{!! $playlist->title !!}">{!! $playlist->title !!}</a>
                @if(isset($playlist->user))
                    <span class="byline">by <a href="{{ route('frontend.user', ['username' => $playlist->user->username]) }}" class="playlist-link" title="{{ $playlist->user->name }}">{{ $playlist->user->name }}</a></span>
                @endif
            </div>
        </div>
    @elseif($element == "search")
        <div class="module module-row tall playlist grid-item" data-toggle="contextmenu" data-trigger="right" data-type="playlist" data-id="{{ $playlist->id }}" data-index="{{ $index }}">
            <div class="img-container">
                <img class="img" src="{{ $playlist->artwork_url }}" alt="{!! $playlist->title !!}"></div>
            <div class="metadata playlist">
                <a href="{{ $playlist->permalink_url }}" class="title playlist-link" data-playlist-id="8991588">{!! $playlist->title !!}</a>
                @if(isset($playlist->user))
                    <div class="meta-inner">
                        <span data-translate-text="BY">by <a href="{{ route('frontend.user', ['username' => $playlist->user->name]) }}" class="meta-text">{{ $playlist->user->name }}</a></span>
                    </div>
                @endif
            </div>
            <div class="row-actions secondary">
                @if(isset($playlist->user) && (! auth()->check() || auth()->check() && auth()->user()->id != $playlist->user->id))
                    <a class="btn btn-favorite favorite @if($playlist->favorite) on @endif" data-type="playlist" data-id="{{ $playlist->id }}" data-title="{!! $playlist->title !!}" data-url="{{ $playlist->permalink_url }}" data-text-on="{{ __('web.PLAYLIST_UNSUBSCRIBE') }}" data-text-off="{{ __('web.PLAYLIST_SUBSCRIBE') }}">
                        <svg class="off" height="26" viewBox="0 0 24 24" width="18" xmlns="http://www.w3.org/2000/svg"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/><path d="M0 0h24v24H0z" fill="none"/></svg>
                        <svg class="on" height="26" viewBox="0 0 24 24" width="18" xmlns="http://www.w3.org/2000/svg"><path d="M0 0h24v24H0z" fill="none"/><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                        @if($playlist->favorite)
                            <span class="label desktop" data-translate-text="PLAYLIST_UNSUBSCRIBE">{{ __('web.PLAYLIST_UNSUBSCRIBE') }}</span>
                        @else
                            <span class="label desktop" data-translate-text="PLAYLIST_SUBSCRIBE"> {{ __('web.PLAYLIST_SUBSCRIBE') }} </span>
                        @endif
                    </a>
                @endif
            </div>
        </div>
    @elseif($element == "activity")
        @if (count($playlists) > 1)
        <a href="{{ $playlist->permalink_url }}" class="feed-item-img show-playlist-tooltip small playlist-link " data-toggle="contextmenu" data-trigger="right" data-type="playlist" data-id="{{ $playlist->id }}">
            <img src="{{ $playlist->artwork_url }}" class="row-feed-image">
        </a>
        @else
        <div class="feed-item">
            <a href="{{ $playlist->permalink_url }}" class="feed-item-img show-playlist-tooltip playlist-link " data-toggle="contextmenu" data-trigger="right" data-type="playlist" data-id="{{ $playlist->id }}">
                <img class="feed-img-medium" src="{{ $playlist->artwork_url }}" width="80" height="80">
            </a>
            <div class="inner">
                <a href="{{ $playlist->permalink_url }}" class="item-title playlist-link">{!! $playlist->title !!}</a>
                @if(isset($playlist->user))
                    <a href="{{ route('frontend.user', ['username' => $playlist->user->username]) }}" class="item-subtitle artist-link">{{ $playlist->user->name }}</a>
                @endif
                <a class="btn play play-object" data-type="playlist" data-id="{{ $playlist->id }}">
                    <svg height="26" viewBox="0 0 24 24" width="18" xmlns="http://www.w3.org/2000/svg"><path d="M8 5v14l11-7z"/><path d="M0 0h24v24H0z" fill="none"/></svg>
                    <span data-translate-text="PLAY_PLAYLIST">{{ __('web.PLAY_PLAYLIST') }}</span>
                </a>
            </div>
        </div>
        @endif
    @else
        <div class="module module-cell playlist small grid-item">
            <div class="img-container">
                <img class="img" src="{{ $playlist->artwork_url }}" alt="{!! $playlist->title !!}"/>
                <div class="actions primary">
                    <a class="btn btn-secondary btn-icon-only btn-options" data-toggle="contextmenu" data-trigger="left" data-type="playlist" data-id="{{ $playlist->id }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M0 0h24v24H0z" fill="none"/><path d="M6 10c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm12 0c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm-6 0c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/></svg>
                    </a>
                    <a class="btn btn-secondary btn-icon-only btn-rounded btn-play play-or-add play-object" data-type="playlist" data-id="{{ $playlist->id }}">
                        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" height="26" width="20"><path d="M8 5v14l11-7z"></path><path d="M0 0h24v24H0z" fill="none"></path></svg>
                    </a>
                    <a class="btn btn-favorite favorite @if($playlist->favorite) on @endif" data-type="playlist" data-id="{{ $playlist->id }}" data-title="{!! $playlist->title !!}" data-url="{{ $playlist->permalink_url }}">
                        <svg class="off" height="26" viewBox="0 0 24 24" width="18" xmlns="http://www.w3.org/2000/svg"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/><path d="M0 0h24v24H0z" fill="none"/></svg>
                        <svg class="on" height="26" viewBox="0 0 24 24" width="18" xmlns="http://www.w3.org/2000/svg"><path d="M0 0h24v24H0z" fill="none"/><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                    </a>
                </div>
            </div>
            <div class="module-inner playlist">
                <a href="{{ $playlist->permalink_url }}" class="headline title">{!! $playlist->title !!}</a>
                @if(isset($playlist->user))
                    <span class="byline">by <a href="{{ route('frontend.user', ['username' => $playlist->user->username]) }}" class="secondary-text">{{ $playlist->user->name }}</a></span>
                @endif
            </div>
        </div>
    @endif
@endforeach