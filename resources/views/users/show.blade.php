@extends('layouts.vertical')

@section('title', 'تفاصيل المستخدم')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <h4 class="mb-0">تفاصيل المستخدم</h4>
                <div class="page-title-right">
                    <a href="{{ route('users.index') }}" class="btn btn-secondary">
                        <iconify-icon icon="solar:arrow-right-bold-duotone" class="me-1"></iconify-icon>
                        العودة للقائمة
                    </a>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="mdi mdi-check-all me-2"></i>
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-4">
                        <div class="avatar-lg me-3">
                            <div class="avatar-title rounded-circle bg-soft-primary text-primary fs-3">
                                {{ substr($user->name, 0, 1) }}
                            </div>
                        </div>
                        <div>
                            <h5 class="mb-1">{{ $user->name }}</h5>
                            <p class="text-muted mb-0">
                                @if($user->role === 'admin')
                                <span class="badge bg-danger">مدير</span>
                                @elseif($user->role === 'accountant')
                                <span class="badge bg-warning">محاسب</span>
                                @elseif($user->role === 'branch')
                                <span class="badge bg-info">فرع</span>
                                @else
                                <span class="badge bg-secondary">أخرى</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <p class="text-muted mb-1">اسم المستخدم</p>
                                <h6>{{ $user->username }}</h6>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <p class="text-muted mb-1">البريد الإلكتروني</p>
                                <h6>{{ $user->email }}</h6>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <p class="text-muted mb-1">الدور</p>
                                <h6>
                                    @if($user->role === 'admin')
                                    مدير النظام
                                    @elseif($user->role === 'accountant')
                                    محاسب
                                    @elseif($user->role === 'branch')
                                    مدير فرع
                                    @else
                                    أخرى
                                    @endif
                                </h6>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <p class="text-muted mb-1">الفرع</p>
                                <h6>{{ $user->branch?->name ?? 'بدون فرع' }}</h6>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <p class="text-muted mb-1">تاريخ الإنشاء</p>
                                <h6>{{ $user->created_at->format('Y-m-d H:i') }}</h6>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <p class="text-muted mb-1">آخر تحديث</p>
                                <h6>{{ $user->updated_at->format('Y-m-d H:i') }}</h6>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="d-flex gap-2">
                        <a href="{{ route('users.edit', $user) }}" class="btn btn-warning">
                            <iconify-icon icon="solar:pen-bold-duotone" class="me-1"></iconify-icon>
                            تعديل
                        </a>
                        @if($user->id !== auth()->id())
                        <button type="button" class="btn btn-danger" onclick="confirmDelete()">
                            <iconify-icon icon="solar:trash-bin-trash-bold-duotone" class="me-1"></iconify-icon>
                            حذف
                        </button>
                        <form id="delete-form" action="{{ route('users.destroy', $user) }}" method="POST" class="d-none">
                            @csrf
                            @method('DELETE')
                        </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete() {
    if (confirm('هل أنت متأكد من حذف هذا المستخدم؟')) {
        document.getElementById('delete-form').submit();
    }
}
</script>
@endsection
