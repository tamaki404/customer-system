@extends('layouts.main')


@push('styles')
    <link rel="stylesheet" href="{{ asset('css/views/customer.css') }}">
    <link rel="stylesheet" href="{{ asset('css/views/products/product.css') }}">

@endpush



@section('content')


    @if (session('success') || session('error'))
        <div id="flash-message"
            class="flash-message alert {{ session('success') ? 'alert-success' : 'alert-danger' }}">
            {{ session('success') ?? session('error') }}
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
                <div class="heading">
                    <div class="name">
                        <span>{{ $product->name }}</span>
                        @if ($product->status === "Listed")
                            <p class="status" data-state="Listed">
                                <span class="material-symbols-outlined icon">check_circle</span>Listed
                            </p>
                        @elseif ($product->status === "Unlisted")
                            <p class="status" data-state="Unlisted">
                                <span class="material-symbols-outlined icon">cancel</span>Unlisted
                            </p>
                        @endif
                    </div>
                    <div class="prod-details">
                        <p class="price">
                            <strong>{{ $product->base_price }}</strong>
                            <span>PHP</span>
                        </p>
                        -
                        <p>
                            <span>{{$product->category}}</span>
                        </p>
                        -
                        <div class="id">
                            <span id="product_id">{{ $product->product_id }}</span>
                            <button id="copy-btn"><span class="material-symbols-outlined">content_copy</span></button>
                        </div>

                        <div id="flash-message" class="flash-message alert" style="display:none;"></div>

                    </div>
                </div>
                <div>
                    <button data-bs-toggle="modal" data-bs-target="#modify-action" class="btn-transition">
                        <span class="material-symbols-outlined">edit</span>
                        Modify product
                    </button>
                </div>
            </div>
        </div>

        <!-- Modify Product Modal -->
        <div class="modal fade" id="modify-action" tabindex="-1" aria-labelledby="modifyProductLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <form class="modal-content" method="POST" action="{{ route('product.update', $product->product_id) }}">
                        @csrf
                        @method('PUT')

                        <div class="modal-header">
                            <p class="modal-title" id="modifyProductLabel">Modify product</p>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        
                        <div class="modal-body">
                            <p class="note-notify">
                                <span class="material-symbols-outlined"> info </span>
                                <span>Update product information below. Changes will be reflected immediately.</span>
                            </p>

                            <div class="modal-option-groups">
                                <!-- Product ID (Read-only or editable based on your requirements) -->
                                <div class="form-group">
                                    <p><span class="req-asterisk">*</span> Product ID</p>
                                    <input type="text" 
                                        name="product_id" 
                                        maxlength="50" 
                                        minlength="3" 
                                        value="{{ old('product_id', $product->product_id) }}" 
                                        required
                                        readonly
                                        style="background-color: #f5f5f5; cursor: not-allowed;">
                                    <small style="color: #666; font-size: 11px; display: block; margin-top: 3px;">
                                        Product ID cannot be changed
                                    </small>
                                    @error('product_id')
                                        <div class="invalid-feedback" style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Name -->
                                <div class="form-group">
                                    <p><span class="req-asterisk">*</span> Name</p>
                                    <input type="text" 
                                        name="name" 
                                        maxlength="100" 
                                        minlength="3" 
                                        value="{{ old('name', $product->name) }}" 
                                        required>
                                    @error('name')
                                        <div class="invalid-feedback" style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Description -->
                                <div class="form-group">
                                    <p><span class="req-asterisk">*</span> Description</p>
                                    <textarea 
                                        id="description"
                                        name="description"
                                        minlength="5"
                                        maxlength="255"
                                        required
                                    >{{ old('description', $product->description) }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback" style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Base Price -->
                                <div class="form-group">
                                    <p>Base price (Optional)</p>
                                    <input type="number" 
                                        step="0.01" 
                                        name="base_price" 
                                        placeholder="&#8369; 0.00" 
                                        value="{{ old('base_price', $product->base_price) }}">
                                    @error('base_price')
                                        <div class="invalid-feedback" style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Category -->
                                <div class="form-group">
                                    <p><span class="req-asterisk">*</span> Category</p>
                                    <select name="category" required>
                                        <option value="">-- Select category --</option>
                                        <option value="By products" {{ old('category', $product->category) == 'By products' ? 'selected' : '' }}>By products</option>
                                        <option value="Cut ups" {{ old('category', $product->category) == 'Cut ups' ? 'selected' : '' }}>Cut ups</option>
                                        <option value="Fillets" {{ old('category', $product->category) == 'Fillets' ? 'selected' : '' }}>Fillets</option>
                                        <option value="Dressed chickens" {{ old('category', $product->category) == 'Dressed chickens' ? 'selected' : '' }}>Dressed chickens</option>
                                        <option value="Uncategorized" {{ old('category', $product->category) == 'Uncategorized' ? 'selected' : '' }}>Uncategorized</option>
                                    </select>                            
                                    @error('category')
                                        <div class="invalid-feedback" style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Measurement Type -->
                                <div class="form-group">
                                    <p><span class="req-asterisk">*</span> Measurement type</p>
                                    <select name="measurement_type" required>
                                        <option value="">-- Select measurement type --</option>
                                        <option value="Heads" {{ old('measurement_type', $product->measurement_type) == 'Heads' ? 'selected' : '' }}>Heads</option>
                                        <option value="Kilos" {{ old('measurement_type', $product->measurement_type) == 'Kilos' ? 'selected' : '' }}>Kilos</option>
                                        <option value="Heads&Kilos" {{ old('measurement_type', $product->measurement_type) == 'Heads&Kilos' ? 'selected' : '' }}>Heads & Kilos</option>
                                    </select>                            
                                    @error('measurement_type')
                                        <div class="invalid-feedback" style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Status (optional - if you want to allow changing status) -->
                                <div class="form-group">
                                    <p><span class="req-asterisk">*</span> Status</p>
                                    <select name="status" required>
                                        <option value="Listed" {{ old('status', $product->status) == 'Listed' ? 'selected' : '' }}>List</option>
                                        <option value="Unlisted" {{ old('status', $product->status) == 'Unlisted' ? 'selected' : '' }}>Unlist</option>
                                    </select>                            
                                    @error('status')
                                        <div class="invalid-feedback" style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Hidden field for tracking who updated -->
                                <input type="hidden" name="updated_by" value="{{ auth()->user()->user_id }}">
                            </div>
                        </div>
                        
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Update Product</button>
                        </div>
                    </form>
                </div>
        </div>

        <div class="product-con">
            <div class="left-board">
                <div class="revenue-details">
                    <p class="upper-revenue">
                        <span class="title">Monthly recurring revenue</span>
                        <span class="revenue">12,000 PHP</span>
                        <span class="total">200 Total invoice</span>
                    </p>
                </div>
                <div class="product-det">
                    <div class="width">
                        <p class="detail-title">Details</p>
                        <p>
                            <span class="detail-name">Name</span>
                            <span class="detail-value">{{ $product->name }}</span>
                        </p>
                        <p>
                            <span class="detail-name">Description</span>
                            <span class="detail-value">{{ ($product->description) ?? "-" }}</span>
                        </p>
                        <p>
                            <span class="detail-name">Base price</span>
                            <span class="detail-value">Php {{ $product->base_price }} </span>
                        </p>
                        <p>
                            <span class="detail-name">Category</span>
                            <span class="detail-value">{{ $product->category }}</span>
                        </p>
                        <p>
                            <span class="detail-name">Measurement</span>
                            <span class="detail-value">{{ $product->measurement_type }}</span>
                        </p>
                        <p>
                            <span class="detail-name">Added by</span>
                            <span class="detail-value">{{ $product->added_by }}</span>
                        </p>
                        <p>
                            <span class="detail-name">Created</span>
                            <span class="detail-value">{{ $product->created_at->format('j F, Y') }}</span>
                        </p>
                        <p>
                            <span class="detail-name">Last update</span>
                            <span class="detail-value">{{ $product->updated_at->format('j F, Y') }}</span>
                        </p>

                    </div>
                    <div class="width pricing-table">
                        <p class="detail-title">Pricing</p>
                        <table>
                            <thead>
                                <tr>
                                    <th>Base price</th>
                                    <th>Updated</th>
                                    <th>Updated by</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td style="text-align: left">₱ {{ $product->base_price }}</td>
                                    <th style="text-align: left">{{ $product->updated_at->format('j F, Y') }}</th>
                                    <th style="text-align: left">{{ $product->added_by }}</th>
                                </tr>
                            </tbody>
                        </table>
        
                    </div>
                </div>


            </div>
            <div class="right-board">
                <div class="product-sales">
                    <div class="head">
                        <p>
                            <strong>Product sales</strong>
                            <span style="margin-left: 5px;" class="material-symbols-outlined">info</span>
                            <span style="margin-left: 15px; font-size: 13px;"> + 20% </span>
                        </p>
                        <p style="font-size: 11px; color: #666; ">
                           Last 7 days
                        </p>
                    </div>
                    <div class="body">
                        <p>
                            <span style="color:#f57c00 ">{{shortNumber($currentSale)}}</span>
                            <span class="title">Total sales</span>
                        </p>
                        <p>
                            <span style="color:#888">{{shortNumber($lastWeekSale)}}</span>
                            <span class="title">Previous period</span>
                        </p>
                    </div>
                    <div class="chart">
                        Chart
                    </div>
                    <div class="footer">
                        <p>Updated at 11:30 am</p>
                    </div>
                </div>
                <div class="product-sales">
                    <div class="head">
                        <p>
                            <strong>Product revenue</strong>
                            <span style="margin-left: 5px;" class="material-symbols-outlined">info</span>
                            <span style="margin-left: 15px; font-size: 13px;"> + 20% </span>
                        </p>
                        <p style="font-size: 11px; color: #666; ">
                           Last 7 days
                        </p>
                    </div>
                    <div class="body">
                        <p>
                            <span style="color:#f57c00 ">31</span>
                            <span class="title">Total revenue</span>
                        </p>
                        <p>
                            <span style="color:#888">15</span>
                            <span class="title">Previous period</span>
                        </p>
                    </div>
                    <div class="chart">
                        Chart
                    </div>
                    <div class="footer">
                        <p>Updated at 11:30 am</p>
                    </div>
                </div>
            </div>
        </div>



   </div>
@endsection



@push('scripts')
    <script src="{{ asset('js/global/copy-btn.js') }}"></script>
    <script src="{{ asset('js/global/short-num.js') }}"></script>

@endpush