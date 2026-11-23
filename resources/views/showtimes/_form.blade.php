<x-form.select name="movie_id" label="Фільм" :options="$movies" :selected="$showtime->movie_id ?? null" />
<x-form.select name="hall_id" label="Зал" :options="$halls" :selected="$showtime->hall_id ?? null" />
<x-form.input name="start_at" label="Початок сеансу" type="datetime-local" :value="isset($showtime) ? $showtime->start_at->format('Y-m-d').'T'.$showtime->start_at->format('H:i') : ''" />
<x-form.input name="base_price" label="Базова ціна" type="number" step="0.01" :value="$showtime->base_price ?? ''" />
<x-form.select name="status" label="Статус" :options="['scheduled' => 'Заплановано', 'cancelled' => 'Скасовано', 'finished' => 'Завершено']" :selected="$showtime->status ?? 'scheduled'" />
