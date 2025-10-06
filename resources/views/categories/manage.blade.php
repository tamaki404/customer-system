@extends('layouts.main')

@section('content')
    <div class="content-bg">
        <div class="content-header">
            <div class="title-actions">
                <p class="heading">Manage Categories</p>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="content-body" style="background:#fff;padding:16px;">
            <form method="POST" action="{{ route('categories.store') }}">
                @csrf
                <div class="form-group">
                    <p><span class="req-asterisk">*</span> Category Name</p>
                    <input type="text" name="name" required>
                    @error('name')
                        <div class="invalid-feedback" style="color:#dc3545;font-size:12px;margin-top:5px;">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <p>Parent Category (optional)</p>
                    <select name="parent_id" id="parent_select">
                        <option value="">-- None (Root) --</option>
                    </select>
                    @error('parent_id')
                        <div class="invalid-feedback" style="color:#dc3545;font-size:12px;margin-top:5px;">{{ $message }}</div>
                    @enderror
                </div>

                <div style="margin-top:12px;">
                    <button type="submit" class="btn-transition">Save Category</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    fetch("{{ route('categories.tree') }}")
        .then(r => r.json())
        .then(data => {
            const parentSelect = document.getElementById('parent_select');
            // flatten roots first-level only for parent selection; deeper can be set after creating
            data.forEach(c => {
                const opt = document.createElement('option');
                opt.value = c.category_id;
                opt.textContent = c.name;
                parentSelect.appendChild(opt);
                // also allow selecting any child as parent of deeper nodes
                c.children.forEach(sc => {
                    const opt2 = document.createElement('option');
                    opt2.value = sc.category_id;
                    opt2.textContent = '— ' + sc.name;
                    parentSelect.appendChild(opt2);
                });
            });
        });
});
</script>
@endpush


