@extends('layouts.main')


@push('styles')
    <link rel="stylesheet" href="{{ asset('css/views/customer.css') }}">
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
                <p class="heading">{{$product->name}}</p>

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



   </div>
@endsection



@push('scripts')

@endpush