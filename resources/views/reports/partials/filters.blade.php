<div class="card filters-card mb-3">
  <div class="card-body">
    <form method="GET" action="{{ url()->current() }}" class="row g-3 align-items-end">
      <div class="col-md-3">
        <label class="form-label small">الفرع</label>
        <select name="branch_id" class="form-select form-select-sm">
          <option value="">الكل</option>
          @foreach(($branches ?? []) as $b)
            <option value="{{ $b->id }}" {{ (string)request('branch_id') === (string)$b->id ? 'selected' : '' }}>{{ $b->name }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-3">
        <label class="form-label small">الموظف</label>
        <select name="employee_id" class="form-select form-select-sm">
          <option value="">الكل</option>
          @foreach(($employees ?? []) as $e)
            <option value="{{ $e->id }}" {{ (string)request('employee_id') === (string)$e->id ? 'selected' : '' }}>{{ $e->name }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-3">
        <label class="form-label small">الصنف</label>
        <select name="category_id" class="form-select form-select-sm">
          <option value="">الكل</option>
          @foreach(($categories ?? []) as $c)
            <option value="{{ $c->id }}" {{ (string)request('category_id') === (string)$c->id ? 'selected' : '' }}>{{ $c->name }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-3">
        <label class="form-label small">العيار</label>
        <select name="caliber_id" class="form-select form-select-sm">
          <option value="">الكل</option>
          @foreach(($calibers ?? []) as $cl)
            <option value="{{ $cl->id }}" {{ (string)request('caliber_id') === (string)$cl->id ? 'selected' : '' }}>{{ $cl->name }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-3">
        <label class="form-label small">نوع المصروف</label>
        <select name="expense_type_id" class="form-select form-select-sm">
          <option value="">الكل</option>
          @foreach(($expenseTypes ?? []) as $t)
            <option value="{{ $t->id }}" {{ (string)request('expense_type_id') === (string)$t->id ? 'selected' : '' }}>{{ $t->name }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-3">
        <label class="form-label small">من</label>
        <input type="date" name="date_from" value="{{ request('date_from', $filters['date_from'] ?? now()->startOfMonth()->format('Y-m-d')) }}" class="form-control form-control-sm" />
      </div>
      <div class="col-md-3">
        <label class="form-label small">إلى</label>
        <input type="date" name="date_to" value="{{ request('date_to', $filters['date_to'] ?? now()->endOfMonth()->format('Y-m-d')) }}" class="form-control form-control-sm" />
      </div>
      <div class="col-md-3 d-flex gap-2">
        <button class="btn btn-primary btn-sm flex-grow-1" type="submit"><iconify-icon icon="solar:filter-bold" class="me-1"></iconify-icon>تطبيق</button>
        <a class="btn btn-light btn-sm" href="{{ url()->current() }}">تفريغ</a>
      </div>
    </form>
  </div>
</div>
