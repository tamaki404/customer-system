@extends('layouts.main')


@push('styles')
    <link rel="stylesheet" href="{{ asset('css/views/customer.css') }}">
@endpush



@section('content')




@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

   <div class="content-bg" >
        <div class="content-header">
            <div class="contents-display">
                <p>
                    <a href="{{ route('products.list') }}">< Products list</a>
                </p>
            </div>

            <div class="title-actions">
                <p class="heading">Product {{$product->product_id}}</p>



                <div>
                    <button data-bs-toggle="modal" data-bs-target="#modify-action" class="btn-transition">Modify product</button>
                </div>

          

            </div>
       


        </div>

                <div class="content-body" style="padding: 10px; border: none; height: auto;">
                    <p>{{ $product->name}}</p>
                    <div style="margin-top:12px;">
                        <button data-bs-toggle="modal" data-bs-target="#edit-category" class="btn-transition">Edit Category</button>
                    </div>
                </div>

                <div class="modal fade" id="edit-category" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog">
                        <form class="modal-content" method="POST" action="{{ route('products.updateParent') }}">
                            @csrf
                            <div class="modal-header">
                                <p class="modal-title">Update Product Parent</p>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="form-group">
                                    <p>Parent Product (optional)</p>
                                    <div id="edit_parent_picker">
                                        <select id="edit_parent_product_root" data-level="0" onchange="onEditParentLevelChange(this)">
                                            <option value="">-- No parent (root product) --</option>
                                        </select>
                                    </div>
                                    <input type="hidden" name="parent_product_id" id="edit_final_parent_product_id">
                                </div>
                                <input type="hidden" name="product_id" value="{{ $product->product_id }}">
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary">Save</button>
                            </div>
                        </form>
                    </div>
                </div>



   </div>
@endsection



@push('scripts')

<script>
document.addEventListener('DOMContentLoaded', function() {
    fetch("{{ route('products.tree') }}")
        .then(r => r.json())
        .then(data => populateEditLevelOptions(document.getElementById('edit_parent_product_root'), data));
});
function populateEditLevelOptions(selectEl, nodes) {
    [...selectEl.options].slice(1).forEach(o => o.remove());
    nodes.forEach(n => {
        const opt = document.createElement('option');
        opt.value = n.product_id;
        opt.textContent = n.name;
        selectEl.appendChild(opt);
    });
}

function onEditParentLevelChange(selectEl) {
    const level = parseInt(selectEl.getAttribute('data-level') || '0', 10);
    const wrapper = document.getElementById('edit_parent_picker');

    // Remove deeper levels only
    const deeper = [...wrapper.querySelectorAll('select')].filter(s => parseInt(s.getAttribute('data-level')||'0',10) > level);
    deeper.forEach(s => s.remove());

    const value = selectEl.value;
    document.getElementById('edit_final_parent_product_id').value = value;

    if (!value) return;

    fetch(`{{ url('/products') }}/${value}/children`)
        .then(r => r.json())
        .then(children => {
            if (!children.length) return;
            const next = document.createElement('select');
            next.setAttribute('data-level', String(level+1));
            next.innerHTML = '<option value="">-- Select child product --</option>';
            next.addEventListener('change', function() { onEditParentLevelChange(next); });
            wrapper.appendChild(next);
            children.forEach(c => {
                const opt = document.createElement('option');
                opt.value = c.product_id;
                opt.textContent = c.name;
                next.appendChild(opt);
            });
        });
}
</script>



@endpush