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
                                    <div id="edit_parent_tree" style="max-height:220px; overflow:auto; border:1px solid #eee; border-radius:6px; padding:8px;"></div>
                                    <div id="edit_parent_breadcrumb" style="margin-top:6px; font-size:12px; color:#666;"></div>
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
    buildEditParentTree();
});

function buildEditParentTree() {
    fetch("{{ route('products.tree') }}")
        .then(r => r.json())
        .then(data => {
            const root = document.getElementById('edit_parent_tree');
            root.innerHTML = '';
            const ul = document.createElement('ul');
            ul.style.listStyle = 'none';
            ul.style.paddingLeft = '0';
            data.forEach(node => ul.appendChild(makeEditParentTreeNode(node, [])));
            root.appendChild(ul);
        });
}

function makeEditParentTreeNode(node, path) {
    const li = document.createElement('li');
    li.style.margin = '2px 0';
    const row = document.createElement('div');
    row.style.display = 'flex';
    row.style.alignItems = 'center';
    row.style.gap = '6px';

    const toggle = document.createElement('span');
    toggle.textContent = node.children && node.children.length ? '▸' : '•';
    toggle.style.cursor = node.children && node.children.length ? 'pointer' : 'default';
    toggle.style.width = '14px';

    const label = document.createElement('button');
    label.type = 'button';
    label.textContent = node.name;
    label.className = 'btn btn-sm';
    label.style.padding = '2px 6px';
    label.style.border = '1px solid #ddd';
    label.style.background = '#fafafa';
    label.addEventListener('click', () => {
        document.getElementById('edit_final_parent_product_id').value = node.product_id;
        const bc = document.getElementById('edit_parent_breadcrumb');
        const names = path.concat([node.name]).join(' > ');
        bc.textContent = names;
        document.querySelectorAll('#edit_parent_tree button').forEach(b => b.style.background = '#fafafa');
        label.style.background = '#e7f1ff';
    });

    row.appendChild(toggle);
    row.appendChild(label);
    li.appendChild(row);

    const childUl = document.createElement('ul');
    childUl.style.listStyle = 'none';
    childUl.style.marginLeft = '16px';
    childUl.style.display = 'none';
    li.appendChild(childUl);

    if (node.children && node.children.length) {
        node.children.forEach(ch => childUl.appendChild(makeEditParentTreeNode(ch, path.concat([node.name]))));
        toggle.style.cursor = 'pointer';
    }

    toggle.addEventListener('click', () => {
        const open = childUl.style.display !== 'none';
        if (open) {
            childUl.style.display = 'none';
            toggle.textContent = '▸';
            return;
        }
        childUl.style.display = 'block';
        toggle.textContent = '▾';
        if (!childUl.hasChildNodes()) {
            fetch(`{{ url('/products') }}/${node.product_id}/children`)
                .then(r => r.json())
                .then(children => {
                    if (!children || !children.length) return;
                    children.forEach(ch => childUl.appendChild(makeEditParentTreeNode(ch, path.concat([node.name]))));
                });
        }
    });
    return li;
}
</script>



@endpush