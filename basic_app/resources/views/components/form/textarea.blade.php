@props([

    'name',
    'label',
    'value' => '',
    'dir' => 'ltr',
    'type'=>'text'
])
<div style="margin-bottom: 22px;">
    <label  style="display: block;" dir="{{$dir}}">
        {{ $label }}
    </label>

    <x-adminlte-input class="textarea"
        type="{{ $type }}"
        name="{{ $name }}"
        dir="{{$dir}}"
        value="{{old($name, $value) }}"
        {{ $attributes->merge(['class' => 'form-control']) }}
    />
</div>
