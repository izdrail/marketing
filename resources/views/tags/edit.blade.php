@extends('marketing::layouts.app')

@section('title', __("Edit Tag"))

@section('heading')
    {{ __('Tags') }}
@stop

@section('content')

    @component('marketing::layouts.partials.card')
        @slot('cardHeader', __('Edit Tag'))

        @slot('cardBody')
            <form action="{{ route('marketing.tags.update', $tag->id) }}" method="POST" class="form-horizontal">
                @csrf
                @method('PUT')

                @include('marketing::tags.partials.form')

                <x-sendportal.submit-button :label="__('Save')" />
            </form>
        @endSlot
    @endcomponent

@stop
