@extends('layouts.vertical', ['title' => 'إدارة الأجهزة'])
@section('content')
@if(auth()->user() && auth()->user()->isAdmin())
<div class="container">
    <h2>إدارة الأجهزة</h2>
    @if(session('user_link'))
        <div class="alert alert-info">
            رابط دخول مستخدم (بدون جهاز):
            <a href="{{ session('user_link') }}" target="_blank" id="user-link">{{ session('user_link') }}</a>
            <button class="btn btn-sm btn-secondary ms-2" onclick="navigator.clipboard.writeText(document.getElementById('user-link').href)">نسخ الرابط</button>
        </div>
    @endif
    @if(session('device_link'))
        <div class="alert alert-success">
            رابط الجهاز الجديد:
            <a href="{{ session('device_link') }}" target="_blank" id="device-link">{{ session('device_link') }}</a>
            <button class="btn btn-sm btn-secondary ms-2" onclick="navigator.clipboard.writeText(document.getElementById('device-link').href)">نسخ الرابط</button>
        </div>
    @endif
    <form method="POST" action="{{ route('settings.devices.generateUserLink') }}" class="mb-3">
        @csrf
        <div class="input-group">
            <input type="text" name="name" class="form-control" placeholder="اسم الجهاز" required>
            <button type="submit" class="btn btn-primary">توليد رابط دخول مستخدم (بدون جهاز)</button>
        </div>
        @error('name')
            <div class="text-danger mt-2">{{ $message }}</div>
        @enderror
    </form>
    <hr>
    <h4>الأجهزة المسجلة</h4>
    <table class="table">
        <thead>
            <tr>
                <th>الاسم</th>
                <th>تاريخ آخر دخول</th>
                <th>رمز الجهاز</th>
                <th>إجراء</th>
            </tr>
        </thead>
        <tbody>
            @foreach($devices as $device)
            <tr>
                <td>{{ $device->name }}</td>
                <td>{{ $device->last_login_at ? $device->last_login_at : '--' }}</td>
                <td style="font-size: 0.8em;">{{ $device->token }}</td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteDeviceModal{{ $device->id }}">حذف</button>
                    <!-- Delete Confirmation Modal -->
                    <div class="modal fade" id="deleteDeviceModal{{ $device->id }}" tabindex="-1" aria-labelledby="deleteDeviceLabel{{ $device->id }}" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="deleteDeviceLabel{{ $device->id }}">تأكيد حذف الجهاز</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    هل أنت متأكد أنك تريد حذف هذا الجهاز؟ لا يمكن التراجع عن هذا الإجراء.
                                </div>
                                <div class="modal-footer">
                                    <form method="POST" action="{{ route('settings.devices.delete', $device->id) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                                        <button type="submit" class="btn btn-danger">تأكيد الحذف</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @if(session('success'))
    <script>
        window.onload = function() {
            var successModal = new bootstrap.Modal(document.getElementById('successDeleteModal'));
            successModal.show();
        }
    </script>
    <div class="modal fade" id="successDeleteModal" tabindex="-1" aria-labelledby="successDeleteLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="successDeleteLabel">تم الحذف بنجاح</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    {{ session('success') }}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" data-bs-dismiss="modal">موافق</button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@else
<div class="container"><div class="alert alert-danger mt-4">غير مصرح لك بعرض هذه الصفحة.</div></div>
@endif
@endsection
