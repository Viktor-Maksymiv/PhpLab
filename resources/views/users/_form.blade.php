<x-form.input name="name" label="Імʼя" :value="$user->name ?? ''" />
<x-form.input name="email" label="Email" type="email" :value="$user->email ?? ''" />
<x-form.input name="password" label="Пароль (під час редагування залиште порожнім, щоб не змінювати)" type="password" />
@if (auth()->user()->isAdmin())
    <x-form.select name="role" label="Роль" :options="['customer' => 'Глядач', 'cashier' => 'Касир', 'admin' => 'Адміністратор']" :selected="$user->role ?? 'customer'" />
@endif
<x-form.input name="phone" label="Телефон" :value="$user->phone ?? ''" />
@if (auth()->user()->isAdmin())
    <x-form.input name="loyalty_points" label="Бонусні бали" type="number" :value="$user->loyalty_points ?? 0" />
@endif
