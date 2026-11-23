<x-form.select name="user_id" label="Клієнт" :options="$users" :selected="$order->user_id ?? null" />
<x-form.select name="showtime_id" label="Сеанс" :options="$showtimes" :selected="$order->showtime_id ?? null" />
<x-form.select name="status" label="Статус" :options="['pending' => 'Очікує оплати', 'paid' => 'Оплачено', 'cancelled' => 'Скасовано']" :selected="$order->status ?? 'pending'" />
