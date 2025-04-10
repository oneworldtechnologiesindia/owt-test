@extends('layouts.master')

@section('title')
     @lang('translation.Document')
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            <a href="{{ route('home') }}">@lang('translation.Dashboard')</a>
        @endslot
        @slot('title')
            @lang('translation.Document')
        @endslot
    @endcomponent

    <div class="checkout-tabs">
        <div class="row">
            <div class="col-lg-2">
                <div class="nav flex-column nav-pills" id="document-tab" role="tablist" aria-orientation="vertical">
                    <a class="nav-link active" id="document-hq-terms-condition-tab" data-bs-toggle="pill" href="#document-hq-terms-condition"
                        role="tab" aria-controls="document-hq-terms-condition" aria-selected="true">
                        {{-- bx bx-question-mark --}}
                        <i class="bx bx-fingerprint d-block check-nav-icon mt-4 mb-2"></i>
                        <p class="fw-bold mb-4">@lang('translation.HQ - Terms & conditions')</p>
                    </a>
                    <a class="nav-link" id="document-hq-privacy-policy-tab" data-bs-toggle="pill" href="#document-hq-privacy-policy"
                        role="tab" aria-controls="document-hq-privacy-policy" aria-selected="false">
                        <i class="bx bx-check-shield d-block check-nav-icon mt-4 mb-2"></i>
                        <p class="fw-bold mb-4">@lang('translation.HQ - Privacy Policy')</p>
                    </a>
                    <a class="nav-link" id="document-dealer-terms-condition-tab" data-bs-toggle="pill" href="#document-dealer-terms-condition"
                        role="tab" aria-controls="document-dealer-terms-condition" aria-selected="false">
                        <i class="bx bx-fingerprint d-block check-nav-icon mt-4 mb-2"></i>
                        <p class="fw-bold mb-4">@lang('translation.Dealer - Terms & conditions')</p>
                    </a>
                    <a class="nav-link" id="document-withdrow-policy-tab" data-bs-toggle="pill" href="#document-withdrow-policy"
                        role="tab" aria-controls="document-withdrow-policy" aria-selected="false">
                        <i class="bx bx-receipt d-block check-nav-icon mt-4 mb-2"></i>
                        <p class="fw-bold mb-4">@lang('translation.Dealer - Withdraw Policy')</p>
                    </a>
                </div>
            </div>
            <div class="col-lg-10">
                <div class="card">
                    <div class="card-body">
                        <div class="tab-content" id="document-tabContent">
                            <div class="tab-pane fade show active" id="document-hq-terms-condition" role="tabpanel"
                                aria-labelledby="document-hq-terms-condition-tab">
                                <h4 class="card-title mb-4">@lang('translation.HQ - Terms & conditions')</h4>
                                <form action="#" type="post" name="hq-terms-condition"
                                    class="form-horizontal hq-terms-condition" id="hq-terms-condition">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="title" class="form-label">@lang('translation.Title')</label>
                                        <input type="text" class="form-control title" name="title">
                                        <span class="invalid-feedback titleError" data-ajax-feedback="title"
                                            role="alert"></span>
                                    </div>
                                    <div class="mb-3">
                                        <label for="description" class="form-label">@lang('translation.Description')</label>
                                        <textarea name="description" id="hqtc-description" class="form-control description editor-tinymce" cols="30" rows="10"></textarea>
                                        <span class="invalid-feedback descriptionError" data-ajax-feedback="description"
                                            role="alert"></span>
                                    </div>
                                    <div class="mb-3">
                                        <label for="German Title" class="form-label">@lang('translation.German Title')</label>
                                        <input type="text" class="form-control german_title" name="german_title">
                                        <span class="invalid-feedback german_titleError" data-ajax-feedback="german_title"
                                            role="alert"></span>
                                    </div>
                                    <div class="mb-3">
                                        <label for="German Description" class="form-label">@lang('translation.German Description')</label>
                                        <textarea name="german_description" id="hqtc-description-german" class="form-control german_description editor-tinymce" cols="30" rows="10"></textarea>
                                        <span class="invalid-feedback german_descriptionError" data-ajax-feedback="german_description"
                                            role="alert"></span>
                                    </div>
                                    <div class="col-md-12 form-footer text-end mt-3">
                                        <button type="submit" class="btn btn-success waves-effect waves-light">
                                            @lang('translation.Update')
                                        </button>
                                    </div>
                                </form>
                            </div>
                            <div class="tab-pane fade" id="document-hq-privacy-policy" role="tabpanel"
                                aria-labelledby="document-hq-privacy-policy-tab">
                                <h4 class="card-title mb-4">@lang('translation.HQ - Privacy Policy')</h4>
                                <form action="#" type="post" name="hq-privacy-policy"
                                    class="form-horizontal hq-privacy-policy" id="hq-privacy-policy">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="title" class="form-label">@lang('translation.Title')</label>
                                        <input type="text" class="form-control title" name="title">
                                        <span class="invalid-feedback titleError" data-ajax-feedback="title"
                                            role="alert"></span>
                                    </div>
                                    <div class="mb-3">
                                        <label for="description" class="form-label">@lang('translation.Description')</label>
                                        <textarea name="description" id="hqpp-description" class="form-control description editor-tinymce" cols="30" rows="10"></textarea>
                                        <span class="invalid-feedback descriptionError" data-ajax-feedback="description"
                                            role="alert"></span>
                                    </div>
                                    <div class="mb-3">
                                        <label for="German Title" class="form-label">@lang('translation.German Title')</label>
                                        <input type="text" class="form-control german_title" name="german_title">
                                        <span class="invalid-feedback german_titleError" data-ajax-feedback="german_title"
                                            role="alert"></span>
                                    </div>
                                    <div class="mb-3">
                                        <label for="German Description" class="form-label">@lang('translation.German Description')</label>
                                        <textarea name="german_description" id="hqpp-description-german" class="form-control german_description editor-tinymce" cols="30" rows="10"></textarea>
                                        <span class="invalid-feedback german_descriptionError" data-ajax-feedback="german_description"
                                            role="alert"></span>
                                    </div>
                                    <div class="col-md-12 form-footer text-end mt-3">
                                        <button type="submit" class="btn btn-success waves-effect waves-light">
                                            @lang('translation.Update')
                                        </button>
                                    </div>
                                </form>
                            </div>
                            <div class="tab-pane fade" id="document-dealer-terms-condition" role="tabpanel"
                                aria-labelledby="document-dealer-terms-condition-tab">
                                <h4 class="card-title mb-4">@lang('translation.Dealer - Terms & conditions')</h4>
                                <form action="#" type="post" name="dealer-terms-condition"
                                    class="form-horizontal dealer-terms-condition" id="dealer-terms-condition">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="title" class="form-label">@lang('translation.Title')</label>
                                        <input type="text" class="form-control title" name="title">
                                        <span class="invalid-feedback titleError" data-ajax-feedback="title"
                                            role="alert"></span>
                                    </div>
                                    <div class="mb-3">
                                        <label for="description" class="form-label">@lang('translation.Description')</label>
                                        <textarea name="description" id="dtc-description" class="form-control description editor-tinymce" cols="30" rows="10"></textarea>
                                        <span class="invalid-feedback descriptionError" data-ajax-feedback="description"
                                            role="alert"></span>
                                    </div>
                                    <div class="mb-3">
                                        <label for="German Title" class="form-label">@lang('translation.German Title')</label>
                                        <input type="text" class="form-control german_title" name="german_title">
                                        <span class="invalid-feedback german_titleError" data-ajax-feedback="german_title"
                                            role="alert"></span>
                                    </div>
                                    <div class="mb-3">
                                        <label for="German Description" class="form-label">@lang('translation.German Description')</label>
                                        <textarea name="german_description" id="dtc-description-german" class="form-control german_description editor-tinymce" cols="30" rows="10"></textarea>
                                        <span class="invalid-feedback german_descriptionError" data-ajax-feedback="german_description"
                                            role="alert"></span>
                                    </div>
                                    <div class="col-md-12 form-footer text-end mt-3">
                                        <button type="submit" class="btn btn-success waves-effect waves-light">
                                            @lang('translation.Update')
                                        </button>
                                    </div>
                                </form>
                            </div>
                            <div class="tab-pane fade" id="document-withdrow-policy" role="tabpanel"
                                aria-labelledby="document-withdrow-policy-tab">
                                <h4 class="card-title mb-4">@lang('translation.Dealer - Withdraw Policy')</h4>
                                <form action="#" type="post" name="dealer-withdraw-policy"
                                    class="form-horizontal dealer-withdraw-policy" id="dealer-withdraw-policy">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="title" class="form-label">@lang('translation.Title')</label>
                                        <input type="text" class="form-control title" name="title">
                                        <span class="invalid-feedback titleError" data-ajax-feedback="title"
                                            role="alert"></span>
                                    </div>
                                    <div class="mb-3">
                                        <label for="description" class="form-label">@lang('translation.Description')</label>
                                        <textarea name="description" id="dwp-description" class="form-control description editor-tinymce" cols="30" rows="10"></textarea>
                                        <span class="invalid-feedback descriptionError" data-ajax-feedback="description"
                                            role="alert"></span>
                                    </div>
                                    <div class="mb-3">
                                        <label for="German Title" class="form-label">@lang('translation.German Title')</label>
                                        <input type="text" class="form-control german_title" name="german_title">
                                        <span class="invalid-feedback german_titleError" data-ajax-feedback="german_title"
                                            role="alert"></span>
                                    </div>
                                    <div class="mb-3">
                                        <label for="German Description" class="form-label">@lang('translation.German Description')</label>
                                        <textarea name="german_description" id="dwp-description-german" class="form-control german_description editor-tinymce" cols="30" rows="10"></textarea>
                                        <span class="invalid-feedback german_descriptionError" data-ajax-feedback="german_description"
                                            role="alert"></span>
                                    </div>
                                    <div class="col-md-12 form-footer text-end mt-3">
                                        <button type="submit" class="btn btn-success waves-effect waves-light">
                                            @lang('translation.Update')
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- end row -->
@endsection

@section('script')
    <!-- Tinymce js -->
    <script src="{{ URL::asset('/assets/libs/tinymce/tinymce.min.js') }}"></script>
    <script>
        var addUpdateUrl = "{{ route('document.addupdate') }}";
        var getUrl = "{{ route('document.get') }}";
    </script>
@endsection

@section('script-bottom')
    <script src="{{ addPageJsLink('document.js') }}"></script>
@endsection
