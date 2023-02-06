@extends('marketing::layouts.app')

@section('title', __('New Tag'))

@section('heading')
    {{ __('Tags') }}
@stop

@section('content')

    @component('marketing::layouts.partials.card')
        @slot('cardHeader', __('Create Tag'))

        @slot('cardBody')
            <form action="{{ route('marketing.tags.store') }}" method="POST" class="form-horizontal">
                @csrf

                @include('marketing::tags.partials.form')

                <x-sendportal.submit-button :label="__('Save')" />
            </form>
        @endSlot
    @endcomponent

@stop
