{{-- resources/views/profile/edit.blade.php --}}
@extends('adminlte::page')

@php
    $t        = 'adminlte::adminlte.';
    $dirIsRtl = app()->getLocale() === 'ar';

    // safe fallbacks
    $title     = __($t.'title')                !== $t.'title' ? __($t.'title')                : __('Profile');
    $yourInfo  = __($t.'your_information')     !== $t.'your_information' ? __($t.'your_information') : __('Your Information');
    $pngJpg    = __($t.'png_jpg')              !== $t.'png_jpg' ? __($t.'png_jpg')            : __('PNG/JPG up to 2MB');
    $nameLbl   = __($t.'name')                 !== $t.'name' ? __($t.'name')                  : __('Name');
    $emailLbl  = __($t.'email')                !== $t.'email' ? __($t.'email')                : __('Email');
    $langLbl   = __($t.'language')             !== $t.'language' ? __($t.'language')          : __('Language');
    $engLbl    = __($t.'english')              !== $t.'english' ? __($t.'english')            : 'English';
    $arLbl     = __($t.'arabic')               !== $t.'arabic' ? __($t.'arabic')              : 'العربية';
    $avatarLbl = __($t.'avatar')               !== $t.'avatar' ? __($t.'avatar')              : __('Avatar');
    $chooseLbl = __($t.'choose_file')          !== $t.'choose_file' ? __($t.'choose_file')    : __('Choose File');
    $backLbl   = __($t.'back')                 !== $t.'back' ? __($t.'back')                  : __('Back');
    $saveLbl   = __($t.'save_changes')         !== $t.'save_changes' ? __($t.'save_changes')  : __('Save Changes');
    $dirNote   = __($t.'direction_notice')     !== $t.'direction_notice'
                    ? __($t.'direction_notice')
                    : __('Direction (RTL/LTR) switches based on language after saving.');

    $changePassTitle = __($t.'change_password_optional') !== $t.'change_password_optional'
                        ? __($t.'change_password_optional')
                        : __('Change Password (optional)');

    $currPassLbl = __($t.'current_password') !== $t.'current_password'
                        ? __($t.'current_password')
                        : __('Current Password');

    $newPassLbl = __($t.'new_password') !== $t.'new_password'
                        ? __($t.'new_password')
                        : __('New Password');

    $confirmPassLbl = __($t.'confirm_new_password') !== $t.'confirm_new_password'
                        ? __($t.'confirm_new_password')
                        : __('Confirm New Password');

    $avatarUrl = !empty($user?->avatar)
        ? asset('storage/'.$user->avatar)
        : 'https://ui-avatars.com/api/?name='.urlencode($user->name ?? 'U').'&size=128';
@endphp

@section('title', $title)

@section('content_header')
    <h1 class="m-0">{{ $title }}</h1>
@endsection

