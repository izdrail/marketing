@extends('marketing::layouts.base')

@section('htmlBody')
    <div class="container-fluid">
        <div class="row">

            <div class="sidebar bg-purple-100 min-vh-100 d-none d-xl-block">

                <div class="mt-4">
                    <div class="logo text-center">
                        <a href="{{ route('marketing.dashboard') }}">
                            <img src="{{ asset('/vendor/marketing/img/logo-main.png') }}" alt="" width="175px">
                        </a>
                    </div>
                </div>

                <div class="mt-5">
                    @include('marketing::layouts.partials.sidebar')
                </div>
            </div>

            @include('marketing::layouts.main')
        </div>
    </div>
@endsection
