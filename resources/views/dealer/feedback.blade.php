@extends('layouts.master')

@section('title')
     @lang('translation.Feedback')
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            <a href="{{ route('dealer.home') }}">@lang('translation.Dashboard')</a>
        @endslot
        @slot('title')
            @lang('translation.Feedback')
        @endslot
    @endcomponent

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="btn-group-card-header d-flex align-items-center justify-content-between mb-4">
                        <h4 class="card-title">@lang('translation.All_Feedbacks')</h4>
                    </div>
                    <div class="table-responsive" data-simplebar>
                        <table id="listTable" class="table align-middle table-hover table-nowrap w-100 dataTable">
                            <thead class="table-light">
                                <tr>
                                     <th>@lang('translation.Customer')</th>
                                    <th>@lang('translation.Feedback_Type')</th>
                                    <th>@lang('translation.Feedback')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($appointmentData as $alist)
                                    <tr>
                                        <td>{{ $alist['first_name'].' '.$alist['last_name'] }}</td>
                                        <td>Appointment</td>
                                        <td>{{ $alist['rating'].'/5'}}</td>
                                    </tr>
                                @endforeach

                                @foreach($orderData as $olist)
                                    <tr>
                                        <td>{{ $olist['first_name'].' '.$olist['last_name'] }}</td>
                                        <td>Sales</td>
                                        <td>{{ $olist['average'].'/5'}}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div> <!-- end col -->
    </div>
@endsection
