<x-form.select name="hall_id" label="Зал" :options="$halls" :selected="$seat->hall_id ?? null" />
<x-form.input name="row_number" label="Ряд" type="number" :value="$seat->row_number ?? ''" />
<x-form.input name="seat_number" label="Місце" type="number" :value="$seat->seat_number ?? ''" />
<x-form.select name="type" label="Тип місця" :options="['standard' => 'Стандарт', 'vip' => 'VIP', 'sofa' => 'Диван']" :selected="$seat->type ?? 'standard'" />
