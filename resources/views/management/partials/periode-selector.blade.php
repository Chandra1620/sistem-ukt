{{-- Pemilih periode analitik. Butuh variabel: $periodes, $selectedPeriode --}}
@php
    $__sel = (string) ($selectedPeriode ?? 'all');
@endphp
<form method="GET" class="periode-selector d-flex align-items-center gap-2">
    @foreach (request()->except('periode') as $__k => $__v)
        @if (!is_array($__v))
            <input type="hidden" name="{{ $__k }}" value="{{ $__v }}">
        @endif
    @endforeach
    <label for="periode-select" class="small text-muted fw-semibold mb-0 text-nowrap">
        <i class="bi bi-calendar-range me-1"></i>Periode
    </label>
    <select id="periode-select" name="periode" class="form-select form-select-sm" style="min-width: 210px;"
        onchange="this.form.submit()">
        <option value="all" {{ $__sel === 'all' ? 'selected' : '' }}>Semua Periode</option>
        @foreach ($periodes as $__p)
            @php
                $__label = $__p->nama;
                if (!empty($__p->tahun)) {
                    $__label .= ' (' . $__p->tahun . ')';
                }
                if (($__p->status ?? null) === 'aktif') {
                    $__label .= ' • aktif';
                }
            @endphp
            <option value="{{ $__p->id }}" {{ $__sel === (string) $__p->id ? 'selected' : '' }}>{{ $__label }}</option>
        @endforeach
    </select>
</form>
