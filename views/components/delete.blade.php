@php $_id = str_random() @endphp
@php $_url = $url ?? route(array_wrap($route)[0], $route[1] ?? []) @endphp
<a class="{{ $class ?? 'text-danger' }}"
   href="{{ $_url }}"
   onclick="event.preventDefault(); if(confirm(this.innerText + '?')) document.getElementById('{{ $_id }}').submit()"
>{{ $slot ?? __('Delete') }}</a>
<form id="{{ $_id }}" class="d-none" method="POST"
      action="{{ $_url }}">
  @method('DELETE')
  @csrf
</form>
