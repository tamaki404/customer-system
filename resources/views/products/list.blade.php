@extends('layouts.main')

@push('styles')
    <link rel="stylesheet" href="{{asset('css/staffs/list.css')}}">
@endpush

@section('content')

    @if (auth()->user()->role !== 'Supplier')

        {{-- add product modal --}}
        <div class="modal fade" id="add-product-modal" tabindex="-1" aria-labelledby="requestActionLabel" aria-hidden="true">
            <div class="modal-dialog">
                <form class="modal-content"  method="POST" action="{{ route('product.add') }}">
                    @csrf
            
                    @if (session('success'))
                        <div class="alert alert-success" style="margin: 10px;">
                            <h6 style="margin-bottom: 5px; font-weight: bold;">Success:</h6>
                            <p style="margin: 0; font-size: 14px;">{{ session('success') }}</p>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger" style="margin: 10px;">
                            <h6 style="margin-bottom: 5px; font-weight: bold;">Error:</h6>
                            <p style="margin: 0; font-size: 14px;">{{ session('error') }}</p>
                        </div>
                    @endif
                
                    <div class="modal-header">
                        <p class="modal-title" id="requestActionLabel">Add product form</p>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    
                    <div class="modal-body">
                        <p class="note-notify">
                            <span class="material-symbols-outlined"> info </span>
                            <span>Products added will be automatically listed.</span>

                        </p>

                        <div class="modal-option-groups">
                            <div class="form-group">
                                <p><span class="req-asterisk">*</span> Prie</p>
                                <input type="text" name="name" maxlength="200" minlength="3" required>
                                @error('name')
                                    <div class="invalid-feedback" style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <p><span class="req-asterisk">*</span> Suggested retail price (base_price)</p>
                                <input type="text" name="base_price" maxlength="200" placeholder="&#8369; 0.00" minlength="3" required>
                                @error('name')
                                    <div class="invalid-feedback" style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <p><span class="req-asterisk">*</span> Product ID</p>
                                <input type="text" name="product_id" maxlength="200" minlength="3" required>
                                @error('name')
                                    <div class="invalid-feedback" style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <p>Parent Product (optional)</p>
                                <div id="parent_tree" style="max-height:220px; overflow:auto; border:1px solid #eee; border-radius:6px; padding:8px;"></div>
                                <div id="parent_breadcrumb" style="margin-top:6px; font-size:12px; color:#666;"></div>
                                <input type="hidden" name="parent_product_id" id="final_parent_product_id">
                            </div>
                            <div>
                                <p><span class="req-asterisk">*</span> Category</p>
                                <select name="category" id="" required>
                                    <option value="">-- Select category --</option>
                                    <option value="Frozen">Frozen</option>
                                    <option value="Processed">Processed</option>
                                    <option value="Chicken">Chicken</option>
                                </select>                            
                                @error('name')
                                    <div class="invalid-feedback" style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</div>
                                @enderror
                            </div>
                            <div>
                                <p><span class="req-asterisk">*</span> Unit</p>
                                <select name="unit" id="" required>
                                    <option value="">-- Select unit --</option>
                                    <option value="Pack">Pack</option>
                                    <option value="Box">Box</option>
                                    <option value="Bag">Bag</option>
                                    <option value="Piece">Piece</option>
                                </select>                            
                                @error('name')
                                    <div class="invalid-feedback" style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</div>
                                @enderror
                            </div>
                            <div>
                                <p>Weight (Optional)</p>
                                <select name="weight" id="">
                                    <option value="">-- Select weight --</option>
                                    <option value="Kilogram">Kilogram (kg)</option>
                                    <option value="Gram">Gram (g)</option>
                                    <option value="Piece">Piece (pc)</option>
                                </select>                            @error('name')
                                    <div class="invalid-feedback" style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</div>
                                @enderror
                            </div>
                            <div>

                                


                            </div>
                            <input type="hidden" name="status" value="Listed">
                            <input type="hidden" name="added_by" value="{{ auth()->user()->user_id }}">
                        </div>
        

                    </div>
                    
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="add-staff-submit">List product</button>
                    </div>


                
                </form>
            </div>
        </div>

        {{-- set ceiling modal --}}
        <div class="modal fade" id="set-ceiling-modal" tabindex="-1" aria-labelledby="requestActionLabel" aria-hidden="true">
            <div class="modal-dialog">
                <form class="modal-content" method="POST" action="{{ route('set.global_ceiling') }}">
                    @csrf

                    <div class="modal-header">
                        <p class="modal-title">Set Global Ceiling Price</p>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <p class="note-notify">
                            <span class="material-symbols-outlined">info</span>
                            <span>Global ceilings only apply to future price changes. Existing prices remain until edited.</span>
                        </p>

                        {{-- Toggle between Fixed or Percentage --}}
                        <div class="form-group checkbox-form">
                            <input type="checkbox" id="use_percentage" name="use_percentage" style="width:auto;height:auto;">
                            <label for="use_percentage">Use percentage (%)</label>
                        </div>

                        <div id="fixed-input">
                            <p><span class="req-asterisk">*</span> Fixed price</p>
                            <input type="number" step="0.01" name="fixed_price" class="form-control">
                        </div>

                        <div id="percentage-input" style="display:none;">
                            <p><span class="req-asterisk">*</span> Percentage ceiling (%)</p>
                            <input type="number" step="0.01" name="percentage_ceiling" class="form-control">
                        </div>

                        <input type="hidden" name="method_select" id="method" value="Fixed">

                        {{-- City selection --}}
                        <div class="form-group mt-3">
                            <p><span class="req-asterisk">*</span> Select city to apply to</p>
                            <select name="city_selected" id="city_select" class="form-control" required>
                                <option value="">-- Select city --</option>
                                @foreach($cities as $city)
                                    <option value="{{ $city }}">{{ ucfirst($city) }}</option>
                                @endforeach
                            </select>
                        </div>

                        <p id="supplier-count" class="mt-2 text-muted">0 suppliers selected within city</p>

                        {{-- Effectivity --}}
                        <div class="form-group mt-3 p-2 rounded" style="box-shadow: #f8912a30 0px 0px 0px 3px;">
                            <label class="form-label">Effectivity</label>
                            <div class="d-flex gap-3">
                                <div>
                                    <label for="start_date">Start date</label>
                                    <input type="datetime-local" id="start_date" name="start_date" class="form-control" required>
                                </div>
                                <div>
                                    <label for="end_date">End date</label>
                                    <input type="datetime-local" id="end_date" name="end_date" class="form-control" required>
                                </div>
                            </div>
                        </div>

                        <input type="hidden" name="staff_id" value="{{ auth()->id() }}">
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Apply Ceiling Price</button>
                    </div>
                </form>

            </div>
        </div>


    @endif
        <div class="content-bg">
                <div class="content-header">
                    <div class="contents-display">
                        <form action="{{ route('products.list') }}" id="text-search" class="search-text-con" method="GET">
                            <input type="text" name="search" class="search-bar"
                                placeholder="Search by SUP ID. , Supplier, Representative and status"
                                value="{{ request('search') }}"
                                style="outline:none;"
                            >
                            <button type="submit" class="search-btn"><span class="material-symbols-outlined">search</span></button>
                        </form>


                        <form action="{{ route('products.list') }}" class="date-search" id="from-to-date" method="GET">
                            <p>Date range</p>
                            <div class="from-to-picker">
                                <div class="month-div">
                                    <span>From</span>
                                    <input type="date" name="from_date" class="input-date"
                                        value="{{ request('from_date', now()->startOfMonth()->format('Y-m-d')) }}"
                                        onchange="this.form.submit()">
                                </div>
                                <div class="month-div">
                                    <span>To</span>
                                    <input type="date" name="to_date" class="input-date"
                                        value="{{ request('to_date', now()->endOfMonth()->format('Y-m-d')) }}"
                                        onchange="this.form.submit()">
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="heading" style="display: flex; flex-direction: row; justify-content: space-between; margin-top: 10px;">
                        <p class="heading">Products list</p>
                            @if ( auth()->user()->role === 'Admin')
                                <div style="display: flex; flex-direction: row; margin-left: auto; gap: 10px">
                                    <button class="add-staff-btn btn-transition" data-bs-toggle="modal" data-bs-target="#add-product-modal">
                                        <span style="font-size: 15px; margin: 0" class="material-symbols-outlined">add</span>
                                        Add products
                                    </button>
                                    <button class="set-ceiling-btn btn-transition" data-bs-toggle="modal" data-bs-target="#set-ceiling-modal">
                                        <span style="font-size: 15px; margin: 0" class="material-symbols-outlined">price_change</span>
                                        Ceiling price
                                    </button>
                                </div>
                            @endif


                    </div>

                </div>


                @if (auth()->user()->role !== 'Supplier')
                <div class="main-board">
                    <div class="ceiling-table">
                        <div class="table-section">
                            <table>
                                <thead style="background-color: #fff; padding: 10px;">
                                    <tr style="text-align: left;height: 30px;">

                                        <th style="display: flex; flex-direction: row; gap: 5px; align-items:center;">
                                            <span class="material-symbols-outlined" style="font-size: 16px">
                                                price_change
                                            </span>
                                            Ceiling price
                                        </th>
                                    </tr>
                                    
                                </thead>
                                    <tbody>
                                        @foreach($ceilings as $ceiling)
                                            @php
                                                $now = \Carbon\Carbon::now();
                                                if($ceiling->start_date <= $now && $ceiling->end_date >= $now) {
                                                    $status = 'Active';
                                                    $badge = 'success';
                                                } elseif($ceiling->start_date > $now) {
                                                    $status = 'Upcoming';
                                                    $badge = 'warning';
                                                } else {
                                                    $status = 'Expired';
                                                    $badge = 'secondary';
                                                }
                                            @endphp

                                            <tr>
                                                <td>
                                                    <strong>[{{ ucfirst($ceiling->city_selected) }}]</strong> – 
                                                    @if($ceiling->method === 'Fixed')
                                                        ₱{{ number_format($ceiling->fixed_price, 2) }}
                                                    @else
                                                        {{ $ceiling->percentage_ceiling }}%
                                                    @endif
                                                    <br>
                                                    <small class="text-muted">
                                                        {{ \Carbon\Carbon::parse($ceiling->start_date)->format('M d, Y') }} → 
                                                        {{ \Carbon\Carbon::parse($ceiling->end_date)->format('M d, Y') }}
                                                    </small>
                                                    &nbsp;&nbsp;
                                                    <span class="badge bg-{{ $badge }}">{{ $status }}</span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>

                            </table>
                        </div>
                        

                    </div>
                    <div class="table-div">
                        <div class="content-body" style="background: #fff">

                            <table style="width:100%; border-collapse:collapse; border: 1px solid #fff;">
                                <thead style="background-color: #fff;">
                                    <tr style="background:#fff; text-align: center; height: 30px; border-bottom: 1px solid #ccc;">
                                        <th>#</th>
                                        <th>Product ID</th>
                                        <th>Name</th>
                                        <th>Category</th>
                                        <th>Base price</th>
                                        <th>Ceiling price</th>
                                        <th>Unit</th>
                                        <th>Weight</th>
                                        <th>Sold</th>

                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($products as $product)
                                        <tr onclick="window.location.href='{{ route('products.product', ['product_id' => $product->product_id]) }}'">
                                            <th>{{ $loop->iteration }}</th>
                                            <td>{{ $product->product_id }}</td>
                                            <td>{{ $product->name }}</td>
                                            <td>{{ $product->category }}</td>
                                            <td>₱{{ number_format($product->base_price, 2) }}</td>
                                            <td>₱</td>
                                            <td>{{ $product->unit }}</td>
                                            <td>{{ $product->weight }}</td>
                                            <td>--</td>
                                        </tr>

                                    @endforeach
                                </tbody>
                            </table>
                    
                        </div>

                        <div class="pagination-div">
                            <p>50 out of 100 <span>2/3</span></p>
                            <div>
                                <button>Previous</button>
                                <button>Next</button>
                            </div>
                        </div>
                    </div>
                </div>


                @elseif (auth()->user()->role === 'Supplier')

                    <div class="content-body" style="background: #fff">

                        <table style="width:100%; border-collapse:collapse; border: 1px solid #fff;">
                            <thead style="background-color: #fff;">
                                <tr style="background:#fff; text-align: center; height: 30px; border-bottom: 1px solid #ccc;">
                                    <th>#</th>
                                    <th>Product ID</th>
                                    <th>Name</th>
                                    <th>Category</th>
                                    <th>Price</th>
                                    <th>Unit</th>
                                    <th>Weight</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($setProducts as $setProduct)
                                    <tr>
                                        <th>{{ $loop->iteration }}</th>
                                        <td>{{ $setProduct->product_id }}</td>
                                        <td>{{ $setProduct->product->name }}</td>
                                        <td>{{ $setProduct->product->category }}</td>
                                        <td>₱{{ number_format($setProduct->price, 2) }}</td>
                                        <td>{{ $setProduct->product->unit }}</td>
                                        <td>{{ $setProduct->product->weight }}</td>
                                    </tr>

                                @endforeach
                            </tbody>
                        </table>
                
                    </div>

                @endif

        </div>


