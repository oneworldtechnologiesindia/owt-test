<div class="product-filter-container">
    <div class="card">
        <div class="card-body" style="min-height: 507px;">
            <h4 class="card-title mb-4">@lang('translation.Search For Products')</h4>

            @if (isset($allBrands) && !empty($allBrands) && count($allBrands) > 0)
                <div>
                    <div class="mb-3">
                        <label for="brand_id" class="form-label">@lang('translation.Brand')</label>
                        <select name="brand_id[]" id="brand_id"
                            class="form-control select2 select2-multiple"
                            placeholder="@lang('translation.Filter Product By Brand')" multiple>
                            @foreach ($allBrands as $brand)
                                <option value="{{ $brand->id }}">{{ $brand->brand_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            @endif
            @if (isset($allProductType) && !empty($allProductType) && count($allProductType) > 0)
                <div>
                    <div class="mb-3">
                        <label for="producttype_id" class="form-label">@lang('translation.Type')</label>
                        <select name="producttype_id[]" id="producttype_id"
                            class="form-control select2 select2-multiple"
                            placeholder="@lang('translation.Filter Product By Type')" multiple>
                            @foreach ($allProductType as $type)
                                <option value="{{ $type->id }}">{{ $type->type_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            @endif
            @if (isset($allProductCategory) && !empty($allProductCategory) && count($allProductCategory) > 0)
                <div>
                    <div class="mb-3">
                        <label for="productcategory_id" class="form-label">
                            @lang('translation.Category')</label>
                        <select name="productcategory_id[]" id="productcategory_id"
                            class="form-control select2 select2-multiple"
                            placeholder="@lang('translation.Filter Product By Category')" multiple>
                            @foreach ($allProductCategory as $category)
                                <option value="{{ $category->id }}">
                                    {{ $category->category_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            @endif
            <div>
                <div class="select-product-container mb-4">
                    <div class="mb-3">
                        <label for="product_id" class="form-label">@lang('translation.Product')</label>
                        <select name="product_id[]" id="product_id"
                            class="form-control select2 m-b-10"
                            placeholder="@lang('translation.Select Product')">
                            <option></option>
                        </select>
                        <span class="invalid-feedback" id="product_idError"
                            data-ajax-feedback="product_id" role="alert"></span>
                    </div>
                </div>
            </div>
            <div class="mb-3">
                <a href="javascript:void(0)"
                    class="btn btn-primary waves-effect waves-light reset-filter d-block">@lang('translation.Reset_filter')</a>
            </div>
        </div>
    </div>
</div>