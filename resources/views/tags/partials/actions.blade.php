<form action="{{ route('marketing.tags.destroy', $tag->id) }}" method="POST">
    @csrf
    @method('DELETE')
    <a href="{{ route('marketing.tags.edit', $tag->id) }}"
       class="btn btn-sm btn-light">{{ __('Edit') }}</a>
    <button type="submit" class="btn btn-sm btn-light">{{ __('Delete') }}</button>
</form>
