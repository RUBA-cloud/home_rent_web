@props([
    'data' => [],
    'name' => 'branch_ids',
    'selected' => [],
    'label' => __('adminlte::adminlte.select_branch'),
])

@php
    $config = [
        "placeholder" => __("adminlte::adminlte.select_multiple"),
      //  "multiple"    => true, // 👈 multiple support
    ];
@endphp


{{-- Example dynamic Select2 --}}
<x-adminlte-select2
    id="{{ $name }}"
    name="{{ $name }}[]"
    label="{{ $label }}"
    igroup-size="sm"
    :config="$config"

    >
    {{-- Prepend icon --}}
    <x-slot name="prependSlot">
        <div class="input-group-text bg-gradient-primary">
            <i class="fas fa-code-branch"></i>
        </div>
    </x-slot>

    {{-- Dynamic options --}}
    @foreach($data as $item)
         <option value="{{(int) $item->id }}">

            {{ collect(old($name, $selected))->contains($item->id) ? 'selected' : '' }}
            {{ $item->name_en ?? $item->name_ar }}
        </option>
    @endforeach
</x-adminlte-select2>