@section('content')
<div class="row" dir="{{ $dirIsRtl ? 'rtl' : 'ltr' }}">
    {{-- FULL WIDTH CARD --}}
    <div class="col-12">
        {{-- flash --}}
        @if (session('status'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle {{ $dirIsRtl ? 'ml-2' : 'mr-2' }}"></i>{{ session('status') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="{{ __('Close') }}">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <div class="card card-primary">
            <div class="card-header {{ $dirIsRtl ? 'text-right' : '' }}">
                <h3 class="card-title">
                    <i class="fas fa-user {{ $dirIsRtl ? 'ml-2' : 'mr-2' }}"></i>{{ $yourInfo }}
                </h3>
                <div class="card-tools {{ $dirIsRtl ? 'mr-auto' : '' }}">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse" aria-label="{{ __('Collapse') }}">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>

            <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" novalidate>
                @csrf
                @method('PUT')
                <div class="card-body {{ $dirIsRtl ? 'text-right' : '' }}">
                    {{-- Name --}}
                    <div class="form-group">
                        <label for="name">{{ $nameLbl }}</label>
                        <input id="name" type="text" name="name"
                               class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name', $user->name ?? '') }}" required autocomplete="off">
                        @error('name') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>

                    {{-- Email --}}
                    <div class="form-group">
                        <label for="email">{{ $emailLbl }}</label>
                        <input id="email" type="email" name="email"
                               class="form-control @error('email') is-invalid @enderror"
                               value="{{ old('email', $user->email ?? '') }}" required autocomplete="off">
                        @error('email') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>

                    {{-- Language --}}
                    <div class="form-group">
                        <label for="locale">{{ $langLbl }}</label>
                        <select id="locale" name="locale"
                                class="form-control @error('locale') is-invalid @enderror">
                            <option value="en" @selected(old('locale', $user->locale ?? app()->getLocale())==='en')>{{ $engLbl }}</option>
                            <option value="ar" @selected(old('locale', $user->locale ?? app()->getLocale())==='ar')>{{ $arLbl }}</option>
                        </select>
                        @error('locale') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        <small class="text-muted d-block mt-1">{{ $dirNote }}</small>
                    </div>

                    {{-- Avatar --}}
                    <div class="form-group">
                        <label for="avatar">{{ $avatarLbl }}</label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input @error('avatar') is-invalid @enderror"
                                   id="avatar" name="avatar" accept="image/*">
                            <label class="custom-file-label {{ $dirIsRtl ? 'text-right' : '' }}" for="avatar">
                                {{ $chooseLbl }}
                            </label>
                            @error('avatar') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>

                        <div class="mt-3 d-flex align-items-center {{ $dirIsRtl ? 'flex-row-reverse' : '' }}">
                            <img id="avatarPreview"
                                 src="{{ $avatarUrl }}"
                                 alt="avatar" class="img-circle elevation-2"
                                 style="width:64px;height:64px;object-fit:cover;">
                            <small class="{{ $dirIsRtl ? 'mr-3' : 'ml-3' }} text-muted">{{ $pngJpg }}</small>
                        </div>
                    </div>

                    {{-- Password (optional) --}}
                    <div class="border-top pt-3 mt-3">
                        <h5 class="mb-3">
                            <i class="fas fa-lock {{ $dirIsRtl ? 'ml-2' : 'mr-2' }}"></i>
                            {{ $changePassTitle }}
                        </h5>

                        <div class="form-group">
                            <label for="current_password">{{ $currPassLbl }}</label>
                            <input id="current_password" type="password" name="current_password"
                                   class="form-control @error('current_password') is-invalid @enderror"
                                   autocomplete="new-password">
                            @error('current_password') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>

                        <div class="form-group">
                            <label for="password">{{ $newPassLbl }}</label>
                            <input id="password" type="password" name="password"
                                   class="form-control @error('password') is-invalid @enderror"
                                   autocomplete="new-password">
                            @error('password') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>

                        <div class="form-group">
                            <label for="password_confirmation">{{ $confirmPassLbl }}</label>
                            <input id="password_confirmation" type="password" name="password_confirmation"
                                   class="form-control" autocomplete="new-password">
                        </div>
                    </div>
                </div>

                <div class="card-footer d-flex {{ $dirIsRtl ? 'justify-content-start' : 'justify-content-end' }}">
                    <a href="{{ url()->previous() }}"
                       class="btn btn-outline-secondary mx-1">
                        <i class="fas fa-arrow-{{ $dirIsRtl ? 'right' : 'left' }}"></i>
                        {{ $backLbl }}
                    </a>
                    <button type="submit" class="btn btn-primary mx-1">
                        <i class="fas fa-save"></i>
                        {{ $saveLbl }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
    // update file label & preview
    document.addEventListener('DOMContentLoaded', function(){
        const fileInput = document.getElementById('avatar');
        const preview   = document.getElementById('avatarPreview');

        if (fileInput) {
            fileInput.addEventListener('change', function(){
                const fileName = this.files && this.files.length ? this.files[0].name : '';
                const label    = this.nextElementSibling;
                if (label && fileName) {
                    label.innerText = fileName;
                }

                if (this.files && this.files[0]) {
                    const reader = new FileReader();
                    reader.onload = function(ev){
                        if (preview) preview.src = ev.target.result;
                    };
                    reader.readAsDataURL(fileInput.files[0]);
                }
            });
        }
    });
</script>
@endpush
