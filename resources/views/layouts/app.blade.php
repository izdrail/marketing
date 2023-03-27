@extends('marketing::layouts.base')

@section('htmlBody')
    <div class="container-fluid">
        <div class="row">

            <div class="sidebar bg-purple-100 min-vh-100 d-none d-xl-block">

                <div class="mt-4">
                    <div class="logo text-center">
                        <h3 class="text-white bold mb-0 pb-0">
                            {{ config('app.name') }}
                        </h3>
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
