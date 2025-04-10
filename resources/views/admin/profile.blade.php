@extends('layouts.master')

@section('title')
    @lang('translation.Profile')
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            <a href="{{ route('home') }}">@lang('translation.Dashboard')</a>
        @endslot
        @slot('title')
            @lang('translation.Profile')
        @endslot
    @endcomponent
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">@lang('translation.Personal_Details')</h4>
                    <form id="profile_form" method="post" class="form-horizontal" action="{{ route('updateProfile') }}"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="first_name" class="form-label">@lang('translation.First_Name')</label>
                            <input id="first_name" type="text" class="form-control first_name" name="first_name"
                                placeholder="@lang('translation.Enter_First_Name')">
                            <span class="invalid-feedback" id="first_nameError" data-ajax-feedback="first_name"
                                role="alert"></span>
                        </div>
                        <div class="mb-3">
                            <label for="last_name" class="form-label">@lang('translation.Last_Name')</label>
                            <input id="last_name" type="text" class="form-control last_name" name="last_name"
                                placeholder="@lang('translation.Enter_Last_Name')">
                            <span class="invalid-feedback" id="last_nameError" data-ajax-feedback="last_name"
                                role="alert"></span>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">@lang('translation.Email')</label>
                            <input id="email" type="text" class="form-control email" name="email"
                                placeholder="@lang('translation.Enter_Email')">
                            <span class="invalid-feedback" id="emailError" data-ajax-feedback="email" role="alert"></span>
                        </div>
                        <div class="col-md-12 form-footer text-end mt-3">
                            <a href="{{ route('home') }}" class="btn btn-default waves-effect">@lang('translation.Cancel')</a>
                            <button type="submit" class="btn btn-success waves-effect waves-light">
                                @lang('translation.Update')
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div> <!-- end col -->
    </div>
@endsection

@section('script')
    <script type="text/javascript">
        var updateProfileUrl = "{{ route('updateProfile') }}";
        var profileDetailUrl = "{{ route('profileDetail') }}";
    </script>
@endsection

@section('script-bottom')
    <script src="{{ addPageJsLink('profile_all.js') }}"></script>
@endsection
