@extends('layouts.app')

@section('title', 'Edit Department')

@section('content')
<div class="container-fluid">

    <div class="d-flex align-items-center gap-2 mb-4">
        <a href="{{ route('departments.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <h4 class="mb-0 fw-bold">Edit Department</h4>
            <p class="text-muted small mb-0">Changes will reflect immediately in the Quality Assurance Assessment form.</p>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('departments.update', $department) }}">
                        @csrf @method('PUT')

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Department Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" value="{{ old('name', $department->name) }}"
                                   class="form-control @error('name') is-invalid @enderror" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Applies To <span class="text-muted fw-normal small">(select all that apply, leave blank for organisation-wide)</span></label>
                            <div class="d-flex flex-wrap gap-3 mt-1">
                                @php $selectedCats = old('categories', $department->categories ?? []); @endphp
                                @foreach(\App\Models\Department::CATEGORIES as $key => $label)
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="categories[]"
                                               id="cat_{{ $key }}" value="{{ $key }}"
                                               {{ in_array($key, $selectedCats) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="cat_{{ $key }}">{{ $label }}</label>
                                    </div>
                                @endforeach
                            </div>
                            @error('categories')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">HOD / Incharge <span class="text-muted fw-normal small">(optional)</span></label>
                            <select name="hod_id" class="form-select @error('hod_id') is-invalid @enderror">
                                <option value="">— No HOD assigned —</option>
                                @foreach($hods as $hod)
                                    <option value="{{ $hod->id }}"
                                        {{ old('hod_id', $department->hod_id) == $hod->id ? 'selected' : '' }}>
                                        {{ $hod->name }}{{ $hod->department ? ' · '.$hod->department : '' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('hod_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Description</label>
                            <textarea name="description" rows="3"
                                      class="form-control @error('description') is-invalid @enderror"
                                      placeholder="Optional description...">{{ old('description', $department->description) }}</textarea>
                            @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1"
                                       {{ old('is_active', $department->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">Active (visible in assessment form)</label>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-success px-4">
                                <i class="bi bi-check2 me-1"></i>Update Department
                            </button>
                            <a href="{{ route('departments.index') }}" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card mt-3 border-danger">
                <div class="card-body">
                    <p class="fw-semibold text-danger mb-1">Delete Department</p>
                    <p class="text-muted small mb-3">Permanently removes this department. Existing feedback records are not affected.</p>
                    <form method="POST" action="{{ route('departments.destroy', $department) }}"
                          onsubmit="return confirm('Delete {{ $department->name }}? This cannot be undone.')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger">
                            <i class="bi bi-trash me-1"></i>Delete Department
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
