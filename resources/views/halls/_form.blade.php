<x-form.input name="name" label="Назва залу" :value="$hall->name ?? ''" />
<x-form.input name="capacity" label="Місткість" type="number" :value="$hall->capacity ?? ''" />
<x-form.select name="format" label="Формат" :options="['2D' => '2D', '3D' => '3D', 'IMAX' => 'IMAX', '4DX' => '4DX']" :selected="$hall->format ?? null" />
<x-form.input name="rows_count" label="Кількість рядів" type="number" :value="$hall->rows_count ?? ''" />
