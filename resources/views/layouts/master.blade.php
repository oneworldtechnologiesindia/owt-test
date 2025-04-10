@php
    $loginUser = Auth::user();
@endphp
<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8" />
    <title> @yield('title') | {{ config('app.name') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="" name="description" />
    <meta content="" name="author" />
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ URL::asset('assets/images/favicon.ico') }}">
    @include('layouts.head-css')
</head>

@section('body')

    <body data-sidebar="light" data-layout-mode="light">
    @show
    <!-- Loader -->
    <div id="preloader">
        <div id="status">
            <div class="spinner-chase">
                <div class="chase-dot"></div>
                <div class="chase-dot"></div>
                <div class="chase-dot"></div>
                <div class="chase-dot"></div>
                <div class="chase-dot"></div>
                <div class="chase-dot"></div>
            </div>
        </div>
    </div>
    <div id="my_custom_loader">
        <div id="status">
            <div class="spinner-chase">
                <div class="chase-dot"></div>
                <div class="chase-dot"></div>
                <div class="chase-dot"></div>
                <div class="chase-dot"></div>
                <div class="chase-dot"></div>
                <div class="chase-dot"></div>
            </div>
        </div>
    </div>
    <!-- Begin page -->
    <div id="layout-wrapper">
        @include('layouts.topbar')
        @include('layouts.sidebar')
        <!-- ============================================================== -->
        <!-- Start right Content here -->
        <!-- ============================================================== -->
        <div class="main-content">
            <div class="page-content">
                <div class="container-fluid">
                    @yield('content')
                </div>
                <!-- container-fluid -->
            </div>
            <!-- End Page-content -->
            @include('layouts.footer')
        </div>
        <!-- end main content-->
    </div>
    <!-- END layout-wrapper -->

    <script type="text/javascript">
        var siteUrl = "{{ asset('/') }}";
        var baseUrl = "{{ asset('/') }}{{ Request::segment(1) }}/";
        var nameUrl = "{{ Request::segment(2) }}";
        var getNotificationUrl = "{{ route('notification.getNotification') }}";
        var _token = "{{ csrf_token() }}";
        var localLang = "{{ str_replace('_', '-', app()->getLocale()) }}";
        var addLang = "@lang('translation.Add')";
        var editLang = "@lang('translation.Edit')";
        var deleteLang = "@lang('translation.Delete')";
        var invoice = "@lang('translation.Invoice')";
        var something_went_wrong = "@lang('translation.Something went wrong')";
        var are_you_sure = "@lang('translation.Are you sure')";
        var you_wont_be_able_to_revert_this = "@lang("translation.You won't be able to revert this")";
        var yes_delete_it = "@lang('translation.Yes_delete_it')";
        var no_cancel = "@lang('translation.No_cancel')";
        var add_event = "@lang('translation.Add Event')";
        var edit_event = "@lang('translation.Edit Event')";
        var update = "@lang('translation.Update')";
        var save = "@lang('translation.Save')";
        var you_want_to_change_this = "@lang('translation.You want to change this')";
        var yes_change_it = "@lang('translation.Yes_change_it')";
        var active = "@lang('translation.Active')";
        var deactive = "@lang('translation.Deactive')";
        var brand = "@lang('translation.Brand')";
        var typep = "@lang('translation.Type')";
        var category = "@lang('translation.Category')";
        var product_type = "@lang('translation.Product Type')";
        var product_category = "@lang('translation.Product Category')";
        var product_name = "@lang('translation.Product Name')";
        var anschlusse = "@lang('translation.CONNECTIONS')";
        var ausfuhrung = "@lang('translation.EXECUTION')";
        var url = "@lang('translation.URL')";
        var retail = "@lang('translation.Retail')";
        var bemerkung = "@lang('translation.Remark')";
        var accepted = "@lang('translation.Accepted')";
        var yes_accept_it = "@lang('translation.Yes_accept_it')";
        var yes_close_it = "@lang('translation.Yes_close_it')";
        var view = "@lang('translation.View')";
        var you_want_to_cancel_appointment = "@lang('translation.You want to Cancel Appointment')";
        var yes_cancel_it = "@lang('translation.Yes_cancel_it')";
        var you_want_to_confirm_appointment = "@lang('translation.You want to Confirm Appointment')";
        var yes_confirm_it = "@lang('translation.Yes_Confirm_it')";
        var please_upload_a_valid_pdf_file = "@lang('translation.Please upload a valid pdf file')";
        var please_upload_valid = "@lang('translation.Please upload valid')";
        var file_size_cannot_exceed = "@lang('translation.File size cannot exceed')";
        var select_brand = "@lang('translation.Select Brand')";
        var you_want_to_complete_appointment = "@lang('translation.You want to Complete Appointment')";
        var yes_complete_it = "@lang('translation.Yes_Complete_it')";
        var purchase_enquiry = "@lang('translation.Purchase Enquiry')";
        var all_attributes = "@lang('translation.All Attributes')";
        var in_stock = "@lang('translation.In stock')";
        var is_used = "@lang('translation.Is used')";
        var ready_for_demo = "@lang('translation.Ready for demo')";
        var attributes = "@lang('translation.Attributes')";
        var select_connection = "@lang('translation.Select connection')";
        var select_execution = "@lang('translation.Select execution')";
        var select_attribute = "@lang('translation.Select Attribute')";
        var this_row_is_duplicate = "@lang('translation.This row is duplicate')";
        var appointment_already_taken_please_choose_another_time = "@lang('translation.Appointment already taken please choose another time')";
        var select_product_type = "@lang('translation.Select Product Type')";
        var select_product_category = "@lang('translation.Select Product Category')";
        var select_ausfuhrung = "@lang('translation.Select Execution')";
        var select_connections = "@lang('translation.Select Connections')";
        var you_wont_cancel_contract = "@lang('translation.You_wont_cancel_contract')";
        var no_i_don_t = "@lang('translation.No_I_don_t')";
        var withdraw_cancelation = "@lang('translation.Withdraw Cancelation')";
        var you_wont_withdraw_contract = "@lang('translation.You_wont_withdraw_contract')";
        var yes_withdraw_it = "@lang('translation.Yes_withdraw_it')";
        var cancel_contract = "@lang('translation.Cancel Contract')";
        var dealer = "@lang('translation.Dealer')";
        var amount = "@lang('translation.Amount')";
        var gultigkeit = "@lang('translation.Validity')";
        var view_offer = "@lang('translation.View offer')";
        var qty = "@lang('translation.Qty')";
        var product_connection = "@lang('translation.Product Connection')";
        var product_execution = "@lang('translation.Product Execution')";
        var product_attributes = "@lang('translation.Product Attributes')";
        var select_product = "@lang('translation.Select Product')";
        var are_you_sure_want_to_accepted_this_offers = "@lang('translation.Are you sure want to Accepted This Offers')";
        var are_you_sure_unsaved_content_will_be_dismissed = "@lang('translation.Are you sure? Unsaved content will be dismissed.')";
        var are_you_sure_want_to_rejected_this_offers = "@lang('translation.Are you sure want to Rejected This Offers')";
        var you_want_to_confirm_payment = "@lang('translation.You want to confirm payment')";
        var yes_ship_it = "@lang('translation.Yes_ship_it')";
        var are_you_sure_want_to_ship_this_order = "@lang('translation.Are you sure want to ship this order')";
        var are_you_sure_want_to_cancel_this_order = "@lang('translation.Are you sure want to cancel this order')";
        var No_new_notifications = "@lang('translation.No_new_notifications')";
        var currency_type = '{{ getDealerCurrencyType() }}';
    </script>

    <!-- JAVASCRIPT -->
    @include('layouts.vendor-scripts')

    <script>
        $(function() {
            $('.metismenu .has-submenu').on('click', function(e) {
                e.preventDefault(); // Prevent default action if necessary

                const $this = $(this);
                const $icon = $this.find('.icon-chevron i');
                const $submenu = $this.next('.sub-menu');

                if ($this.attr('aria-expanded') === 'true') {
                    $this.attr('aria-expanded', 'false');
                    $submenu.collapse('hide');
                    $icon.removeClass('bx-chevron-right').addClass('bx-chevron-down');
                } else {
                    $this.attr('aria-expanded', 'true');
                    $submenu.collapse('show');
                    $icon.removeClass('bx-chevron-down').addClass('bx-chevron-right');
                }

                const $otherSubmenu = $('.metismenu .sub-menu').not($submenu);
                $otherSubmenu.removeClass('show');
                $otherSubmenu.parent().find('.icon-chevron i').removeClass('bx-chevron-down').addClass(
                    'bx-chevron-right');
            });
        });
    </script>

</body>

</html>
