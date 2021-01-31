@extends('backend.index')
@section('content')
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="{{ route('backend.dashboard') }}">Control Panel</a>
        </li>
        <li class="breadcrumb-item active"><a href="{{ route('backend.banners') }}">Banners</a></li>
        <li class="breadcrumb-item active">{{ isset($banner) ? $banner->description : 'Add new banners' }}</li>
    </ol>
    <div class="row">
        <div class="col-lg-12">
            <form method="POST" action="" class="form-horizontal" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label>Type:</label>
                    @if(env('MEDIA_AD_MODULE') == 'true')
                        {!! makeDropDown( array("0" => "Banner", "1" => "Audio", "2" => "Video"), "type", isset($banner) ? $banner->type : old('type') ) !!}
                    @else
                        {!! makeDropDown( array("0" => "Banner"), "type", isset($banner) ? $banner->type : old('type') ) !!}
                    @endif
                </div>
                <div class="form-group">
                    <label>The name of the banner (latin characters):</label>
                    <input class="form-control" type="text" name="banner_tag" value="{{ isset($banner) && ! old('banner_tag') ? $banner->banner_tag : old('banner_tag') }}" required>
                </div>
                <div class="form-group">
                    <label>Description:</label>
                    <input class="form-control" type="text" name="description" value="{{ isset($banner) && ! old('description') ? $banner->description : old('description') }}" required>
                </div>
                <div class="form-group">
                    <label>Start Date:</label>
                    <input class="form-control datetimepicker-with-form" type="text" name="started_at" value="{{ isset($banner) && ! old('started_at') ? \Carbon\Carbon::parse($banner->started_at)->format('Y/m/d H:i') : old('started_at') }}" placeholder="Pick a date" autocomplete="off">
                </div>
                <div class="form-group">
                    <label>End Date:</label>
                    <input class="form-control datetimepicker-with-form" type="text" name="ended_at" value="{{ isset($banner) && ! old('ended_at') ? \Carbon\Carbon::parse($banner->ended_at)->format('Y/m/d H:i') : old('ended_at') }}" placeholder="Pick a date" autocomplete="off">
                </div>
                <div id="attachment-wrap" class="form-group d-none">
                    <label>Attachment</label>
                    <div class="input-group col-xs-12">
                        <input type="file" name="file" class="file-selector">
                        <span class="input-group-addon"><i class="fas fa-fw fa-image"></i></span>
                        <input type="text" class="form-control input-lg" disabled placeholder="Upload File">
                        <span class="input-group-btn">
                            <button class="browse btn btn-primary input-lg" type="button"><i class="fas fa-fw fa-file"></i> Browse</button>
                        </span>
                    </div>
                </div>
                <div id="banner-code-wrap" class="form-group">
                    <label>Banner code:</label>
                    <textarea name="code" class="form-control editor" rows="5">{{ isset($banner) && ! old('code') ? $banner->code : old('code') }}</textarea>
                </div>
                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="customCheck" name="disabled" {{ isset($banner) && $banner->approved ? '' : 'checked' }}>
                        <label class="custom-control-label" for="customCheck">Disabled</label>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Save</button>
                <button type="reset" class="btn btn-info">Reset</button>
            </form>
        </div>
    </div>
    <script>

    </script>
@endsection
@section('script')
    <script>
        $(document).ready(function () {
            $(document).on('change', 'select[name="type"]', function () {
                var type = $(this).val();
                if(parseInt(type)) {
                    $('#attachment-wrap').removeClass('d-none');
                    $('#banner-code-wrap').addClass('d-none');
                } else {
                    $('#attachment-wrap').addClass('d-none');
                    $('#banner-code-wrap').removeClass('d-none');
                }
            });
        });
    </script>
@endsection