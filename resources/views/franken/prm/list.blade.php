@extends('layouts.main')

@push('styles')
    <link rel="stylesheet" href="{{asset('css/staffs/list.css')}}">
    <link rel="stylesheet" href="{{asset('css/franken/pr/list.css')}}">

@endpush

@section('content')

    @if ($errors->any())
        <div class="alert alert-danger" style="margin: 10px;">
            <h6 style="margin-bottom: 10px; font-weight: bold;">Validation Errors:</h6>
            <ul style="margin: 0; padding-left: 20px;">
                @foreach ($errors->all() as $error)
                    <li style="font-size: 14px;">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    @if (session('success') || session('error'))
        <div 
            id="flash-message"
            class="flash-message 
                {{ session('success') ? 'alert-success' : 'alert-danger' }}">
            <strong>
                {{ session('success') ? 'Success:' : ' Error:' }}
            </strong>
            {{ session('success') ?? session('error') }}
        </div>
    @endif

        {{-- Create promo modal --}}
        <div class="modal fade" id="set-promo-modal" tabindex="-1" aria-labelledby="setPromoLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <form class="modal-content shadow-sm border-0" method="POST" action="{{ route('prm.create') }}">
                    @csrf
                    <div class="modal-header">
                        <p class="modal-title" id="requestActionLabel"> 
                            List a promo
                        </p>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    {{-- Body --}}
                    <div class="modal-body">
                        <p class="note-notify">
                            <span class="material-symbols-outlined"> info </span>
                            <span>Make sure promo details are correct before applying</span>
                        </p>
                        {{-- Name --}}
                        <div class="form-group mt-3">
                            <label for="name" class="form-label">
                                <span class="req-asterisk">*</span> Name promo
                            </label>
                            <input type="text" id="name" name="name" class="form-control" maxlength="100" placeholder="e.g. Summer Sale, Dealer Discount" required>
                        </div>
                        {{-- Description --}}
                        <div class="form-group mt-3">
                            <label for="description" class="form-label">Add a short description (Recommended)</label>
                            <textarea id="description" name="description" maxlength="255" class="form-control" rows="2" placeholder="Optional short description...">{{ old('description') }}</textarea>
                        </div>
                        {{-- Value --}}
                        <div class="form-group mt-3">
                            <label class="form-label">
                                <span class="req-asterisk">*</span> Quantity to sell
                            </label>
                            <div class="d-flex gap-2 align-items-center">
                                <input type="number" name="quantity" class="form-control" placeholder="Enter quantity" required>
                            </div>
                        </div>
                        {{-- Value --}}
                        <div class="form-group mt-3">
                            <label class="form-label">
                                <span class="req-asterisk">*</span> Set value
                            </label>
                            <div class="d-flex gap-2 align-items-center">
                                <input type="number" name="value" class="form-control" min="0" step="0.01" placeholder="Enter value" required>
                                <select name="value_type" id="value_type" class="form-select w-auto" required>
                                    <option value="percentage" >%</option>
                                    <option value="fixed">₱ (Fixed)</option>
                                </select>
                            </div>
                        </div>
                        {{-- Account Type --}}
                        <div class="form-group mt-3">
                            <label for="category" class="form-label">
                                <span class="req-asterisk">*</span> Select account type to apply to
                            </label>
                            <select name="category" id="category" class="form-select" required>
                                <option value="HRI" {{ old('category') == 'HRI' ? 'selected' : '' }}>HRI</option>
                            </select>
                        </div>
                        {{-- Product --}}
                        <div class="form-group mt-3">
                            <label for="product" class="form-label">
                                <span class="req-asterisk">*</span> Select product to apply to
                            </label>
                            <select name="product" id="product" class="form-select" required>
                            <option value="" disabled {{ old('product') ? '' : 'selected' }}>-- Select product --</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->product_id }}">{{ ucfirst($product->name) }}</option>
                                @endforeach
                            </select>
                        </div>
                        {{-- Effectivity Dates --}}
                        <div class="form-group mt-4 p-3 border rounded-3" style="background: #fafafa;">
                                <label class="form-label  mb-2">Effectivity Period</label>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="start_date" class="form-label small">Start Date</label>
                                        <input type="datetime-local" id="start_date" name="start_date" class="form-control" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="end_date" class="form-label small">End Date</label>
                                        <input type="datetime-local" id="end_date" name="end_date" class="form-control" required>
                                    </div>
                                </div>
                        </div>
                        {{-- Hidden --}}
                        <input type="hidden" name="user_id" value="{{ Auth()->user()->user_id }}" required>
                    </div>
                    {{-- Footer --}}
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Apply promo</button>
                    </div>
                </form>
            </div>
        </div>
        <div class="content-bg">
                <div class="content-header">
                    <div class="contents-display">
                        <form action="{{ route('purchaseorders.list') }}" id="text-search" class="search-text-con" method="GET">
                            <input type="text" name="search" class="search-bar"
                                placeholder="Search by PO ID, Customer, Status"
                                value="{{ request('search') }}"
                                style="outline:none;"
                            >
                            <button type="submit" class="search-btn"><span class="material-symbols-outlined">search</span></button>
                        </form>
                        <form action="{{ route('purchaseorders.list') }}" class="date-search" id="from-to-date" method="GET">
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
                        <p class="heading">Promos</p>
                        @if ( auth()->user()->role === 'Admin')
                            <div style="display: flex; flex-direction: row; margin-left: auto; gap: 10px">
                                <button class="add-staff-btn btn-transition" data-bs-toggle="modal" data-bs-target="#set-promo-modal" style="font-size: 14px">
                                    <span style="font-size: 15px; margin: 0" class="material-symbols-outlined">shoppingmode</span>
                                    List a promo
                                </button>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="content-body" style="background: #fff">
                    <table style="width:100%; border-collapse:collapse; border: 1px solid #fff;">
                        <thead style="background-color: #fff;">
                            <tr style="background:#fff; text-align: center; height: 30px; border-bottom: 1px solid #ccc;">
                                <th>#</th>
                                <th>Created</th>
                                <th>Title</th>
                                <th>Exclusive for</th>
                                <th>Validity</th>
                                <th>Quantity</th>
                                <th>Product</th>
                                <th>Discount</th>
                            </tr>
                        </thead>
                        <tbody>                                
                            @foreach ($promos as $promo)
                                <tr>
                                    <td>{{$loop->iteration}}</td>
                                    <td>{{ $promo->created_at->format('F j, Y') }}</td>
                                    <td>{{ $promo->name }}</td>
                                    <td>{{ $promo->category }}</td>
                                    <td>{{\Carbon\Carbon::parse($promo->start_date)->format('F j, Y') }} - {{\Carbon\Carbon::parse($promo->end_date)->format('F j, Y') }}</td>
                                    <td>
                                        {{ $promo->quantity }}
                                    </td>
                                    <td>{{ $promo->product->name }}</td>
                                    <th>
                                        @if ($promo->value_type==="Percentage")
                                            {{ number_format($promo->value, 0) }}%  OFF
                                        @elseif ($promo->value_type==="Fixed")
                                            ₱{{ $promo->value }} LESS
                                        @endif
                                    </th>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            <div class="pagination-div" style="margin-top: 15px;">
                <p>Showing {{ $promos->firstItem() }} to {{ $promos->lastItem() }} of {{ $promos->total() }} entries</p>
                {{ $promos->links() }}
            </div>
        </div>


@endsection

@push('scripts')


@endpush
