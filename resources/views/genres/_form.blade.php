<x-form.input name="name" label="Назва жанру" :value="$genre->name ?? ''" />
<x-form.input name="slug" label="Slug (латиницею)" :value="$genre->slug ?? ''" />
<x-form.input name="description" label="Опис" :value="$genre->description ?? ''" />
