<ul class="nav nav-pills mb-4">
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('marketing.messages.index') ? 'active'  : '' }}"
           href="{{ route('marketing.messages.index') }}">{{ __('Sent') }}</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('marketing.messages.draft') ? 'active'  : '' }}"
           href="{{ route('marketing.messages.draft') }}">{{ __('Draft') }}</a>
    </li>
</ul>
