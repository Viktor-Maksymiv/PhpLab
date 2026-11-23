<x-form.input name="title" label="Назва фільму" :value="$movie->title ?? ''" />
<x-form.input name="duration_minutes" label="Тривалість (хв)" type="number" :value="$movie->duration_minutes ?? ''" />
<x-form.select name="age_rating" label="Віковий рейтинг" :options="['0+' => '0+', '6+' => '6+', '12+' => '12+', '16+' => '16+', '18+' => '18+']" :selected="$movie->age_rating ?? null" />
<x-form.input name="release_date" label="Дата прем’єри" type="date" :value="isset($movie) ? $movie->release_date->format('Y-m-d') : ''" />
<x-form.input name="poster_url" label="Посилання на постер" :value="$movie->poster_url ?? ''" />

@php($selectedGenres = old('genres', isset($movie) ? $movie->genres->pluck('id')->all() : []))

<div class="mb-3">
    <label class="form-label d-block">Жанри</label>
    @foreach ($genres as $genre)
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" name="genres[]" id="genre_{{ $genre->id }}" value="{{ $genre->id }}" @checked(in_array($genre->id, $selectedGenres))>
            <label class="form-check-label" for="genre_{{ $genre->id }}">{{ $genre->name }}</label>
        </div>
    @endforeach
    @error('genres')
        <div class="text-danger small">{{ $message }}</div>
    @enderror
    @error('genres.*')
        <div class="text-danger small">{{ $message }}</div>
    @enderror
</div>
