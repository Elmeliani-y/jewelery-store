@extends('layouts.vertical', ['title' => 'إعدادات النظام'])

@section('css')
    @include('components.form-styles')
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">إعدادات النظام</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
                        <li class="breadcrumb-item active">إعدادات النظام</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            حدثت أخطاء في الإدخال. يرجى المراجعة.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form action="{{ route('settings.update') }}" method="POST" class="arabic-text" enctype="multipart/form-data">
        @csrf
        <div class="row">
            <div class="col-lg-8">
                <div class="form-section">
                    <h5 class="section-header">
                        <iconify-icon icon="solar:settings-bold-duotone"></iconify-icon>
                        معلومات المتجر
                    </h5>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="company_name">اسم المتجر <span class="text-danger">*</span></label>
                            <input type="text" id="company_name" name="company_name" class="form-control @error('company_name') is-invalid @enderror" value="{{ old('company_name', $settings['company_name'] ?? '') }}" required>
                            @error('company_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="currency_symbol">رمز العملة <span class="text-danger">*</span></label>
                            <input type="text" id="currency_symbol" name="currency_symbol" class="form-control @error('currency_symbol') is-invalid @enderror" value="{{ old('currency_symbol', $settings['currency_symbol'] ?? 'ريال') }}" required>
                            @error('currency_symbol')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label" for="address">العنوان</label>
                            <input type="text" id="address" name="address" class="form-control @error('address') is-invalid @enderror" value="{{ old('address', $settings['address'] ?? '') }}" placeholder="مثال: شارع الملك، الرياض">
                            @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label" for="phones">أرقام الهواتف</label>
                            <input type="text" id="phones" name="phones" class="form-control @error('phones') is-invalid @enderror" value="{{ old('phones', $settings['phones'] ?? '') }}" placeholder="مثال: 0500000000, 0110000000">
                            @error('phones')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="tax_number">الرقم الضريبي</label>
                            <input type="text" id="tax_number" name="tax_number" class="form-control @error('tax_number') is-invalid @enderror" value="{{ old('tax_number', $settings['tax_number'] ?? '') }}">
                            @error('tax_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="commercial_register">السجل التجاري</label>
                            <input type="text" id="commercial_register" name="commercial_register" class="form-control @error('commercial_register') is-invalid @enderror" value="{{ old('commercial_register', $settings['commercial_register'] ?? '') }}">
                            @error('commercial_register')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label" for="logo">اللوجو (شعار المتجر)</label>
                            <input type="file" id="logo" name="logo" accept="image/*" class="form-control @error('logo') is-invalid @enderror">
                            @error('logo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <div class="mt-2 d-flex align-items-center gap-3">
                                @if(!empty($settings['logo_path']))
                                    <img src="{{ asset($settings['logo_path']) }}" alt="شعار المتجر" style="height:48px; border-radius:8px; border:1px solid var(--ct-border-color)">
                                @endif
                                <img id="logoPreview" src="#" alt="معاينة الشعار" style="height:48px; border-radius:8px; border:1px solid var(--ct-border-color); display:none;">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <h5 class="section-header">
                        <iconify-icon icon="solar:shield-warning-bold-duotone"></iconify-icon>
                        السلوك والواجهات
                    </h5>
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="enable_delete_modal" name="enable_delete_modal" value="1" {{ old('enable_delete_modal', ($settings['enable_delete_modal'] ?? true) ? '1' : '') ? 'checked' : '' }}>
                                <label class="form-check-label" for="enable_delete_modal">استخدام نافذة تأكيد مخصصة عند الحذف</label>
                            </div>
                        </div>
                        <div class="col-md-12 mb-1">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="show_tax_in_totals" name="show_tax_in_totals" value="1" {{ old('show_tax_in_totals', ($settings['show_tax_in_totals'] ?? true) ? '1' : '') ? 'checked' : '' }}>
                                <label class="form-check-label" for="show_tax_in_totals">عرض الضريبة ضمن الإجماليات</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="form-section">
                    <h5 class="section-header">
                        <iconify-icon icon="solar:settings-bold-duotone"></iconify-icon>
                        الإجراءات
                    </h5>
                    <div class="d-grid gap-3">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <iconify-icon icon="solar:diskette-bold"></iconify-icon>
                            حفظ الإعدادات
                        </button>
                        <a href="{{ route('dashboard') }}" class="btn btn-light btn-lg">
                            <iconify-icon icon="solar:arrow-right-bold"></iconify-icon>
                            إلغاء
                        </a>
                    </div>
                </div>

                <div class="alert alert-info mt-3">
                    <iconify-icon icon="solar:info-circle-bold" class="fs-5 me-2"></iconify-icon>
                    <strong>ملاحظة:</strong> يتم حفظ الإعدادات في ملف آمن داخل النظام.
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@section('script')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const logoInput = document.getElementById('logo');
    const preview = document.getElementById('logoPreview');
    if (logoInput) {
        logoInput.addEventListener('change', function() {
            const file = this.files?.[0];
            if (!file) { preview.style.display = 'none'; return; }
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'inline-block';
            };
            reader.readAsDataURL(file);
        });
    }
});
</script>
</div>
@endsection