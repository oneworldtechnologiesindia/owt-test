@extends('layouts.master')

@section('title')
     @lang('translation.Password Change')
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            <a href="{{ route('home') }}">@lang('translation.Dashboard')</a>
        @endslot
        @slot('title')
            @lang('translation.Password Change')
        @endslot
    @endcomponent

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">@lang('translation.Password Change')</h4>
                    <form id="password_form" method="post" class="form-horizontal" action="{{ route('password.update') }}"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="current_password" class="control-label">@lang('translation.Old Password')</label>
                                <input id="current_passwordp" type="password" class="form-control" name="current_password"
                                    placeholder="@lang('translation.Old Password')">
                                <span class="invalid-feedback" id="current_passwordpError"
                                    data-ajax-feedback="current_passwordp" role="alert"></span>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="password" class="control-label">@lang('translation.Password')</label>
                                <input id="passwordp" type="password" class="form-control" name="password"
                                    placeholder="@lang('translation.Password')">
                                <span class="invalid-feedback" id="passwordpError" data-ajax-feedback="passwordp"
                                    role="alert"></span>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="password_confirmation" class="control-label">
                                    @lang('translation.Password Confirmation')</label>
                                <input id="password_confirmationp" type="password" class="form-control"
                                    name="password_confirmation" placeholder="@lang('translation.Password Confirmation')">
                                <span class="invalid-feedback" id="password_confirmationpError"
                                    data-ajax-feedback="password_confirmationp" role="alert"></span>
                            </div>
                            <div class="col-md-12 form-footer text-end mt-3">
                                <a href="{{ route('home') }}" class="btn btn-default waves-effect">@lang('translation.Cancel')</a>
                                <button type="submit" class="btn btn-success waves-effect waves-light">
                                    @lang('translation.Update')
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script type="text/javascript">
        var updateProfileUrl = "{{ route('passwords.update') }}";
    </script>
@endsection

@section('script-bottom')
    <script src="{{ addPageJsLink('password.js') }}"></script>
@endsection