@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        buildParentTree();
    });

    function buildParentTree() {
        fetch("{{ route('products.tree') }}")
            .then(r => r.json())
            .then(data => {
                const root = document.getElementById('parent_tree');
                root.innerHTML = '';
                const ul = document.createElement('ul');
                ul.style.listStyle = 'none';
                ul.style.paddingLeft = '0';
                data.forEach(node => ul.appendChild(makeParentTreeNode(node, [])));
                root.appendChild(ul);
            });
    }

    function makeParentTreeNode(node, path) {
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
        document.getElementById('final_parent_product_id').value = node.product_id;
        const bc = document.getElementById('parent_breadcrumb');
        const names = path.concat([node.name]).join(' > ');
        bc.textContent = names;
        document.querySelectorAll('#parent_tree button').forEach(b => b.style.background = '#fafafa');
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
        node.children.forEach(ch => childUl.appendChild(makeParentTreeNode(ch, path.concat([node.name]))));
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
                    children.forEach(ch => childUl.appendChild(makeParentTreeNode(ch, path.concat([node.name]))));
                });
        }
    });
    return li;
}
</script>

<script>


    // Supplier count update
    const supplierCounts = @json($supplierCounts);
    const citySelect = document.getElementById("city_select");
    const supplierText = document.getElementById("supplier-count");

    citySelect.addEventListener("change", function () {
        const city = citySelect.value.toLowerCase();
        const count = supplierCounts[city] || 0;
        supplierText.textContent = `${count} supplier(s) selected within city`;
    });

</script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const checkbox = document.getElementById('use_percentage');
    const fixedInput = document.getElementById('fixed-input');
    const percentInput = document.getElementById('percentage-input');
    const methodField = document.getElementById('method');

    checkbox.addEventListener('change', () => {
        if (checkbox.checked) {
            fixedInput.style.display = 'none';
            percentInput.style.display = 'block';
            methodField.value = 'Percentage';
        } else {
            fixedInput.style.display = 'block';
            percentInput.style.display = 'none';
            methodField.value = 'Fixed';
        }
    });
});
</script>


@endpush
