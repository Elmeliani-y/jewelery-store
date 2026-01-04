@extends('layouts.vertical', ['title' => 'إدارة عناوين IP المحظورة'])

@section('css')
<style>
    .blocked-ip-card {
        border: 1px solid var(--bs-border-color);
        border-radius: 12px;
        transition: all 0.3s ease;
        background: var(--bs-card-bg);
    }
    .blocked-ip-card:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }
    .page-header {
        background: linear-gradient(135deg, #dc3545, #fd7e14);
        padding: 1.5rem;
        border-radius: 14px;
        color: #fff;
        margin-bottom: 1.5rem;
    }
    [data-bs-theme="dark"] .page-header {
        background: linear-gradient(135deg, #c82333, #e8590c);
    }
    .ip-badge {
        background: var(--bs-danger-bg-subtle);
        color: var(--bs-danger);
        padding: 0.5rem 1rem;
        border-radius: 8px;
        font-family: monospace;
        font-size: 1rem;
        font-weight: 600;
    }
    .stats-box {
        border: 1px solid var(--bs-border-color);
        border-radius: 12px;
        padding: 1.5rem;
        text-align: center;
        background: var(--bs-body-bg);
    }
    .stats-box h3 {
        font-size: 2rem;
        font-weight: bold;
        margin-bottom: 0.5rem;
        color: var(--bs-danger);
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <h4 class="mb-1">
                <i class="ri-shield-cross-line me-2"></i>
                إدارة عناوين IP المحظورة
            </h4>
            <p class="mb-0 opacity-75">عرض وإدارة عناوين IP المحظورة بسبب محاولات تسجيل دخول فاشلة</p>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="ri-check-line me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="ri-error-warning-line me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Stats -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="stats-box">
                <h3>{{ $blockedIps->total() }}</h3>
                <p class="text-muted mb-0">إجمالي السجلات</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stats-box">
                <h3>{{ $blockedIps->where('blocked_at', '!=', null)->count() }}</h3>
                <p class="text-muted mb-0">محظور حالياً</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stats-box">
                <form action="{{ route('q8r2s6t0.clear') }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف جميع السجلات؟')">
                    @csrf
                    <button type="submit" class="btn btn-danger">
                        <i class="ri-delete-bin-line me-1"></i>
                        حذف جميع السجلات
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Blocked IPs Table -->
    <div class="card blocked-ip-card">
        <div class="card-body">
            @if($blockedIps->isEmpty())
                <div class="text-center py-5">
                    <i class="ri-shield-check-line" style="font-size: 4rem; color: var(--bs-success);"></i>
                    <h5 class="mt-3">لا توجد عناوين IP محظورة</h5>
                    <p class="text-muted">النظام آمن حالياً</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>عنوان IP</th>
                                <th>محاولات فاشلة</th>
                                <th>آخر محاولة</th>
                                <th>تاريخ الحظر</th>
                                <th>الحالة</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($blockedIps as $ip)
                                <tr>
                                    <td>
                                        <span class="ip-badge">{{ $ip->ip_address }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-warning">{{ $ip->failed_attempts }}</span>
                                    </td>
                                    <td>
                                        @if($ip->last_attempt_at)
                                            <small class="text-muted">
                                                {{ $ip->last_attempt_at->diffForHumans() }}
                                            </small>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($ip->blocked_at)
                                            <small class="text-muted">
                                                {{ $ip->blocked_at->format('Y-m-d H:i') }}
                                            </small>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($ip->blocked_at)
                                            <span class="badge bg-danger">محظور</span>
                                        @else
                                            <span class="badge bg-success">نشط</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($ip->blocked_at)
                                            <form action="{{ route('q8r2s6t0.unblock') }}" method="POST" class="d-inline">
                                                @csrf
                                                <input type="hidden" name="ip_address" value="{{ $ip->ip_address }}">
                                                <button type="submit" class="btn btn-sm btn-success" title="إلغاء الحظر">
                                                    <i class="ri-lock-unlock-line"></i>
                                                    إلغاء الحظر
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-3">
                    {{ $blockedIps->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
