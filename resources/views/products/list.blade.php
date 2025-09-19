@extends('layouts.main')

@push('styles')
    <link rel="stylesheet" href="{{asset('css/staffs/list.css')}}">
@endpush

@section('content')

    @if (auth()->user()->role !== 'Supplier')
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
                            <p><span class="req-asterisk">*</span> Name</p>
                            <input type="text" name="name" maxlength="200" minlength="3" required>
                            @error('name')
                                <div class="invalid-feedback" style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <p><span class="req-asterisk">*</span> Suggested retail price (SRP)</p>
                            <input type="text" name="srp" maxlength="200" placeholder="&#8369; 0.00" minlength="3" required>
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
                            <p><span class="req-asterisk">*</span> Category</p>
                            <select name="category" id="" required>
                                <option value="">-- Select category --</option>
                                <option value="Frozen">Frozen</option>
                                <option value="Cuts">Cuts</option>
                                <option value="Eggs">Eggs</option>
                                <option value="Processed">Processed</option>
                            </select>
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
                            <button class="add-staff-btn btn-transition" data-bs-toggle="modal" data-bs-target="#add-product-modal">
                                <span style="font-size: 15px; margin: 0" class="material-symbols-outlined">add</span>
                                Add products
                            </button>
                        @endif

                    </div>

                </div>


                @if (auth()->user()->role !== 'Supplier')
                    <div class="content-body" style="background: #fff">

                        <table style="width:100%; border-collapse:collapse; border: 1px solid #fff;">
                            <thead style="background-color: #fff;">
                                <tr style="background:#fff; text-align: center; height: 30px; border-bottom: 1px solid #ccc;">
                                    <th>#</th>
                                    <th>Product ID</th>
                                    <th>Name</th>
                                    <th>Category</th>
                                    <th>SRP</th>
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
                                        <td>₱{{ number_format($product->srp, 2) }}</td>
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

@endpush
