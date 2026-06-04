@props([
    'name'    => null,
    'value'   => null,
    'checked' => false,
    'id'      => null,
    'route'   => null,
    'method'  => 'PATCH',
])

<div x-data="{ on: @js((bool) $checked) }">

    @if($route)
    <form x-ref="form" action="{{ $route }}" method="POST" class="hidden">
        @csrf
        @method($method)
    </form>
    @endif

    <button type="button"
            @click="on = !on; {{ $route ? '$refs.form.submit()' : '$refs.cb.checked = on' }}"
            :class="on ? 'bg-green-500' : 'bg-red-500'"
            class="relative w-11 h-6 rounded-full cursor-pointer transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-green-400 shrink-0">
        <span :class="on ? 'translate-x-5' : 'translate-x-0'"
              class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow transition-transform duration-200 ease-in-out">
        </span>
    </button>

    @if($name)
    <input type="checkbox"
           x-ref="cb"
           name="{{ $name }}"
           value="{{ $value }}"
           id="{{ $id }}"
           class="sr-only"
           @if($checked) checked @endif>
    @endif

</div>
