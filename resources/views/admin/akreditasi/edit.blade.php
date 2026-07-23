@extends('layouts.app')
@section('title', 'Edit Akreditasi')
@section('page-title', 'Edit Akreditasi')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm" style="border-radius:20px;">
                <div class="card-header" style="background:white;border-bottom:1px solid #f0f2f5;padding:16px 20px;">
                    <h6 class="fw-semibold mb-0">
                        <i class="bi bi-pencil text-warning me-2"></i>
                        Edit Akreditasi: {{ $akreditasi->nama }}
                    </h6>
                </div>
                <div class="card-body p-4">

                    @if ($akreditasi->programStudi()->count() > 0)
                        <div class="alert alert-warning border-0 small mb-4">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            Akreditasi ini digunakan oleh
                            <strong>{{ $akreditasi->programStudi()->count() }} program studi</strong>.
                            Perubahan nominal KIP akan langsung berpengaruh pada seluruh mahasiswa KIP
                            di program studi tersebut.
                        </div>
                    @endif

                    <form action="{{ route('admin.akreditasi.update', $akreditasi) }}" method="POST">
                        @csrf @method('PUT')

                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                Nama Akreditasi <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror"
                                value="{{ old('nama', $akreditasi->nama) }}">
                            @error('nama')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                Nominal UKT KIP <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" name="nominal_kip"
                                    class="form-control @error('nominal_kip') is-invalid @enderror"
                                    value="{{ old('nominal_kip', $akreditasi->nominal_kip) }}" min="0"
                                    step="1000">
                            </div>
                            @error('nominal_kip')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Keterangan</label>
                            <textarea name="keterangan" class="form-control @error('keterangan') is-invalid @enderror" rows="3">{{ old('keterangan', $akreditasi->keterangan) }}</textarea>
                            @error('keterangan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                Urutan <span class="text-danger">*</span>
                            </label>
                            <input type="number" name="urutan" class="form-control @error('urutan') is-invalid @enderror"
                                value="{{ old('urutan', $akreditasi->urutan) }}" min="0">
                            @error('urutan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="aktif" value="1"
                                    id="aktifSwitch" {{ old('aktif', $akreditasi->aktif) ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="aktifSwitch">
                                    Status Aktif
                                </label>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-warning">
                                <i class="bi bi-save me-1"></i> Update
                            </button>
                            <a href="{{ route('admin.akreditasi.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-1"></i> Kembali
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
