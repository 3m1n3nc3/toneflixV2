@if(Route::currentRouteName() == 'frontend.homepage')
    <div id="landing-hero" class="p-0">
        <div class="claim-hero">
            <div class="container claim-container">
                <div class="row">
                    <div class="col">
                        <div class="vertical-align">
                            <p class="claim-subtitle text-uppercase" data-translate-text="PREMIUM">{{ __('web.PREMIUM') }}</p>
                            <h1 class="claim-display-title" data-translate-text="LANDING_TITLE">{{ __('web.LANDING_TITLE') }}</h1>
                            <p class="claim-h3 text-left text-white" data-translate-text="LANDING_DESC">{{ __('web.LANDING_DESC') }}</p>
                            <a class="button-white orange w-button claim-artist-access" data-translate-text="LANDING_BUTTON_TEXT">{{ __('web.LANDING_BUTTON_TEXT') }}</a>
                        </div>
                    </div>
                    <div class="claim-column-right col">
                        <img src="{{ asset('skins/default/images/main-landing.png') }}" width="540" alt="{{ __('web.LANDING_TITLE') }}" class="claim-landing-image">
                    </div>
                </div>
            </div>
        </div>
        @if(Cache::has('trending_week'))
            <div class="va-section">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-5 col-12 align-items-center d-flex">
                            <div class="position-relative">
                                <h1 data-translate-text="LANDING_TRENDING_TITLE">{{ __('web.LANDING_TRENDING_TITLE') }}</h1>
                                <p data-translate-text="LANDING_TRENDING_DESC">{{ __('web.LANDING_TRENDING_DESC') }}</p>
                                <a href="{{ route('frontend.trending.week') }}" class="cta-link mt-3">
                                    <span>
                                        <i class="landing-icon">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16"><path d="M6.5 13.5L5.5 12.5 9.9 8 5.5 3.5 6.5 2.5 12.1 8 6.5 13.5z"></path></svg>
                                        </i>
                                    </span>
                                    <span class="cta-text font-weight-bolder" data-translate-text="LANDING_TRENDING_BUTTON_TEXT">{{ __('web.LANDING_TRENDING_BUTTON_TEXT') }}</span>
                                </a>
                            </div>
                        </div>
                        <div class="col-lg-7 col-12">
                            <div class="custom-grid-wrapper row">
                                @foreach(Cache::get('trending_week')->slice(0, 6) as $song)
                                    <a href="{{ $song->permalink_url }}" class="custom-grid text-dec-none col-lg-4 col-6">
                                        <div class="position-relative overflow-hidden">
                                            <div class="custom-grid-image block placeholder position-relative" style="padding-bottom:100%;">
                                                <img src="{{ $song->artwork_url }}" alt="{!! $song->title !!}" class="block position-absolute" />
                                            </div>
                                            <div class="custom-grid-cover ">
                                                <div class="position-center-content justify-content-center align-items-center text-center p-2">
                                                    <h5 class="mb-2">{!! $song->title !!}</h5>
                                                    <p class="mb-0">@foreach($song->artists as $artist){!! $artist->name !!}@if(!$loop->last), @endif @endforeach</p>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        <div class="va-section-footer secondary">
            <div class="container claim-container">
                <h2 class="claim-h2 mb-5" data-translate-text="WHY_US">{{ __('web.WHY_US') }}</h2>
                <div class="row">
                    <div class="card-info w-col col-lg-4 col-12">
                        <div class="position-relative d-flex justify-content-center mb-3">
                            <img src="{{ asset('skins/default/images/landing-collection.svg') }}" alt="" class="card-image">
                        </div>
                        <h3 class="claim-feature-h3 text-center" data-translate-text="WHY_US_1_T">{{ __('web.WHY_US_1_T') }}</h3>
                        <p class="claim-h3-regular text-secondary" data-translate-text="WHY_US_1_D">{{ __('web.WHY_US_1_D') }}</p>
                    </div>
                    <div class="card-info w-col col-lg-4 col-12">
                        <div class="position-relative d-flex justify-content-center mb-3">
                            <img src="{{ asset('skins/default/images/landing-pocket.svg') }}" alt="" class="card-image">
                        </div>
                        <h3 class="claim-feature-h3 text-center" data-translate-text="WHY_US_2_T">{{ __('web.WHY_US_2_T') }}</h3>
                        <p class="claim-h3-regular text-secondary" data-translate-text="WHY_US_2_D">{{ __('web.WHY_US_2_D') }}</p>
                    </div>
                    <div class="card-info w-col col-lg-4 col-12">
                        <div class="position-relative d-flex justify-content-center mb-3">
                            <img src="{{ asset('skins/default/images/landing-foryou.svg') }}" alt="" class="card-image">
                        </div>
                        <h3 class="claim-feature-h3 text-center" data-translate-text="WHY_US_3_T">{{ __('web.WHY_US_3_T') }}</h3>
                        <p class="claim-h3-regular text-secondary" data-translate-text="WHY_US_3_D">{{ __('web.WHY_US_3_D') }}</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="va-section">
            <div class="container">
                <div class="row">
                    <div class="col-lg-6 col-12 d-flex justify-content-center align-items-center">
                        <img src="{{ asset('skins/default/images/landing-community.svg') }}" alt="" class="card-image">
                    </div>
                    <div class="col-lg-6 col-12">
                        <h2 class="claim-h2-white padding-bottom-40px" data-translate-text="JOIN_US_TITLE">{{ __('web.JOIN_US_TITLE') }}</h2>
                        <p class="claim-h3" data-translate-text="JOIN_US_DESCRIPTION">{{ __('web.JOIN_US_DESCRIPTION') }}</p>
                        <div class="d-flex justify-content-center">
                            <a class="button-white w-button claim-artist-access text-primary"data-translate-text="JOIN_US_BUTTON_TEXT">{{ __('web.JOIN_US_BUTTON_TEXT') }}</a>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <div class="va-section-footer secondary">
            <div class="container">
                <h2 class="claim-h2-white padding-bottom-40px" data-translate-text="CLAIM_NOW_TEXT">{{ __('web.CLAIM_NOW_TEXT') }}</h2>
                <p class="claim-h3" data-translate-text="CLAIM_NOW_DESCRIPTION">{!! __('web.CLAIM_NOW_DESCRIPTION') !!}</p>
                <div class="d-flex justify-content-center">
                    <a class="button-white w-button claim-artist-access text-primary" data-translate-text="CLAIM_NOW_BUTTON_TEXT">{{ __('web.CLAIM_NOW_BUTTON_TEXT') }}</a>
                </div>
            </div>
        </div>
        @include('homepage.footer')
    </div>
@endif
