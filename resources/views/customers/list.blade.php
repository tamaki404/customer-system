@extends('layouts.main')

@push('styles')
    <link rel="stylesheet" href="{{asset('css/customers/list.css')}}">
@endpush

@section('content')
    @if (session('success') || session('error'))
        <div id="flash-message" 
            class="flash-message alert {{ session('success') ? 'alert-success' : 'alert-danger' }}"
            style="position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px; padding: 15px; border-radius: 5px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
            {{ session('success') ?? session('error') }}
        </div>
    @endif
   <div class="content-bg">

        @if (auth()->user()->role !== 'Customer')

        {{-- Set Sale & Discounts Modal --}}
        <div class="modal fade" id="set-promo-modal" tabindex="-1" aria-labelledby="setPromoLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <form class="modal-content shadow-sm border-0" method="POST" action="{{ route('set.sale_discount') }}">
                    @csrf
            
                    <div class="modal-header">
                        <p class="modal-title" id="requestActionLabel"> 
                        
                            Set sale & discounts
                        </p>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    {{-- Body --}}
                    <div class="modal-body">
                        <p class="note-notify">
                            <span class="material-symbols-outlined"> info </span>
                            <span>Products added will be automatically listed.</span>
                        </p>

                        {{-- Type --}}
                        <div class="form-group mt-3">
                            <label for="type" class="form-label">
                                <span class="req-asterisk">*</span> What will it be?
                            </label>
                            <select id="type" name="type" class="form-select" required>
                                <option value="" disabled {{ old('type') ? '' : 'selected' }}>-- Select type --</option>
                                <option value="Sale" {{ old('type') == 'Sale' ? 'selected' : '' }}>Sale</option>
                                <option value="Discount" {{ old('type') == 'Discount' ? 'selected' : '' }}>Discount</option>
                            </select>
                        </div>

                        {{-- Name --}}
                        <div class="form-group mt-3">
                            <label for="name" class="form-label">
                                <span class="req-asterisk">*</span> What would you like it to be called?
                            </label>
                            <input type="text" id="name" name="name" class="form-control" maxlength="100" placeholder="e.g. Summer Sale, Dealer Discount" required>
                        </div>

                        {{-- Description --}}
                        <div class="form-group mt-3">
                            <label for="description" class="form-label">Add a description (Recommended)</label>
                            <textarea id="description" name="description" maxlength="255" class="form-control" rows="2" placeholder="Optional short description...">{{ old('description') }}</textarea>
                        </div>

                        {{-- Value --}}
                        <div class="form-group mt-3">
                            <label class="form-label">
                                <span class="req-asterisk">*</span> How many is available to sell?
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
                                    <option value="percentage" {{ old('value_type') == 'Percentage' ? 'selected' : '' }}>%</option>
                                    <option value="fixed" {{ old('value_type') == 'Fixed' ? 'selected' : '' }}>₱ (Fixed)</option>
                                </select>
                            </div>
                        </div>

                        {{-- Account Type --}}
                        <div class="form-group mt-3">
                            <label for="category" class="form-label">
                                <span class="req-asterisk">*</span> Select account type to apply to
                            </label>
                            <select name="category" id="category" class="form-select" required>
                                <option value="" disabled {{ old('category') ? '' : 'selected' }}>-- Select account type --</option>
                                <option value="Wholesale" {{ old('category') == 'Wholesale' ? 'selected' : '' }}>Wholesale</option>
                                <option value="Distributor" {{ old('category') == 'Distributor' ? 'selected' : '' }}>Distributor</option>
                                <option value="HRI" {{ old('category') == 'HRI' ? 'selected' : '' }}>HRI</option>
                                <option value="Dealer" {{ old('category') == 'Dealer' ? 'selected' : '' }}>Dealer</option>
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
                        <button type="submit" class="btn btn-primary">Apply Sale/Discount</button>
                    </div>
                </form>
            </div>
        </div>


        @endif


        <div class="content-header">
            <div class="contents-display">
                <form action="{{ route('customers.list') }}" id="text-search" class="search-text-con" method="GET">
                    <input type="text" name="search" class="search-bar"
                        placeholder="Search by CUST ID. , Customer, Representative and status"
                        value="{{ request('search') }}"
                        style="outline:none;"
                    >
                    <button type="submit" class="search-btn"><span class="material-symbols-outlined">search</span></button>
                </form>


                <form action="{{ route('customers.list') }}" class="date-search" id="from-to-date" method="GET">
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

            @if (auth()->user()->role === "Admin")

            <div class="heading" style="display: flex; flex-direction: row; justify-content: space-between; margin-top: 10px;">
                <p class="heading">Customers list</p>
                    @if ( auth()->user()->role === 'Admin')
                        <div style="display: flex; flex-direction: row; margin-left: auto; gap: 10px">
                            <button class="add-staff-btn btn-transition" data-bs-toggle="modal" data-bs-target="#set-promo-modal" style="font-size: 14px">
                                <span style="font-size: 15px; margin: 0" class="material-symbols-outlined">shoppingmode</span>
                                 Sale and discounts
                            </button>
                    
                        </div>
                    @endif
            </div>
            @elseif (auth()->user()->role_type === "sales_representative")
                <p class="heading">Assigned customers</p>
            @else
                <p class="heading">Customers list</p>
            @endif

        </div>
        <div class="content-body" style="background: #fff">
            <table style="width:100%; border-collapse:collapse; border: 1px solid #fff;">
                <thead style="background-color: #fff;">
                    <tr style="background:#fff; text-align: center; height: 30px; border-bottom: 1px solid #ccc;">
                        <th>#</th>
                        <th>CUST ID.</th>
                        <th>Customer</th>
                        <th>Email</th>
                        <th>Type</th>
                        <th>Sales Agent</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($customers as $customer)
                        <tr onclick="window.location.href='{{ route('customers.customer', ['customer_id' => $customer->customer_id]) }}'">
                            <th>{{ $loop->iteration }}</th>
                            <td>{{ $customer->customer_id }}</td>
                            <td>{{ $customer->company_name }}</td>
                            <td>{{ $customer->user->email_address }}</td>
                            <td>{{ $customer->category }}</td>
                            <td>
                                @if ($customer->account_status->staff_id !== NULL)
                                    {{ $customer->staff_name }}
                                @else 
                                    --

                                @endif
                            </td>

                            <td>{{ $customer->account_status->account_status }}</td>
                        </tr>

                    @endforeach
                </tbody>
            </table>
       
        </div>

        <div class="pagination-div">
            <p>Showing {{ $customers->firstItem() }} to {{ $customers->lastItem() }} of {{ $customers->total() }} entries</p>
            {{ $customers->links() }}
        </div>

   </div>
@endsection
