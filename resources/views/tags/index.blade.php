@extends('marketing::layouts.app')

@section('title', __('Tags'))

@section('heading')
    {{ __('Tags') }}
@endsection

@section('content')
    @component('marketing::layouts.partials.actions')

        @slot('right')
            <a class="btn btn-primary btn-md btn-flat" href="{{ route('marketing.tags.create') }}">
                <i class="fa fa-plus"></i> {{ __('New Tag') }}
            </a>
        @endslot
    @endcomponent

    <div class="card">
        <div class="card-table">
            <table class="table">
                <thead>
                <tr>
                    <th>{{ __('Name') }}</th>
                    <th>{{ __('Subscribers') }}</th>
                    <th>{{ __('Actions') }}</th>
                </tr>
                </thead>
                <tbody>
                @forelse($tags as $tag)
                    <tr>
                        <td>
                            <a href="{{ route('marketing.tags.edit', $tag->id) }}">
                                {{ $tag->name }}
                            </a>
                        </td>
                        <td>{{ $tag->subscribers_count }}</td>
                        <td>
                            @include('marketing::tags.partials.actions')
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="100%">
                            <p class="empty-table-text">{{ __('You have not created any tags.') }}</p>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @include('marketing::layouts.partials.pagination', ['records' => $tags])

@endsection
