@extends('layouts.vertical', ['title' => 'إدارة الأجهزة'])
@section('content')
<div class="container">
    <h2>الأجهزة</h2>
    <form method="POST" action="{{ route('settings.devices.generate') }}">
        @csrf
        <button type="submit" class="btn btn-primary">إنشاء رمز الربط</button>
    </form>
    @if(session('pairing_code'))
        <div class="alert alert-success mt-3 d-flex align-items-center gap-2">
            <span>رمز الربط: <strong id="pairingCode">{{ session('pairing_code') }}</strong> (صالح لمدة 10 دقائق)</span>
            <button class="btn btn-outline-secondary btn-sm" onclick="copyPairingCode()" type="button">نسخ الرمز</button>
        </div>
        <script>
        function copyPairingCode() {
            const code = document.getElementById('pairingCode').innerText;
            navigator.clipboard.writeText(code).then(function() {
                alert('تم نسخ الرمز!');
            }, function() {
                alert('حدث خطأ أثناء النسخ');
            });
        }
        </script>
    @endif
    <hr>
    <h4>الأجهزة المسجلة</h4>
    <table class="table">
        <thead>
            <tr>
                <th>الاسم</th>
                <th>تاريخ الإنشاء</th>
                <th>مدة الجلسة</th>
                <th>إجراء</th>
            </tr>
        </thead>
        <tbody>
            @foreach($devices as $device)
            <tr>
                <td>{{ $device->name }}</td>
                <td>{{ $device->created_at ? $device->created_at->format('Y-m-d H:i:s') : '' }}</td>
                <td>
                    @php
                        $start = $device->last_login_at ?: $device->created_at;
                    @endphp
                    @if($start)
                        {{ now()->diffForHumans($start, true) }}
                    @else
                        --
                    @endif
                </td>
                <td>
                    <form method="POST" action="{{ route('settings.devices.delete', $device->id) }}" class="delete-device-form">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn btn-danger btn-sm" onclick="confirmDelete(this)">حذف</button>
                    </form>
                </td>
            </tr>
            @endforeach
                <script>
                function confirmDelete(btn) {
                    if (confirm('هل أنت متأكد من حذف هذا الجهاز؟')) {
                        btn.closest('form').submit();
                    }
                }
                </script>
        </tbody>
    </table>
</div>
@endsection
