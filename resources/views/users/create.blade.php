@extends('layouts.vertical')

@section('title', 'إضافة مستخدم جديد')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="mb-0">إضافة مستخدم جديد</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('users.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">الاسم الكامل <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                   value="{{ old('name') }}" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">اسم المستخدم <span class="text-danger">*</span></label>
                                <input type="text" name="username" class="form-control @error('username') is-invalid @enderror" 
                                       value="{{ old('username') }}" required>
                                @error('username')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">كلمة المرور <span class="text-danger">*</span></label>
                                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">تأكيد كلمة المرور <span class="text-danger">*</span></label>
                                <input type="password" name="password_confirmation" class="form-control" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">الدور <span class="text-danger">*</span></label>
                                <select name="role" id="roleSelect" class="form-select @error('role') is-invalid @enderror" required>
                                    <option value="">اختر الدور</option>
                                    <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>مدير</option>
                                    <option value="accountant" {{ old('role') === 'accountant' ? 'selected' : '' }}>محاسب</option>
                                    <option value="branch" {{ old('role') === 'branch' ? 'selected' : '' }}>فرع</option>
                                </select>
                                @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6 mb-3" id="branchField">
                                <label class="form-label">الفرع</label>
                                <select name="branch_id" class="form-select @error('branch_id') is-invalid @enderror">
                                    <option value="">بدون فرع</option>
                                    @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>
                                        {{ $branch->name }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('branch_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <iconify-icon icon="solar:check-circle-bold-duotone" class="me-1"></iconify-icon>
                                حفظ
                            </button>
                            <a href="{{ route('users.index') }}" class="btn btn-secondary">
                                <iconify-icon icon="solar:close-circle-bold-duotone" class="me-1"></iconify-icon>
                                إلغاء
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const roleSelect = document.getElementById('roleSelect');
        const branchField = document.getElementById('branchField');
        
        function toggleBranchField() {
            const role = roleSelect.value;
            if (role === 'admin' || role === 'accountant') {
                branchField.style.display = 'none';
            } else {
                branchField.style.display = 'block';
            }
        }
        
        roleSelect.addEventListener('change', toggleBranchField);
        toggleBranchField();
    });
</script>
@endsection
