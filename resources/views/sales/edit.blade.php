@extends('layouts.vertical')

@section('title') تعديل المبيعة @endsection

@section('css')
<style>
    .edit-header {background: linear-gradient(115deg,var(--bs-warning) 0%,#ffb547 100%); color:#212529; border-radius:.85rem; padding:1.3rem 1.6rem; margin-bottom:1.4rem; position:relative; overflow:hidden;}
    .edit-header:before {content:''; position:absolute; inset:0; background:radial-gradient(circle at 88% 18%, rgba(255,255,255,.35), transparent 70%);}    
    .edit-header h4 {font-weight:600;}
    .badge-meta {font-weight:500;}
    .form-section {background:var(--bs-body-bg); border:1px solid var(--bs-border-color); border-radius:.75rem; padding:1.25rem 1.25rem; margin-bottom:1rem;}
    .form-section h6 {font-size:.8rem; letter-spacing:.07em; text-transform:uppercase; font-weight:600; opacity:.7; margin-bottom:.75rem;}
    .required:after {content:'*'; color:var(--bs-danger); margin-right:4px;}
    .payment-group {display:none;}
    .payment-visible {display:block !important;}
    .text-mono {font-family:ui-monospace,SFMono-Regular,Menlo,monospace; direction:ltr;}
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="edit-header d-flex flex-column flex-lg-row justify-content-between gap-3">
        <div>
            <h4 class="mb-1"><i class="mdi mdi-pencil-outline me-1"></i> تعديل المبيعة <span class="text-mono">{{ $sale->invoice_number }}</span></h4>
            <small class="opacity-75">قم بتحديث بيانات الفاتورة ثم احفظ التغييرات</small>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <span class="badge badge-meta bg-primary-subtle text-primary"><i class="mdi mdi-storefront-outline me-1"></i>{{ $sale->branch->name }}</span>
            <span class="badge badge-meta bg-info-subtle text-info"><i class="mdi mdi-account-outline me-1"></i>{{ $sale->employee->name }}</span>
            <span class="badge badge-meta bg-warning-subtle text-warning"><i class="mdi mdi-gold me-1"></i>{{ $sale->caliber->name }}</span>
            <span class="badge badge-meta bg-secondary-subtle text-secondary"><i class="mdi mdi-shape-outline me-1"></i>{{ $sale->category->name }}</span>
        </div>
    </div>

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="mdi mdi-alert-circle-outline me-1"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form method="POST" action="{{ route('sales.update', $sale) }}" novalidate>
        @csrf
        @method('PUT')

        <div class="row g-3">
            <div class="col-xl-8">
                <div class="form-section">
                    <h6>البيانات الأساسية</h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label required">الفرع</label>
                            <select name="branch_id" class="form-select @error('branch_id') is-invalid @enderror" id="branch_id">
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}" {{ old('branch_id', $sale->branch_id)==$branch->id?'selected':'' }}>{{ $branch->name }}</option>
                                @endforeach
                            </select>
                            @error('branch_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required">الموظف</label>
                            <select name="employee_id" class="form-select @error('employee_id') is-invalid @enderror" id="employee_id">
                                @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}" {{ old('employee_id', $sale->employee_id)==$employee->id?'selected':'' }}>{{ $employee->name }}</option>
                                @endforeach
                            </select>
                            @error('employee_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required">الفئة</label>
                            <select name="category_id" class="form-select @error('category_id') is-invalid @enderror">
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id', $sale->category_id)==$category->id?'selected':'' }}>{{ $category->name }}</option>
                                @endforeach
                            </select>
                            @error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required">العيار</label>
                            <select name="caliber_id" class="form-select @error('caliber_id') is-invalid @enderror">
                                @foreach($calibers as $caliber)
                                    <option value="{{ $caliber->id }}" {{ old('caliber_id', $sale->caliber_id)==$caliber->id?'selected':'' }}>{{ $caliber->name }}</option>
                                @endforeach
                            </select>
                            @error('caliber_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required">الوزن (جم)</label>
                            <input type="number" step="0.001" name="weight" value="{{ old('weight', $sale->weight) }}" class="form-control @error('weight') is-invalid @enderror" placeholder="0.000">
                            @error('weight')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required">المبلغ الإجمالي</label>
                            <input type="number" step="0.01" name="total_amount" value="{{ old('total_amount', $sale->total_amount) }}" class="form-control @error('total_amount') is-invalid @enderror" placeholder="0">
                            @error('total_amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label">ملاحظات</label>
                            <textarea name="notes" rows="3" class="form-control @error('notes') is-invalid @enderror" placeholder="إضافة ملاحظات ..">{{ old('notes', $sale->notes) }}</textarea>
                            @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <h6>طريقة الدفع</h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label required">طريقة الدفع</label>
                            <select name="payment_method" id="payment_method" class="form-select @error('payment_method') is-invalid @enderror">
                                @php($pm = old('payment_method', $sale->payment_method))
                                <option value="cash" {{ $pm=='cash'?'selected':'' }}>نقداً</option>
                                <option value="network" {{ $pm=='network'?'selected':'' }}>شبكة</option>
                                <option value="mixed" {{ $pm=='mixed'?'selected':'' }}>مختلط</option>
                            </select>
                            @error('payment_method')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 payment-group" id="cash_group">
                            <label class="form-label required">مبلغ نقدي</label>
                            <input type="number" step="0.01" name="cash_amount" value="{{ old('cash_amount', $sale->cash_amount) }}" class="form-control @error('cash_amount') is-invalid @enderror" placeholder="0">
                            @error('cash_amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 payment-group" id="network_group">
                            <label class="form-label required">مبلغ شبكة</label>
                            <input type="number" step="0.01" name="network_amount" value="{{ old('network_amount', $sale->network_amount) }}" class="form-control @error('network_amount') is-invalid @enderror" placeholder="0">
                            @error('network_amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 payment-group" id="reference_group">
                            <label class="form-label required">رقم المرجع الشبكي</label>
                            <input type="text" name="network_reference" value="{{ old('network_reference', $sale->network_reference) }}" class="form-control @error('network_reference') is-invalid @enderror" placeholder="مثال: REF-123456">
                            @error('network_reference')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4">
                <div class="form-section">
                    <h6>المبالغ المحسوبة</h6>
                    <div class="mb-3">
                        <label class="form-label">الضريبة</label>
                        <div class="form-control bg-light text-mono" readonly>{{ number_format($sale->tax_amount,2) }}</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">الصافي</label>
                        <div class="form-control bg-light text-mono" readonly>{{ number_format($sale->net_amount,2) }}</div>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        <button type="submit" class="btn btn-warning"><i class="mdi mdi-content-save-outline me-1"></i> حفظ التعديلات</button>
                        <a href="{{ route('sales.show', $sale) }}" class="btn btn-secondary"><i class="mdi mdi-arrow-left me-1"></i> إلغاء</a>
                    </div>
                </div>
                <div class="form-section">
                    <h6>بيانات إضافية</h6>
                    <p class="mb-1 small text-muted">تاريخ الإنشاء: {{ $sale->created_at->format('Y-m-d H:i') }}</p>
                    <p class="mb-0 small text-muted">آخر تحديث: {{ $sale->updated_at->format('Y-m-d H:i') }}</p>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@section('script-bottom')
<script>
    function togglePaymentGroups(){
        const pm = document.getElementById('payment_method').value;
        const cash = document.getElementById('cash_group');
        const net = document.getElementById('network_group');
        const ref = document.getElementById('reference_group');
        cash.classList.remove('payment-visible');
        net.classList.remove('payment-visible');
        ref.classList.remove('payment-visible');
        if(pm==='cash'){ cash.classList.add('payment-visible'); }
        else if(pm==='network'){ net.classList.add('payment-visible'); ref.classList.add('payment-visible'); }
        else if(pm==='mixed'){ cash.classList.add('payment-visible'); net.classList.add('payment-visible'); ref.classList.add('payment-visible'); }
    }
    document.getElementById('payment_method').addEventListener('change', togglePaymentGroups);
    togglePaymentGroups();
</script>
@endsection
