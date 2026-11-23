<x-form.select name="order_id" label="Замовлення" :options="$orders" :selected="$ticket->order_id ?? null" />
<x-form.select name="showtime_id" label="Сеанс" :options="$showtimes" :selected="$ticket->showtime_id ?? null" />
<x-form.select name="seat_id" label="Місце" :options="$seats" :selected="$ticket->seat_id ?? null" />
<x-form.input name="price" label="Ціна" type="number" step="0.01" :value="$ticket->price ?? ''" />
<x-form.select name="status" label="Статус" :options="['reserved' => 'Заброньовано', 'paid' => 'Оплачено', 'used' => 'Використано', 'refunded' => 'Повернено']" :selected="$ticket->status ?? 'reserved'" />
