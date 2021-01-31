<div id="page-nav">
    <div class="outer">
        <ul>
            <li><a class="page-nav-link @if(Route::currentRouteName() == 'frontend.settings') active @endif" href="{{ route('frontend.settings') }}" data-translate-text="SETTINGS_NAV_PROFILE">{{ __('web.SETTINGS_NAV_PROFILE') }}</a></li>
            @if(! \App\Models\Role::getValue('admin_access'))
                <li><a class="page-nav-link @if(Route::currentRouteName() == 'frontend.settings.subscription') active @endif" href="{{ route('frontend.settings.subscription') }}" data-translate-text="SETTINGS_NAV_SUBSCRIPTION">{{ __('web.SETTINGS_NAV_SUBSCRIPTION') }}</a></li>
            @endif
            <li><a class="page-nav-link @if(Route::currentRouteName() == 'frontend.settings.account') active @endif" href="{{ route('frontend.settings.account') }}" data-translate-text="SETTINGS_NAV_ACCOUNT">{{ __('web.SETTINGS_NAV_ACCOUNT') }}</a></li>
            <li><a class="page-nav-link @if(Route::currentRouteName() == 'frontend.settings.password') active @endif" href="{{ route('frontend.settings.password') }}" data-translate-text="SETTINGS_NAV_PASSWORD">{{ __('web.SETTINGS_NAV_PASSWORD') }}</a></li>
            <li><a class="page-nav-link @if(Route::currentRouteName() == 'frontend.settings.preferences') active @endif" href="{{ route('frontend.settings.preferences') }}" data-translate-text="SETTINGS_NAV_PREFERENCES">{{ __('web.SETTINGS_NAV_PREFERENCES') }}</a></li>
            <li><a class="page-nav-link @if(Route::currentRouteName() == 'frontend.settings.services') active @endif" href="{{ route('frontend.settings.services') }}" data-translate-text="SETTINGS_NAV_THIRD_PARTY">{{ __('web.SETTINGS_NAV_THIRD_PARTY') }}</a></li>
        </ul>
    </div>
</div>