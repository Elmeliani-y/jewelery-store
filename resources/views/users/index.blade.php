@extends('layouts.vertical', ['title' => 'إدارة المستخدمين'])
@section('title','إدارة المستخدمين')

@section('css')
<style>
    .user-card{border-radius:12px;transition:.25s;background:var(--bs-card-bg);border:1px solid var(--bs-border-color);} 
    .user-card:hover{box-shadow:0 4px 14px rgba(0,0,0,.08);transform:translateY(-2px);} 
    [data-bs-theme="dark"] .user-card{border-color:#2e2e2e;} 
    .status-badge{padding:.35rem .75rem;border-radius:8px;font-size:.75rem;font-weight:600;} 
    .page-header{background:linear-gradient(135deg,#0d6efd,#6610f2);padding:1.75rem 1.5rem;border-radius:14px;color:#fff;margin-bottom:2rem;} 
    [data-bs-theme="dark"] .page-header{background:linear-gradient(135deg,#1d2530,#3a4757);} 
    .stats-small{font-size:.75rem;opacity:.8;} 
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <h4 class="mb-1"><iconify-icon icon="solar:users-group-rounded-bold-duotone" class="fs-4 me-2"></iconify-icon> إدارة المستخدمين</h4>
            <p class="mb-0 opacity-75">استعراض وإدارة مستخدمي النظام</p>
        </div>
        <div>
            <a href="{{ route('users.create') }}" class="btn btn-light">
                <iconify-icon icon="solar:add-circle-bold" class="me-1"></iconify-icon> إضافة مستخدم
            </a>
        </div>
    </div>

    @include('layouts.partials.flash')

    <div class="row">
        @forelse($users as $user)
            <div class="col-md-6 col-xl-4 mb-4">
                <div class="user-card p-3 h-100 d-flex flex-column">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <h5 class="mb-1">{{ $user->name }}</h5>
                            @if($user->role === 'admin')
                            <span class="status-badge bg-danger-subtle text-danger">مدير</span>
                            @elseif($user->role === 'accountant')
                            <span class="status-badge bg-warning-subtle text-warning">محاسب</span>
                            @elseif($user->role === 'branch')
                            <span class="status-badge bg-info-subtle text-info">فرع</span>
                            @else
                            <span class="status-badge bg-secondary-subtle text-secondary">أخرى</span>
                            @endif
                        </div>
                        <div class="avatar-lg">
                            <div class="avatar-title rounded-circle bg-soft-primary text-primary fs-3">
                                {{ substr($user->name, 0, 1) }}
                            </div>
                        </div>
                    </div>

                    <div class="mt-3 flex-grow-1">
                        <div class="d-flex justify-content-between mb-1 stats-small">
                            <span>اسم المستخدم:</span>
                            <strong>{{ $user->username }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2 stats-small">
                            <span>الفرع:</span>
                            <strong>{{ $user->branch?->name ?? '-' }}</strong>
                        </div>
                        <div class="text-muted small">✉ {{ $user->email }}</div>
                        <div class="text-muted small">📅 {{ $user->created_at->format('Y-m-d') }}</div>
                    </div>

                    <div class="mt-3 d-flex gap-2">
                        <a href="{{ route('users.show', $user) }}" class="btn btn-secondary btn-sm flex-fill">
                            <iconify-icon icon="solar:eye-bold" class="me-1"></iconify-icon>عرض
                        </a>
                        <a href="{{ route('users.edit', $user) }}" class="btn btn-primary btn-sm flex-fill">
                            <iconify-icon icon="solar:pen-bold" class="me-1"></iconify-icon>تعديل
                        </a>
                        @if($user->id !== auth()->id())
                        <form action="{{ route('users.destroy', $user) }}" method="POST" class="flex-fill delete-form">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm w-100" onclick="return confirm('هل أنت متأكد من حذف هذا المستخدم؟')">
                                <iconify-icon icon="solar:trash-bin-trash-bold" class="me-1"></iconify-icon>حذف
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="text-center py-5">
                    <iconify-icon icon="solar:users-group-rounded-bold-duotone" class="fs-1 text-muted mb-3" style="font-size:4rem"></iconify-icon>
                    <p class="text-muted">لا توجد مستخدمين</p>
                    <a href="{{ route('users.create') }}" class="btn btn-primary mt-2">
                        <iconify-icon icon="solar:add-circle-bold" class="me-1"></iconify-icon> إضافة مستخدم جديد
                    </a>
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection
