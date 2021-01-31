(function (factory) {
    if (typeof define === 'function' && define.amd) {
        define(['jquery'], factory);
    } else if (typeof exports === 'object') {
        factory(require('jquery'));
    } else {
        factory(jQuery);
    }
})(function ($) {
    "use strict";

    $.enginePlayMedia = {
        hd: false,
        video: true,
        radio: false,
        radioStationData: null,
        playNowOrNext: function (last, a, forcePlay) {
            forcePlay = typeof forcePlay !== 'undefined' ? forcePlay : false;
            var object_type = a.data("type");
            var object_id = a.data("id");
            if (object_type === "playlist" || object_type === "album" || object_type === "artist" || object_type === "profile") {
                $.enginePlayMedia.getObjectSongs(last, object_type, object_id, forcePlay);
            } else if (object_type === "song") {
                var song = window['song_data_' + object_id];
                Playlist.playSongsNowOrNext([$.engineUtils.toPlayerJson(song)], true);
            } else if (object_type === "station") {
                var station = window['station_data_' + object_id];
                Playlist.playLiveRadioStation([$.engineUtils.stationToPlayerJson(station)], true);
            } else if (object_type === "episode") {
                var episode = window['episode_data_' + object_id];
                Playlist.playSongsNowOrNext([$.engineUtils.episodeToPlayerJson(episode)], true);
            } else if (object_type === "user") {
                var user = window['user_data_' + object_id];
                $.enginePlayMedia.playUserCollectionSongs(user.id)
            } else if (object_type === "podcast") {
                var podcast = window['podcast_data_' + object_id];
                $.enginePlayMedia.playLatestEpisode(podcast.id)
            }
            __DEV__ && console.log(object_type, object_id);
        },
        addSongsToPlayer: function (last, songs, forcePlay) {
            if (songs.length) {
                var num = songs.length;
                var playerSongsArray = [];
                for (var i = 0; i < num; i++) {
                    var song = $.engineUtils.toPlayerJson(songs[i]);
                    playerSongsArray.push(song);
                }
                __DEV__ && console.log(playerSongsArray);
                last ? Playlist.playSongsLast(playerSongsArray) : Playlist.playSongsNowOrNext(playerSongsArray, forcePlay);
            }
        },
        getObjectSongs: function (last, object_type, object_id, forcePlay) {
            forcePlay = typeof forcePlay !== 'undefined' ? forcePlay : false;
            $.ajax({
                type: "get",
                url: route.route('frontend.' + object_type, {id: object_id, slug: 'music-engine'}),
                dataType: 'json',
                success: function (response) {
                    var songs = [];
                    if (object_type === "album") songs = response.songs;
                    if (object_type === "playlist") songs = response.songs;
                    if (object_type === "artist") songs = response.songs;
                    if (object_type === "profile") songs = response.songs;
                    $.enginePlayMedia.addSongsToPlayer(last, songs, forcePlay);
                }
            });
        },
        playUserRecentSongs: function (user_id) {
            $.ajax({
                type: "get", url: route.route('api.user.recent', {id: user_id}),
                success: function (response) {
                    if (response && response.songs) {
                        $.enginePlayMedia.addSongsToPlayer(false, response.songs.data, true);
                    }
                }
            });
        },
        playUserCollectionSongs: function (user_id) {
            $.ajax({
                type: "get", url: route.route('api.user.collection', {id: user_id}),
                success: function (response) {
                    if (response && response.songs) {
                        $.enginePlayMedia.addSongsToPlayer(false, response.songs.data, true);
                    }
                }
            });
        },
        playUserFavoritesSongs: function (user_id) {
            $.ajax({
                type: "get", url: route.route('api.user.favorites', {id: user_id}),
                success: function (response) {
                    if (response && response.songs) {
                        $.enginePlayMedia.addSongsToPlayer(false, response.songs.data, true);
                    }
                }
            });
        },
        playLatestEpisode: function (id) {
            $.ajax({
                type: "get", url: route.route('api.podcast', {id: id}),
                success: function (response) {
                    if (response && response.episodes) {
                        Playlist.playSongsNowOrNext([$.engineUtils.episodeToPlayerJson(response.episodes[0])], true);
                    }
                }
            });
        },
        Station: function (a) {
            if (EMBED.Playlist.length) {
                $.engineLightBox.show("lightbox-radioClearQueue");
                $(".radioClearQueue").find(".submit").one('click', function () {
                    $.engineLightBox.hide();
                    EMBED.Player.clearQueue();
                    $.enginePlayMedia.radio = true;
                    $.enginePlayMedia.radioStationData = {
                        type: a.data('type'),
                        id: a.data('id')
                    };
                    $('body').addClass("embed-radio-on");
                    $.enginePlayMedia.insertSongToRadioStation();
                });
            } else {
                $.enginePlayMedia.radio = true;
                $.enginePlayMedia.radioStationData = {
                    type: a.data('type'),
                    id: a.data('id')
                };
                $('body').addClass("embed-radio-on");
                $.enginePlayMedia.insertSongToRadioStation();
            }
        },
        insertSongToRadioStation: function () {
            //check if last song
            if ((EMBED.Playlist.length - 1) === EMBED.Player.queueNumber) {
                $.enginePlayMedia.getSongDataForRadio();
            } else {
                if (!EMBED.Playlist.length) {
                    __DEV__ && console.log("currently no song in queue, add then play right away");
                    $.enginePlayMedia.getSongDataForRadio();
                    setTimeout(function () {
                        EMBED.Player.playAt(0);
                    }, 2000);

                }
            }
        },
        getSongDataForRadio: function () {
            var type = $.enginePlayMedia.radioStationData.type;
            var id = $.enginePlayMedia.radioStationData.id;
            setTimeout(function () {
                $.ajax({
                    data: {
                        type: type,
                        id: id,
                        recent_songs: EMBED.Playlist.map(function (song) {
                            return song.id
                        }).join(",")
                    },
                    type: "post",
                    url: route.route('frontend.song.autoplay.get'),
                    success: function (response) {
                        if (response && response.id) {
                            Playlist.playSongsLast([$.engineUtils.toPlayerJson(response)])
                        } else {
                            Toast.show("error", Language.text.PLAYLIST_NO_SONGS);
                        }
                    }
                });
            }, 1000);

        }

    };
});

