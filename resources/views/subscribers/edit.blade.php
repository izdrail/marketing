@extends('marketing::layouts.app')

@section('title', __("Edit Subscriber") . " : {$subscriber->full_name}")

@section('heading')
    {{ __('Subscribers') }}
@stop

@section('content')

    @component('marketing::layouts.partials.card')
        @slot('cardHeader', __('Edit Subscriber'))

        @slot('cardBody')
            <form action="{{ route('marketing.subscribers.update', $subscriber->id) }}" method="POST" class="form-horizontal">
                @csrf
                @method('PUT')

                @include('marketing::subscribers.partials.form')

                <x-sendportal.submit-button :label="__('Save')" />
            </form>
        @endSlot
    @endcomponent

@stop
