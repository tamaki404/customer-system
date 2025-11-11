@extends('layouts.main')

@push('styles')
    <link rel="stylesheet" href="{{asset('css/staffs/list.css')}}">
@endpush

@section('content')


    @if (session('success') || session('error'))
        <div id="flash-message"
            class="flash-message alert {{ session('success') ? 'alert-success' : 'alert-danger' }}">
            {{ session('success') ?? session('error') }}
        </div>
    @endif


    @if (auth()->user()->role !== 'Customer')

        {{-- add product modal --}}
        <div class="modal fade" id="add-product-modal" tabindex="-1" aria-labelledby="requestActionLabel" aria-hidden="true">
            <div class="modal-dialog">
                <form class="modal-content" method="POST" action="{{ route('product.add') }}">
                    @csrf
            
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
                            <!-- Product ID -->
                            <div class="form-group">
                                <p><span class="req-asterisk">*</span> Product ID</p>
                                <input type="text" name="product_id" maxlength="50" minlength="3" value="{{ old('product_id') }}" required>
                                @error('product_id')
                                    <div class="invalid-feedback" style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Name -->
                            <div class="form-group">
                                <p><span class="req-asterisk">*</span> Name</p>
                                <input type="text" name="name" maxlength="100" minlength="3" value="{{ old('name') }}" required>
                                @error('name')
                                    <div class="invalid-feedback" style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <p><span class="req-asterisk">*</span> Description </p>
                                <textarea 
                                    id="description"
                                    name="description"
                                    minlength="5"
                                    maxlength="255"
                                    required
                                >{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback" style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</div>
                                @enderror
                            </div>
                            <!-- Base Price (nullable in migration) -->
                            <div class="form-group">
                                <p>Base price (Optional)</p>
                                <input type="number" step="0.01" name="base_price" placeholder="&#8369; 0.00" value="{{ old('base_price') }}">
                                @error('base_price')
                                    <div class="invalid-feedback" style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Category - MUST match migration enum values -->
                            <div class="form-group">
                                <p><span class="req-asterisk">*</span> Category</p>
                                <select name="category" required>
                                    <option value="">-- Select category --</option>
                                    <option value="By products" {{ old('category') == 'By products' ? 'selected' : '' }}>By products</option>
                                    <option value="Cut ups" {{ old('category') == 'Cut ups' ? 'selected' : '' }}>Cut ups</option>
                                    <option value="Fillets" {{ old('category') == 'Fillets' ? 'selected' : '' }}>Fillets</option>
                                    <option value="Dressed chickens" {{ old('category') == 'Dressed chickens' ? 'selected' : '' }}>Dressed chickens</option>
                                    <option value="Uncategorized" {{ old('category') == 'Uncategorized' ? 'selected' : '' }}>Uncategorized</option>
                                </select>                            
                                @error('category')
                                    <div class="invalid-feedback" style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Measurement Type - MUST match migration enum values -->
                            <div class="form-group">
                                <p><span class="req-asterisk">*</span> Measurement type</p>
                                <select name="measurement_type" required>
                                    <option value="">-- Select measurement type --</option>
                                    <option value="Heads" {{ old('measurement_type') == 'Heads' ? 'selected' : '' }}>Heads</option>
                                    <option value="Kilos" {{ old('measurement_type') == 'Kilos' ? 'selected' : '' }}>Kilos</option>
                                    <option value="Heads&Kilos" {{ old('measurement_type') == 'Heads&Kilos' ? 'selected' : '' }}>Heads & Kilos</option>
                                </select>                            
                                @error('measurement_type')
                                    <div class="invalid-feedback" style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Hidden Fields -->
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
        {{-- <div class="modal fade" id="set-promo-modal" tabindex="-1" aria-labelledby="requestActionLabel" aria-hidden="true">
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

                        <div class="form-group mt-3">
                            <p><span class="req-asterisk">*</span> Select city to apply to</p>
                            <select name="city_selected" id="city_select" class="form-control" required>
                                <option value="">-- Select city --</option>
                                @foreach($cities as $city)
                                    <option value="{{ $city }}">{{ ucfirst($city) }}</option>
                                @endforeach
                            </select>
                        </div>

                        <p id="customer-count" class="mt-2 text-muted">0 customers selected within city</p>

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
        </div> --}}

    @endif
        <div class="content-bg" style="overflow: hidden">
            <div class="content-header">
                <div class="contents-display">
                    <form action="{{ route('products.list') }}" id="text-search" class="search-text-con" method="GET">
                        <input type="text" name="search" class="search-bar"
                            placeholder="Search by CUST ID. , Customer, Representative and status"
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
                                {{-- <button class="set-ceiling-btn btn-transition" data-bs-toggle="modal" data-bs-target="#set-promo-modal">
                                    <span style="font-size: 15px; margin: 0" class="material-symbols-outlined">shoppingmode</span>
                                    Sales & discounts
                                </button> --}}
                            </div>
                        @endif
                </div>
            </div>

                @if (auth()->user()->role !== 'Customer')
                    <div class="main-board">
                        <div class="ceiling-table" style="height: 80%; padding: 0;" >
                            <div class="table-section" style="height:100%; overflow: auto; padding: 0;">
                                <table style="position: relative; border-collapse: collapse; padding: 0;">
                                    <thead style="background-color: #fff; padding: 10px; z-index: 1; position: sticky; top: 0; box-shadow: rgba(50, 50, 93, 0.25) 0px 2px 5px -1px, rgba(0, 0, 0, 0.3) 0px 1px 3px -1px;">
                                        <tr style="text-align: left; height: 30px;">
                                            <th style="width: 100%">
                                                <span class="material-symbols-outlined" style="font-size: 16px">
                                                    price_change
                                                </span>
                                                Active Promos & Discounts
                                            </th>
                                            <th>{{ $activePromosCount }}</th>
                                        </tr>
                                    </thead>
                                        <tbody >
                                            @foreach($activePromos as $promo)
                                                @php
                                                    $now = \Carbon\Carbon::now();
                                                    if($promo->start_date <= $now && $promo->end_date >= $now) {
                                                        $status = 'Active';
                                                        $badge = 'success';
                                                    } elseif($promo->start_date > $now) {
                                                        $status = 'Upcoming';
                                                        $badge = 'warning';
                                                    } else {
                                                        $status = 'Expired';
                                                        $badge = 'secondary';
                                                    }
                                                    // Get remaining quantity
                                                    $remainingQty = $promo->quantity ?? 0;
                                                    $measurementUnit = $promo->product->measurement_type === 'Heads' ? 'pcs' : 'kg';
                                                @endphp
                                                <tr>
                                                    <td title="Click to view details" style="cursor: pointer;" onclick="window.location.href='{{ route('products.product', ['product_id' => $promo->product->product_id]) }}'">
                                                        <strong>[{{ $promo->category }}] - {{ ucfirst($promo->name) }}</strong>
                                                        <p style="margin: 0;">
                                                            <span>{{ ucfirst($promo->product->name) }}</span>
                                                            
                                                        </p>
                                                        @if($promo->value_type === 'Fixed')
                                                            ₱{{ number_format($promo->value, 2) }}
                                                            <span class="badge bg-{{ $badge }}">{{ $promo->type }}</span>
                                                        @else
                                                            {{ $promo->value }}%
                                                            <span class="badge bg-{{ $badge }}">{{ $promo->type }}</span>
                                                        @endif


                                                        @if($remainingQty > 0)
                                                            <span style="background: #e8f5e9; color: #2e7d32; padding: 3px 8px; border-radius: 12px; font-size: 12px; font-weight: 600;">
                                                                <i class="fas fa-box"></i> {{ $remainingQty }} {{ $measurementUnit }} left
                                                            </span>
                                                        @else
                                                            <span style="background: #ffebee; color: #c62828; padding: 3px 8px; border-radius: 12px; font-size: 12px; font-weight: 600;">
                                                                <i class="fas fa-times-circle"></i> Sold Out
                                                            </span>
                                                        @endif 
                                                            <br>
                                                        <small class="text-muted">
                                                            {{ \Carbon\Carbon::parse($promo->start_date)->format('M d, Y') }} → 
                                                            {{ \Carbon\Carbon::parse($promo->end_date)->format('M d, Y') }}
                                                        </small>
                                                        &nbsp;&nbsp;
                                                        {{-- <span class="badge bg-{{ $badge }}">{{ $status }}</span> --}}
                                                    </td>
                                                    <td style="color: #888; font-size: 12px;">#{{ $loop->iteration }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="table-div">
                            <div class="content-body" style="background: #fff; height: 600px; overflow: hidden; padding: 0;">
                                <div style="overflow: auto; height: 100%; padding: 0;">
                                    <table style=" width:100%; height: 100%; border-collapse:collapse; border: 1px solid #fff; padding: 0;">
                                        <thead style="background-color: #fff; position: sticky; top: 0; z-index: 1; box-shadow: rgba(50, 50, 93, 0.25) 0px 2px 5px -1px, rgba(0, 0, 0, 0.3) 0px 1px 3px -1px;">
                                            <tr style="background:#fff; text-align: center; height: 30px; border-bottom: 1px solid #ccc;">
                                                <th>#</th>
                                                <th>Product ID</th>
                                                <th>Name</th>
                                                <th>Category</th>
                                                <th>Base price</th>
                                                <th>Measurement</th>
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
                                                    <td>{{ $product->measurement_type }}</td>
                                                    <td>--</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
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

                @elseif (auth()->user()->role === 'Customer')

                    <div class="content-body" style="background: #fff">
                        <table style="width:100%; border-collapse:collapse; border: 1px solid #fff;">
                            <thead style="background-color: #fff;">
                                <tr style="background:#fff; text-align: center; height: 30px; border-bottom: 1px solid #ccc;">
                                    <th>#</th>
                                    <th>Product ID</th>
                                    <th>Name</th>
                                    <th>Category</th>
                                    <th>Measurement</th>
                                    <th>Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($setProducts as $setProduct)
                                    <tr>
                                        <th>{{ $loop->iteration }}</th>
                                        <td>{{ $setProduct->product_id }}</td>
                                        <td>{{ $setProduct->product->name }}</td>
                                        <td>{{ $setProduct->product->category }}</td>
                                        <td>{{ $setProduct->product->measurement_type }}</td>
                                        <td>₱{{ number_format($setProduct->nego_price, 2) }}</td>
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


    // Customer count update
    const customerCounts = @json($customerCounts);
    const citySelect = document.getElementById("city_select");
    const customerText = document.getElementById("customer-count");

    citySelect.addEventListener("change", function () {
        const city = citySelect.value.toLowerCase();
        const count = customerCounts[city] || 0;
        customerText.textContent = `${count} customer(s) selected within city`;
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
