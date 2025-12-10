@props([
    'image' => null,
    'label' => 'Logo Image',
    'name' => 'logo_image',
    'id' => 'logo',
])

<div class="form-group mb-3">
    <x-adminlte-input-file
        style="background: rgb(53, 62, 131)"
        :id="$id"
        :name="$name"
        :label="$label"
        :required="false"
        :accept="'image/*'"
        :value="$image ? asset($image) : null"
        :preview="true"
        :preview-id="'preview-existing-' . $id"
        :preview-class="'img-thumbnail mt-2'"
        :max-file-size="1024 * 1024" {{-- 1MB --}}
        :max-file-size-error="__('adminlte::adminlte.company_info_form_image_upload_dimensions_max_error')"
        :max-file-size-success="__('adminlte::adminlte.company_info_form_image_upload_dimensions_max_success')"
        :max-file-size-required="__('adminlte::adminlte.company_info_form_image_upload_dimensions_max_required')"
        :max-file-size-invalid="__('adminlte::adminlte.company_info_form_image_upload_dimensions_max_invalid')"
        :placeholder="__('adminlte::adminlte.choose_file')"
        :browse-label="__('adminlte::adminlte.browse')"
        onchange="previewImage_{{ $id }}(this)"
        :show-preview="true"
        :show-upload="false"
        :show-remove="false"
        :show-caption="true"
        :show-browse="false"
        :show-clear="false"
        :show-upload-label="false"
        :show-remove-label="false"
        :show-caption-label="false"
        :show-browse-label="true"
        :show-clear-label="false"
        :show-preview-label="false"
        :show-upload-button="false"
        :show-remove-button="false"
        :show-caption-button="false"
        :show-browse-button="true"
        :show-clear-button="false"
        :show-preview-button="false"
        :show-upload-icon="false"
        :show-remove-icon="false"
        :show-caption-icon="false"
        :show-browse-icon="true"
        :show-clear-icon="false"
        :show-preview-icon="false"
        :show-upload-text="false"
        :show-remove-text="false"
        :show-caption-text="false"
        :show-browse-text="true"
        :show-clear-text="false"
        :show-preview-text="false"
        :show-upload-tooltip="false"
        :show-remove-tooltip="false"
        :placeholder="__('adminlte::adminlte.choose_file')"
        :browse-label="__('adminlte::adminlte.browse')"
        onchange="previewImage_{{ $id }}(this)"
    />

    {{-- Show image from DB if exists --}}
    @if($image)
        <img src="{{ asset($image) }}"
             id="preview-existing-{{ $id }}"
             class="img-thumbnail mt-2"
             style="max-height:150px;">
    @endif
</div>

<script>
    function previewImage_{{ $id }}(input) {
        let previewContainer = document.getElementById('preview-existing-{{ $id }}');

        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                if (!previewContainer) {
                    previewContainer = document.createElement('img');
                    previewContainer.id = 'preview-existing-{{ $id }}';
                    previewContainer.classList.add('img-thumbnail', 'mt-2');
                    previewContainer.style.maxHeight = '150px';
                    input.closest('.form-group').appendChild(previewContainer);
                }
                previewContainer.src = e.target.result;
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        if (typeof bsCustomFileInput !== 'undefined') {
            bsCustomFileInput.init();
        }
    });
</script>
