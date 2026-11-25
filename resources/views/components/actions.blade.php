@props(['resource', 'model', 'show' => true])

@if ($show)
    <a href="{{ route($resource.'.show', $model) }}" class="btn btn-sm btn-info">Перегляд</a>
@endif
@can('update', $model)
    <a href="{{ route($resource.'.edit', $model) }}" class="btn btn-sm btn-warning">Редаг.</a>
@endcan
@can('delete', $model)
    <form action="{{ route($resource.'.destroy', $model) }}" method="POST" class="d-inline" onsubmit="return confirm('Дійсно видалити?');">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-sm btn-danger">Видалити</button>
    </form>
@endcan