(function (factory) {
    if (typeof define === 'function' && define.amd) {
        define(['jquery'], factory);
    } else if (typeof exports === 'object') {
        factory(require('jquery'));
    } else {
        factory(jQuery);
    }
})(function ($) {
    "use strict";
    
    window.Playlist = {
        liveRadio: false,
        renderPlayerForLiveRadio: function () {
            $('#embed_bottom_player').addClass('embed_live_radio');
        },
        renderPlayerSong: function () {
            $('#embed_bottom_player').removeClass('embed_live_radio');
        },
        playSongsNowOrNext: function (songs, forcePlay) {
            forcePlay = typeof forcePlay !== 'undefined' ? forcePlay : false;
            if(! songs[0].streamable && ! songs[0].preview) {
                if(User.isLogged()) {
                    $.engineLightBox.show("lightbox-vipOnlyFeature");
                } else {
                    User.SignIn.show();
                }
                return false;
            }
            if (Playlist.liveRadio) {
                EMBED.Player.clearQueue();
                setTimeout(function () {
                    Playlist.liveRadio = false;
                    EMBED.CanShowQueue = true;
                    Playlist.renderPlayerSong();
                    EMBED.Player.addSongsNextPlaying(songs, forcePlay);
                    Toast.show("queue", songs.length === 1 ? Language.text.POPUP_QUEUE_SONG_ADDED.replace(':numSongs', songs.length) : Language.text.POPUP_QUEUE_SONGS_ADDED.replace(':numSongs', songs.length));
                }, 1000);
            } else {
                Playlist.liveRadio = false;
                EMBED.CanShowQueue = true;
                Playlist.renderPlayerSong();
                EMBED.Player.addSongsNextPlaying(songs, forcePlay);
                Toast.show("queue", songs.length === 1 ? Language.text.POPUP_QUEUE_SONG_ADDED.replace(':numSongs', songs.length) : Language.text.POPUP_QUEUE_SONGS_ADDED.replace(':numSongs', songs.length));
            }
        },
        playSongsLast: function (songs) {
            if (Playlist.liveRadio) {
                EMBED.Player.clearQueue();
                setTimeout(function () {
                    Playlist.liveRadio = false;
                    EMBED.CanShowQueue = true;
                    Playlist.renderPlayerSong();
                    EMBED.Player.addSongLastQueue(songs);
                    !$.enginePlayMedia.radio && Toast.show("queue", songs.length === 1 ? Language.text.POPUP_QUEUE_SONG_ADDED.replace(':numSongs', songs.length) : Language.text.POPUP_QUEUE_SONGS_ADDED.replace(':numSongs', songs.length));
                }, 1000);
            } else {
                Playlist.liveRadio = false;
                EMBED.CanShowQueue = true;
                Playlist.renderPlayerSong();
                EMBED.Player.addSongLastQueue(songs);
                !$.enginePlayMedia.radio && Toast.show("queue", songs.length === 1 ? Language.text.POPUP_QUEUE_SONG_ADDED.replace(':numSongs', songs.length) : Language.text.POPUP_QUEUE_SONGS_ADDED.replace(':numSongs', songs.length));
            }
        },
        playLiveRadioStation: function (songs, forcePlay) {
            forcePlay = typeof forcePlay !== 'undefined' ? forcePlay : false;
            if (EMBED.Playlist.length && !Playlist.liveRadio) {
                $.engineLightBox.show("lightbox-radioClearQueue");
                $(".radioClearQueue").find(".submit").one('click', function () {
                    Playlist.liveRadio = true;
                    EMBED.CanShowQueue = false;
                    $.engineLightBox.hide();
                    EMBED.Player.clearQueue();
                    setTimeout(function () {
                        Playlist.renderPlayerForLiveRadio();
                        EMBED.Player.addSongsNextPlaying(songs, forcePlay);
                        $('body').removeClass('embed_queue_open');
                        Toast.show("radio", "Starting radio station.", "Radio");
                    }, 1000);
                });
            } else {
                Playlist.liveRadio = true;
                EMBED.CanShowQueue = false;
                Playlist.renderPlayerForLiveRadio();
                EMBED.Player.addSongsNextPlaying(songs, forcePlay);
                $('body').removeClass('embed_queue_open');
                Toast.show("radio", "Starting radio station.", "Radio");
            }

        },
    };

    $.engineAudioAd = {
        adPosition: 0,
        isPlaying: false,
        frequency: 3,
        shouldLoop: true,
        currentLoop: 0,
        ready: false,
        audio: null,
        createElement: function(){
            if(! $.engineAudioAd.ready) {
                var adEl = document.createElement('audio');
                adEl.id = 'ad-player';
                adEl.type = 'audio/mpeg';
                adEl.muted = true;
                $('body').append(adEl);
                $('<div/>', {
                    id: 'embed_audio_ad_title',
                }).appendTo('#embed_audio_ad');
                $('<div/>', {
                    id: 'embed_audio_progress',
                }).appendTo('#embed_audio_ad');
                $('<div/>', {
                    id: 'embed_audio_ad_skip',
                }).html('Skip').appendTo('#embed_audio_ad');
                $.engineAudioAd.ready = true;
                setTimeout(function () {
                    $.engineAudioAd.audio = document.getElementById('ad-player');
                    $.engineAudioAd.audio.addEventListener("ended", $.engineAudioAd.Player.ended, false);
                    $.engineAudioAd.audio.addEventListener("error", $.engineAudioAd.Player.error, false);
                    $.engineAudioAd.audio.addEventListener("playing", $.engineAudioAd.Player.playing, false);
                    $.engineAudioAd.audio.addEventListener("pause", $.engineAudioAd.Player.pause, false);
                    $.engineAudioAd.audio.addEventListener("waiting", $.engineAudioAd.Player.waiting, false);
                    $.engineAudioAd.audio.addEventListener("timeupdate", $.engineAudioAd.Player.timeupdate, false);
                    $.engineAudioAd.audio.addEventListener("canplay", $.engineAudioAd.Player.canplay, false);
                }, 500);
            }
        },
        Player: {
            ended: function () {
                $.engineAudioAd.stopAd();
            },
            error: function () {

            },
            playing: function () {
                $('body').addClass('embed_audio_ad');
                $('body').removeClass('embed_queue_open');
                $('#embed_bottom_player').addClass('init');
            },
            pause: function () {

            },
            waiting: function () {

            },
            timeupdate: function () {
                try {
                    var percentage = Math.round((this.currentTime / this.duration) * 100);
                    $('#embed_audio_progress').css('width', percentage + '%');
                } catch(e){}
            },
            canplay: function () {

            }
        },
        stopAd: function () {
            $.engineAudioAd.audio.pause();
            $.engineAudioAd.audio.muted = true;
            $('body').removeClass('embed_audio_ad');
            $.engineAudioAd.audio.currentTime = 0;
            setTimeout(function () {
                EMBED.Player.playPause();
            }, 500);
        },
        init: function () {
            $.engineAudioAd.createElement();
            $('#embed_audio_ad_skip').on('click', function () {
                $.engineAudioAd.stopAd();
            });
            console.log('Ad starting');
            EMBED.Event.add(window, "embedQueueChanged", function () {
                console.log('CURRENT LOOP----' + $.engineAudioAd.currentLoop);
                if($.engineAudioAd.shouldLoop) {
                    $.engineAudioAd.currentLoop++;
                    $.engineAudioAd.shouldLoop = false;
                    setTimeout(function () {
                        $.engineAudioAd.shouldLoop = true;
                    }, 2000)
                }
                if($.engineAudioAd.currentLoop > $.engineAudioAd.frequency) {
                    $.engineAudioAd.currentLoop = 0;
                    $.ajax({
                        type: 'POST',
                        url: route.route('frontend.ad.audio'),
                        cache: true,
                        success: function (response) {
                            EMBED.Player.playPause();
                            $('#embed_audio_ad_title').html(response.description);
                            $('#ad-player').attr('src', response.stream_url);
                            $.engineAudioAd.audio.play();
                            $.engineAudioAd.audio.muted = false;
                        },
                        error: function () {

                        }
                    });
                }
            });
        }
    };

    $(document).ready(function () {
        if($('body').hasClass('media-ad-enabled')) {
            $.engineAudioAd.init();
        }

        $('body').addClass('embed_video_on');
        EMBED.Event.add(window, "embedPlayerEventErrorFired", function () {
            if ((EMBED.Playlist.length - 1) === EMBED.Player.queueNumber) {
                Toast.show("error", Language.text.ERROR_PLAYING_SONG);
            } else {
                Toast.show("error", Language.text.ERROR_HASNEXT_MESSAGE);
            }
            if (Playlist.liveRadio) {
                //Report to admin when player failed
                $.post(route.route('frontend.station.report'), {'id': EMBED.Playlist[EMBED.Player.queueNumber].id}, function (data) {
                });
            }
        });
        EMBED.Event.add(window, "embedQueueHasBeenClear", function () {
            $.enginePlayMedia.radio = false;
            $('body').removeClass("embed-radio-on")
        });
        var initialPlay = false;
        EMBED.Event.add(window, "embedQueueChanged", function () {
            if (initialPlay) return;
            initialPlay = true;
            setTimeout(function () {
                initialPlay = false;
            }, 2000);
            setTimeout(function () {
                if (User.isLogged() && EMBED.Playlist.length && !Playlist.liveRadio) {
                        EMBED.Playlist[EMBED.Player.queueNumber].id !== undefined && $.post(route.route('frontend.song.stream.track.played'), {'id': EMBED.Playlist[EMBED.Player.queueNumber].id, type: EMBED.Playlist[EMBED.Player.queueNumber].type}, function (data) {
                    });
                } else if (Playlist.liveRadio) {
                    $.post(route.route('frontend.station.played'), {'id': EMBED.Playlist[EMBED.Player.queueNumber].id}, function (data) {
                    });
                }
            }, 1000);

        });

        EMBED.Event.add(window, "embedQueueChanged", function () {
            setTimeout(function () {
                if (!EMBED.Playlist.length) {
                    return false;
                }
                if (!Playlist.liveRadio) {
                    var song = EMBED.Playlist[EMBED.Player.queueNumber];
                    $('.play-object[data-type="song"]')
                        .removeAttr('data-current')
                        .removeAttr('data-pause')
                        .removeAttr('data-playing')
                        .removeAttr('data-waiting');
                    $('.play-object[data-type="song"][data-id="' + song.id + '"]')
                        .attr('data-current', 'true')
                        .attr('data-waiting', 'true');
                } else {
                    var station = EMBED.Playlist[EMBED.Player.queueNumber];
                    $('.play-object[data-type="station"]')
                        .removeAttr('data-current')
                        .removeAttr('data-pause')
                        .removeAttr('data-playing')
                        .removeAttr('data-waiting');
                    $('.play-object[data-type="station"][data-id="' + station.id + '"]')
                        .attr('data-current', 'true')
                        .attr('data-waiting', 'true');
                }

                if (!Playlist.liveRadio && $.enginePlayMedia.radio) {
                    $.enginePlayMedia.insertSongToRadioStation($.enginePlayMedia.data);
                }
            }, 200);
            //Update queue song label

            $('#queue-menu-btn-label').html(Language.text.QUEUE_CURRENT_LABEL ? Language.text.QUEUE_CURRENT_LABEL.replace(':current', (EMBED.Player.queueNumber + 1) + '/' + EMBED.Playlist.length) : ('Queue :current songs').replace(':current', (EMBED.Player.queueNumber + 1) + '/' + EMBED.Playlist.length));
        });

        EMBED.Event.add(window, "embedPlayerEventPlayingFired", function () {
            if (!Playlist.liveRadio) {
                var song = EMBED.Playlist[EMBED.Player.queueNumber];
                var el = $('.play-object[data-type="song"][data-id="' + song.id + '"]');
                el.removeAttr('data-pause').removeAttr('data-waiting');
                el.attr('data-playing', 'true');
            } else {
                var station = EMBED.Playlist[EMBED.Player.queueNumber];
                var el = $('.play-object[data-type="station"][data-id="' + station.id + '"]');
                el.removeAttr('data-pause').removeAttr('data-waiting');
                el.attr('data-playing', 'true');
            }
        });

        EMBED.Event.add(window, "embedPlayerEventWaitingFired", function () {
            if (!Playlist.liveRadio) {
                var song = EMBED.Playlist[EMBED.Player.queueNumber];
                var el = $('.play-object[data-type="song"][data-id="' + song.id + '"]');
                el.removeAttr('data-pause').removeAttr('data-playing');
                el.attr('data-waiting', 'true');
            } else {
                var station = EMBED.Playlist[EMBED.Player.queueNumber];
                var el = $('.play-object[data-type="station"][data-id="' + station.id + '"]');
                el.removeAttr('data-pause').removeAttr('data-playing');
                el.attr('data-waiting', 'true');
            }
        });

        EMBED.Event.add(window, "embedPlayerEventPauseFired", function () {
            if (!EMBED.Playlist.length) {
                return false;
            }
            if (!Playlist.liveRadio) {
                var song = EMBED.Playlist[EMBED.Player.queueNumber];
                var el = $('.play-object[data-type="song"][data-id="' + song.id + '"]');
                el.removeAttr('data-waiting').removeAttr('data-playing');
                el.attr('data-pause', 'true');
            } else {
                var station = EMBED.Playlist[EMBED.Player.queueNumber];
                var el = $('.play-object[data-type="station"][data-id="' + station.id + '"]');
                el.removeAttr('data-waiting').removeAttr('data-playing');
                el.attr('data-pause', 'true');
            }
        });
        EMBED.Event.add(window, "embedPlayerEventEndedFired", function () {
            if (!EMBED.Playlist.length) {
                return false;
            }
            if (!Playlist.liveRadio) {
                var song = EMBED.Playlist[EMBED.Player.queueNumber];
                if(song.preview && ! song.streamable) {
                    $.engineLightBox.show("lightbox-vipOnlyFeature");
                }
            }
        });

        $(window).on("enginePageHasBeenLoaded", function () {
            if (!Playlist.liveRadio && EMBED.Playlist.length) {
                var song = EMBED.Playlist[EMBED.Player.queueNumber];
                var el = $('.play-object[data-type="song"][data-id="' + song.id + '"]');
                if (EMBED.Player.Audio.paused) {
                    el.attr('data-current', 'true').attr('data-pause', 'true');
                } else {
                    if (!EMBED.Player.Audio.currentTime) {
                        el.attr('data-current', 'true').attr('data-waiting', 'true');
                    } else {
                        el.attr('data-current', 'true').attr('data-playing', 'true');
                    }
                }
            } else if (EMBED.Playlist.length) {
                var station = EMBED.Playlist[EMBED.Player.queueNumber];
                var el = $('.play-object[data-type="station"][data-id="' + station.id + '"]');
                if (EMBED.Player.Audio.paused) {
                    el.attr('data-current', 'true').attr('data-pause', 'true');
                } else {
                    if (!EMBED.Player.Audio.currentTime) {
                        el.attr('data-current', 'true').attr('data-waiting', 'true');
                    } else {
                        el.attr('data-current', 'true').attr('data-playing', 'true');
                    }
                }
            }
        });
    });
});