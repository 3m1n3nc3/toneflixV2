@extends('index')
@section('content')
    <div id="page-content">
        <div class="linear-header upload">
            <div class="container">
                <h1>UPLOAD</h1>
                <h2>Upload unlimited songs, albums and podcasts. Our platform ensures artists get paid when their tracks get played.</h2>
            </div>
            <div class="container upload-helper">
                <form id="fileupload" data-template="template-upload" method="POST" enctype="multipart/form-data">
                    <div class="upload-container">
                        <h1>Lets get started</h1>
                        @if(config('settings.ffmpeg'))
                            <p data-translate-text="UPLOAD_ALL_FORMAT_TIP">{{ __('web.UPLOAD_ALL_FORMAT_TIP') }}</p>
                        @else
                            <p data-translate-text="UPLOAD_MP3_TIP">{{ __('web.UPLOAD_MP3_TIP') }}</p>
                        @endif
                        <div id="upload-file-button" class="btn btn-primary">
                            <span data-translate-text="CHOOSE_A_FILE">{{ __('web.CHOOSE_A_FILE') }}</span>
                            <input id="upload-file-input" type="file" accept="audio/*" name="file" multiple>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="uploaded-files card-columns card-2-columns"></div>
    </div>
    @include('commons.upload-item', ['genres' => $allowGenres, 'moods' => $allowMoods])
@endsection