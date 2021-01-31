<div id="footer">
    <div class="external-links">
        <a href="{{ url('page/cookies-and-personal-data') }}" class="footer-link" data-translate-text="HOME_COOKIE">{{ __('web.HOME_COOKIE') }}</a>
        <span class="slash">/</span>
        <a href="{{ url('page/privacy-policy') }}" class="footer-link" data-translate-text="HOME_PRIVACY">{{ __('web.HOME_PRIVACY') }}</a>
        <span class="slash">/</span>
        <a href="{{ url('page/legal-information') }}" class="footer-link" data-translate-text="HOME_COPYRIGHTS">{{ __('web.HOME_COPYRIGHTS') }}</a>
        <span class="slash">/</span>
        <a href="{{ url('page/term-and-condition') }}" class="footer-link" data-translate-text="HOME_TERMS">{{ __('web.HOME_TERMS') }}</a>
    </div>
    <div class="social-links">
        <a href="http://www.facebook.com/" class="social-link" target="_blank">
            <svg class="icon" width="16" height="16" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" xml:space="preserve"><path d="M448,0H64C28.704,0,0,28.704,0,64v384c0,35.296,28.704,64,64,64h192V336h-64v-80h64v-64c0-53.024,42.976-96,96-96h64v80h-32c-17.664,0-32-1.664-32,16v64h80l-32,80h-48v176h96c35.296,0,64-28.704,64-64V64C512,28.704,483.296,0,448,0z"></path></svg>
            <span class="social-label">Facebook</span>
        </a>
        <a href="https://twitter.com/" class="last-child social-link" target="_blank">
            <svg class="icon" width="16" height="16" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 510 510" xml:space="preserve"><path d="M459,0H51C22.95,0,0,22.95,0,51v408c0,28.05,22.95,51,51,51h408c28.05,0,51-22.95,51-51V51C510,22.95,487.05,0,459,0z M400.35,186.15c-2.55,117.3-76.5,198.9-188.7,204C165.75,392.7,132.6,377.4,102,359.55c33.15,5.101,76.5-7.649,99.45-28.05c-33.15-2.55-53.55-20.4-63.75-48.45c10.2,2.55,20.4,0,28.05,0c-30.6-10.2-51-28.05-53.55-68.85c7.65,5.1,17.85,7.65,28.05,7.65c-22.95-12.75-38.25-61.2-20.4-91.8c33.15,35.7,73.95,66.3,140.25,71.4c-17.85-71.4,79.051-109.65,117.301-61.2c17.85-2.55,30.6-10.2,43.35-15.3c-5.1,17.85-15.3,28.05-28.05,38.25c12.75-2.55,25.5-5.1,35.7-10.2C425.85,165.75,413.1,175.95,400.35,186.15z"></path></svg>
            <span class="social-label">Twitter</span>
        </a>
    </div>
    <div class="copyright text-secondary">
        <span> © {{ now()->year }} NiNaCoder Group. All Rights Reserved. </span>
    </div>
    <div class="love text-secondary">
        <span>Tremendously thankful for the support of Aquafina &amp; Oreo. </span>
    </div>
</div>