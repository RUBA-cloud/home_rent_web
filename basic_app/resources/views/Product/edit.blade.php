@extends('adminlte::page')

@section('title', __('adminlte::adminlte.edit') . ' ' . __('adminlte::adminlte.product'))

@push('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush

@section('content')
<div class="container-fluid py-3">
    <div class="card">
        <div class="card-header bg-warning text-dark">
            <h3 class="card-title">{{ __('adminlte::adminlte.edit') }} {{ __('adminlte::adminlte.product') }}</h3>
        </div>
        <div class="card-body">
           @include('Product.form', [
                'action' => route('product.update', $product->id),
                'method' => 'PUT',
                'product' => $product,
            ])
        </div>
    </div>
</div>
@endsection

<script>
document.addEventListener('DOMContentLoaded', function () {
    $('.select2').select2({ width: '100%' });

    // Add/remove color input
    const addColorBtn = document.getElementById('addColor');
    const colorInputs = document.getElementById('colorInputs');

    addColorBtn.addEventListener('click', () => {
        const inputGroup = document.createElement('div');
        inputGroup.className = 'input-group mb-2';
        inputGroup.innerHTML = `
            <input type="color" name="colors[]" class="form-control form-control-color" style="max-width: 80px;">
            <button type="button" class="btn btn-outline-danger remove-color">Remove</button>
        `;
        colorInputs.appendChild(inputGroup);
    });

    colorInputs.addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-color')) {
            e.target.closest('.input-group').remove();
        }
    });

    // Image upload preview
    const chooseImagesBtn = document.getElementById('chooseImages');
    const imagesInput = document.getElementById('imagesInput');
    const imagePreview = document.getElementById('imagePreview');

    chooseImagesBtn.addEventListener('click', () => imagesInput.click());

    imagesInput.addEventListener('change', function () {
        imagePreview.innerHTML = '';
        Array.from(this.files).forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = function (e) {
                const div = document.createElement('div');
                div.classList.add('position-relative', 'me-2', 'mb-2');
                div.innerHTML = `
                    <img src="${e.target.result}" alt="Image" style="width: 100px; height: 100px; object-fit: cover; border: 1px solid #ddd; border-radius: 4px;margin:5px">
                    <button type="button" class="btn btn-sm btn-danger remove-preview" data-index="${index}" style="position: absolute; top: -6px; right: -6px;">&times;</button>
                `;
                imagePreview.appendChild(div);
            };
            reader.readAsDataURL(file);
        });
    });

    imagePreview.addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-preview')) {
            const index = e.target.dataset.index;
            const files = Array.from(imagesInput.files);
            files.splice(index, 1);
            const dataTransfer = new DataTransfer();
            files.forEach(file => dataTransfer.items.add(file));
            imagesInput.files = dataTransfer.files;
            imagesInput.dispatchEvent(new Event('change'));
        }
    });
});
</script>
