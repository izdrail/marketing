@extends('marketing::layouts.app')

@section('heading')
    {{ __('Email Services') }}
@stop

@section('content')

    @component('marketing::layouts.partials.card')
        @slot('cardHeader', __('Edit Email Service'))

        @slot('cardBody')
            <form action="{{ route('marketing.email_services.update', $emailService->id) }}" method="POST" class="form-horizontal">
                @csrf
                @method('PUT')
                <x-sendportal.text-field name="name" :label="__('Name')" :value="$emailService->name" />

                @include('marketing::email_services.options.' . strtolower($emailServiceType->name), ['settings' => $emailService->settings])

                <x-sendportal.submit-button :label="__('Update')" />
            </form>
        @endSlot
    @endcomponent

@stop
