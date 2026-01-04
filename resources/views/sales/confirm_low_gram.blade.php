@extends('layouts.vertical', ['title' => 'تنبيه: متوسط الجرام منخفض'])

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="alert alert-warning text-center p-4">
                <h4 class="mb-3"><iconify-icon icon="solar:warning-triangle-bold" class="text-warning fs-2 me-2"></iconify-icon>تنبيه: متوسط الجرام أقل من الحد الأدنى</h4>
                <p class="fs-5">متوسط الجرام في هذه الفاتورة هو <strong>{{ number_format($gramAvg, 2) }}</strong> جرام<br>
                الحد الأدنى المسموح به هو <strong>{{ number_format($minGramAvg, 2) }}</strong> جرام (قابل للتعديل من <a href="{{ route('h4i8j3k7') }}" class="text-primary text-decoration-underline">إعدادات النظام</a>).</p>
                <p class="mb-4">هل ترغب في الاستمرار بحفظ الفاتورة بهذا المتوسط المنخفض؟</p>
                <form method="POST" action="{{ route('t6u1v5w8.store') }}">
                    @csrf
                    @foreach($validated as $key => $value)
                        @if(is_array($value))
                            @foreach($value as $i => $item)
                                @foreach($item as $k => $v)
                                    <input type="hidden" name="{{ $key }}[{{ $i }}][{{ $k }}]" value="{{ $v }}">
                                @endforeach
                            @endforeach
                        @else
                            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                        @endif
                    @endforeach
                    <input type="hidden" name="confirm_low_gram" value="1">
                    <div class="d-flex justify-content-center gap-3">
                        <button type="submit" class="btn btn-warning px-4">موافق، احفظ الفاتورة</button>
                        <a href="{{ url()->previous() }}" class="btn btn-light px-4">تعديل البيانات</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
