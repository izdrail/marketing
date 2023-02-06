<ul class="nav nav-pills mb-4">
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('marketing.campaigns.index') ? 'active'  : '' }}"
           href="{{ route('marketing.campaigns.index') }}">{{ __('Draft') }}</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('marketing.campaigns.sent') ? 'active'  : '' }}"
           href="{{ route('marketing.campaigns.sent') }}">{{ __('Sent') }}</a>
    </li>
</ul>
