@props(['resource', 'id', 'show' => true])

@if ($show)
    <a href="{{ route($resource.'.show', $id) }}" class="btn btn-sm btn-info">Перегляд</a>
@endif
<a href="{{ route($resource.'.edit', $id) }}" class="btn btn-sm btn-warning">Редаг.</a>
<form action="{{ route($resource.'.destroy', $id) }}" method="POST" class="d-inline" onsubmit="return confirm('Дійсно видалити?');">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn btn-sm btn-danger">Видалити</button>
</form>
