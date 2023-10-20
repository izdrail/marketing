@extends('marketing::layouts.app')

@section('title', __('Email Templates'))

@section('heading')
    {{ __('Email Templates') }}
@endsection

@section('content')

    @component('marketing::layouts.partials.actions')
        @slot('right')
            <a class="btn btn-primary btn-md btn-flat" href="{{ route('marketing.templates.create') }}">
                <i class="fa fa-plus mr-1"></i> {{ __('New Template') }}
            </a>
        @endslot
        @slot('left')
            <a class="btn btn-success btn-md btn-flat" href="{{ route('marketing.templates.import') }}">
                <i class="fa fa-plus mr-1"></i> {{ __('Import Template') }}
            </a>
        @endslot
    @endcomponent

    @include('marketing::templates.partials.grid')

@endsection
