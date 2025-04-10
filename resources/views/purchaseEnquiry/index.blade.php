@extends('layouts.master')

@section('title')
    @lang('translation.Purchase Enquiry')
@endsection

@section('css')
    <!-- Datatable Css -->
    <link href="{{ URL::asset('/assets/libs/datatables/datatables.min.css') }}" id="bootstrap-style" rel="stylesheet"
        type="text/css" />
    <!-- Sweet Alert-->
    <link href="{{ URL::asset('/assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            <a href="{{ route('customer.home') }}">@lang('translation.Dashboard')</a>
        @endslot
        @slot('title')
            @lang('translation.Purchase Enquiry')
        @endslot
    @endcomponent
    @if (isset($enquiryblock) && !empty($enquiryblock) && $enquiryblock == 'true' && isset($blockupto) && !empty($blockupto))
        <div class="notice-empty alert alert-danger d-block" role="alert" style="display: none;">
            <i class="mdi mdi-block-helper me-2"></i>
            @lang('translation.You can not create purchase enquiry since') {{ date('M d, Y H:i', strtotime($blockupto)) }}.
        </div>
    @endif
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="btn-group-card-header d-flex align-items-center justify-content-between mb-4">
                        <h4 class="card-title">@lang('translation.Purchase Enquiry')</h4>
                        @if (isset($enquiryblock) && !empty($enquiryblock) && $enquiryblock == 'false')
                            <a href="{{ route('customer.enquiry.create') }}"
                                class="btn btn-primary waves-effect btn-label waves-light add-new"><i
                                    class="bx bx-plus label-icon"></i> @lang('translation.Add_New')</a>
                        @endif
                    </div>
                    <div class="table-responsive" data-simplebar>
                        <table id="listTable" class="table align-middle table-hover table-nowrap w-100 dataTable">
                            <thead class="table-light">
                                <tr>
                                    <th></th>
                                    <th>@lang('translation.Products')</th>
                                    <th>@lang('translation.Offers')</th>
                                    <th>@lang('translation.Status')</th>
                                    <th>@lang('translation.Date')</th>
                                    <th>@lang('translation.Actions')</th>
                                    <th>@lang('translation.Updated At')</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div> <!-- end col -->
    </div>

    <!-- Page Models -->
    <div id="offer-view-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myLargeModalLabel"><span class="modal-lable-class">
                            @lang('translation.Offer Details')</h5>
                    <div style="margin-left: 50px;">
                        <b>
                            @lang('translation.Dealer Service-Level') : <span class="num_rating"></span> / 5;
                        </b>
                        <br />
                        <div class="rating-star">
                            <input type="hidden" class="rating" data-filled="mdi mdi-star text-primary"
                                data-empty="mdi mdi-star-outline text-muted" data-fractions="2" />
                            <b> @lang('translation.Total') <span class="total_feedback"></span> @lang('translation.Feedbacks') </b>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive" data-simplebar>
                        <table id="offerViewTableIndex" class="table align-middle table-hover table-nowrap w-100 dataTable">
                            <thead class="table-light">
                                <tr>
                                    <th></th>
                                    <th>@lang('translation.Product')</th>
                                    <th>@lang('translation.Qty')</th>
                                    <th>@lang('translation.Brand')</th>
                                    <th>@lang('translation.Amount')</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                            <tfoot>
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th colspan="2">@lang('translation.Total Amount'):<span
                                            class="text-muted d-block">@lang('translation.inkl_Mwst & Lieferung')</span></th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <div id="delivery-date-time">
                        <span><b>@lang('translation.Delivery_time'):</b></span>
                        <span class="delivery-date-time"></span>
                    </div>
                </div>
                <div class="col-md-12 modal-footer">
                    <button type="button" class="btn btn-default waves-effect" data-bs-dismiss="modal"
                        aria-label="Close">@lang('translation.Close')</button>
                    <button type="button" class="btn btn-success offer_status waves-effect" data-code="2">
                        @lang('translation.Accept offer')</button>
                </div>
            </div>
        </div>
    </div>

    @if (isset($payment_method) && !empty($payment_method))
        <div id="confirm-offer-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="myLargeModalLabel"><span class="modal-lable-class">
                                @lang('translation.Accept offer')</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="#" method="post" class="form accept-offer" id="accept-offer">
                        <div class="modal-body">
                            @csrf
                            <input type="hidden" name="offerid" value="0" class="offerid" id="offerid">
                            <input type="hidden" name="offerstatus" value="0" class="offerstatus" id="offerstatus">
                            <div class="mb-4">
                                <label for="payment_method" class="form-label">@lang('translation.Payment Method')</label>
                                <select name="payment_method" id="payment_method" class="form-select">
                                    <option value="">@lang('translation.Choose payment method')</option>
                                    @foreach ($payment_method as $key => $value)
                                        <option value="{{ $key }}">{{ $value }}</option>
                                    @endforeach
                                </select>
                                <span class="invalid-feedback" id="payment_methodError"
                                    data-ajax-feedback="payment_method" role="alert"></span>
                            </div>
                            <div class="mb-2">
                                <div class="form-check form-switch form-switch-md">
                                    <input class="form-check-input" type="checkbox" id="dsgvo_terms" name="dsgvo_terms">
                                    <label class="form-check-label" for="dsgvo_terms">@lang('translation.I_Agree')<a
                                            href="{{ route('customer.enquiry.getEnquiryDocuments') }}" target="_blank"
                                            class="enquiry-documents" data-type="terms">
                                            @lang('translation.Terms & Coditions')</a>.</label>
                                    <span class="invalid-feedback" id="dsgvo_termsError" data-ajax-feedback="dsgvo_terms"
                                        role="alert"></span>
                                </div>
                            </div>
                            <div class="mb-2">
                                <div class="form-check form-switch form-switch-md">
                                    <input class="form-check-input" type="checkbox" id="withdrawal_declaration"
                                        name="withdrawal_declaration">
                                    <label class="form-check-label" for="withdrawal_declaration">
                                        @lang('translation.I_Agree') <a href="{{ route('customer.enquiry.getEnquiryDocuments') }}"
                                            target="_blank" class="enquiry-documents" data-type="withdrawal">
                                            @lang('translation.Withdrawal Declaration')</a>.</label>
                                    <span class="invalid-feedback" id="withdrawal_declarationError"
                                        data-ajax-feedback="withdrawal_declaration" role="alert"></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 modal-footer">
                            <button type="button" class="btn btn-default waves-effect" data-bs-dismiss="modal"
                                aria-label="Close">@lang('translation.Close')</button>
                            <button type="submit" class="btn btn-success waves-effect accept-offer">
                                @lang('translation.Confirm')</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
    {{-- test --}}
    <!-- resources/views/payment.blade.php -->



    <!-- Create a form to collect payment details -->
    {{-- <form id="payment-form">
        <div id="sepa-element">
            <!-- SEPA Direct Debit fields go here -->
        </div>

        <div id="card-element">
            <!-- Card fields go here -->
        </div>
         <label for="iban-element">
            IBAN
        </label>
        <div id="iban-element">
      <!-- A Stripe Element will be inserted here. -->
    </div>
        <div id="card-errors" class="help-block animation-slideUp" role="alert"></div>
        <button id="payment-submit" type="button">Pay</button>
    </form> --}}

    {{-- end test --}}
@endsection

@section('script')
    <!-- Include the Stripe.js script -->
    {{-- <script src="https://js.stripe.com/v3/"></script> --}}

    <!-- Datatable js -->
    <script src="{{ URL::asset('/assets/libs/datatables/datatables.min.js') }}"></script>
    <!-- Sweet Alerts js -->
    <script src="{{ URL::asset('/assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>

    <!-- Bootstrap rating js -->
    <script src="{{ URL::asset('/assets/libs/bootstrap-rating/bootstrap-rating.min.js') }}"></script>

    <script src="{{ URL::asset('/assets/js/pages/rating-init.js') }}"></script>

    <script>
        var apiUrl = "{{ route('customer.enquiry.list') }}";
        var deleteUrl = "{{ route('customer.enquiry.delete') }}";
        var addUrl = "{{ route('customer.enquiry.addupdate') }}";
        var detailUrl = "{{ route('customer.getDetailEnquiry') }}";
        var getOfferListUrl = "{{ route('customer.getOfferList') }}";
        var getEnquiryProductListUrl = "{{ route('customer.getEnquiryProductList') }}";
        var updateOfferStatusUrl = "{{ route('customer.updateOfferStatus') }}";
        var getofferDetailUrl = "{{ route('customer.getofferDetail') }}";
        var getEnquiryDocumentsUrl = "{{ route('customer.enquiry.getEnquiryDocuments') }}";
        var getDealerRatingUrl = "{{ route('customer.enquiry.getDealerRatingUrl') }}";
        var getDealerInfoUrl = "{{ route('customer.enquiry.getDealerInfo') }}";
        var getCheckoutSessionUrl = "{{ route('customer.getCheckoutSession') }}";
    </script>
@endsection

@section('script-bottom')
    <script src="{{ addPageJsLink('purchase-enquiry.js') }}"></script>

    {{-- <script>
        // Initialize Stripe.js with your Publishable Key
        // var stripe = Stripe('{{config('services.stripe.public')}}');

        // Create SEPA Direct Debit Element
        // var sepaElement = stripe.elements().create('sepaDebit', {
        //     placeholder: 'Enter your bank account details',
        //     classes: {
        //         base: 'my-custom-class',
        //         focus: 'my-custom-class-focused',
        //         invalid: 'my-custom-class-invalid'
        //     },
        //     style: {
        //         base: {
        //             fontSize: '16px',
        //             color: '#333',
        //             padding: '10px'
        //         }
        //     },
        //     supportedCountries: ['AT', 'DE', 'FR', 'ES', 'IT'],
        //     mandateOptions: {
        //         mandateAcceptance: {
        //             text: 'By providing your bank account information, you authorize us to debit your account for the total amount.',
        //             notificationMethod: 'email'
        //         },
        //         mandateReference: '1234567890'
        //     }
        // });

        // Create Card Element
        // var cardElement = stripe.elements().create('card', {
        //     placeholder: 'Enter your card details',
        //     classes: {
        //         base: 'my-custom-class',
        //         focus: 'my-custom-class-focused',
        //         invalid: 'my-custom-class-invalid'
        //     },
        //     style: {
        //         base: {
        //             fontSize: '16px',
        //             color: '#333',
        //             padding: '10px'
        //         }
        //     },
        //     hidePostalCode: true,
        //     iconStyle: 'solid',
        //     value: {
        //         exp_month: '12',
        //         exp_year: '2023'
        //     },
        //     disabled: false,
        //     hideIcon: false,
        //     hideCVC: false,
        //     showIcon: 'always'
        // });

        // // Mount SEPA Direct Debit Element to the form
        // // sepaElement.mount('#sepa-element');

        // // Mount Card Element to the form
        // cardElement.mount('#card-element');

        // Handle form submission
        // document.getElementById('payment-submit').addEventListener('click', function() {
        //     // Create Payment Methods for SEPA Direct Debit and Card
        //     stripe.createPaymentMethod({
        //         type: 'sepa_debit',
        //         sepa_debit: sepaElement,
        //         billing_details: {
        //             // SEPA Direct Debit billing details go here
        //         }
        //     }).then(function(result) {
        //         // Handle SEPA Direct Debit Payment Method
        //         if (result.paymentMethod) {
        //             // Payment Method ID for SEPA Direct Debit
        //             var sepaPaymentMethodId = result.paymentMethod.id;
        //             // Process SEPA Direct Debit Payment Method and Card Payment Method as needed
        //             // For example, you can pass the Payment Method IDs to your server for further processing
        //         }
        //     });
        // });
        var stripe = Stripe('{{config('services.stripe.public')}}');
        var elements = stripe.elements();
        var style = {
            base: {
                color: '#32325d',
                fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
                fontSmoothing: 'antialiased',
                fontSize: '16px',
                '::placeholder': {
                    color: '#aab7c4'
                }
            },
            invalid: {
                color: '#fa755a',
                iconColor: '#fa755a'
            }
        };
        var card = elements.create('card',{hidePostalCode: true,style: style});
        card.mount('#card-element');
        card.addEventListener('change', function(event) {
            var displayError = document.getElementById('card-errors');
            if (event.error) {
                displayError.textContent = event.error.message;
            } else {
                displayError.textContent = '';
            }
        });

        // Custom styling can be passed to options when creating an Element.
            var style = {
            base: {
                color: '#32325d',
                fontSize: '16px',
                '::placeholder': {
                color: '#aab7c4'
                },
                ':-webkit-autofill': {
                color: '#32325d',
                },
            },
            invalid: {
                color: '#fa755a',
                iconColor: '#fa755a',
                ':-webkit-autofill': {
                color: '#fa755a',
                },
            },
            };

            const options = {
            style,
            supportedCountries: ['SEPA'],
            // Elements can use a placeholder as an example IBAN that reflects
            // the IBAN format of your customer's country. If you know your
            // customer's country, we recommend passing it to the Element as the
            // placeholderCountry.
            placeholderCountry: 'DE',
            };

            // Create an instance of the IBAN Element
            const iban = elements.create('iban', options);

            // Add an instance of the IBAN Element into the `iban-element` <div>
            iban.mount('#iban-element');

            iban.addEventListener('change', function(event) {
                var displayError = document.getElementById('card-errors');
                if (event.error) {
                    displayError.textContent = event.error.message;
                } else {
                    displayError.textContent = '';
                }
            });
            // var form = document.getElementById('payment-form');
            // form.addEventListener('submit', function(event) {
            //     debugger;
            //     event.preventDefault();
            //     // stripe.createToken(card).then(function(result) {
            //     //     if (result.error) {
            //     //         // Inform the user if there was an error.
            //     //         var errorElement = document.getElementById('card-errors');
            //     //         errorElement.textContent = result.error.message;
            //     //     } else {
            //     //         // Send the token to your server.
            //     //         stripeTokenHandler_for_createcustomer(result.token);
            //     //     }
            //     // });

            //     stripe.createToken(iban).then(function(result) {
            //         debugger;
            //         if (result.error) {
            //             // Inform the user if there was an error.
            //             var errorElement = document.getElementById('card-errors');
            //             errorElement.textContent = result.error.message;
            //         } else {
            //             // Send the token to your server.
            //             stripeTokenHandler_for_createcustomer(result.token);
            //         }
            //     });
            // });
             document.getElementById('payment-submit').addEventListener('click', function() {
                 debugger;
                    event.preventDefault();
                    // stripe.createToken(card).then(function(result) {
                    //     if (result.error) {
                    //         // Inform the user if there was an error.
                    //         var errorElement = document.getElementById('card-errors');
                    //         errorElement.textContent = result.error.message;
                    //     } else {
                    //         // Send the token to your server.
                    //         stripeTokenHandler_for_createcustomer(result.token);
                    //     }
                    // });
                    stripe.createPaymentMethod('card', card)
                    .then(function(result) {
                    if (result.error) {
                        // Handle errors from creating the payment method
                        console.error(result.error);
                    } else {
                        // Payment method created successfully
                        // Send the payment method ID to your server for further processing
                        var paymentMethodId = result.paymentMethod.id;
                        // submitPaymentMethod(paymentMethodId);
                    }
                    });

                // Create SEPA Direct Debit PaymentMethod
                    // Create SEPA Direct Debit PaymentMethod with owner information
                    // console.log(iban.value);
                    // stripe.createPaymentMethod({
                    // type: 'sepa_debit',
                    // sepa_debit: {
                    //     iban: "AT61 1904 3002 3457 3201",
                    // },
                    // billing_details: {
                    //     name: "name",
                    //     email:"radheshyam.amcodr@mailinator.com"
                    //     // Add any other required billing details as needed
                    // }
                    // }).then(function(result) {
                    // // Handle PaymentMethod creation success
                    // var paymentMethodId = result.paymentMethod.id;
                    // // You can use the paymentMethodId to save the PaymentMethod to your backend for future use
                    // }).catch(function(error) {
                    // // Handle PaymentMethod creation error
                    // console.error(error);
                    // });
            });

        function stripeTokenHandler_for_createcustomer(token) {
            console.log(token);

            // var action='<?php // echo $action; ?>';
            // console.log(action);
            // $.ajax({
            //    url:'',
            //    type: 'POST',
            //    data:{'token':token.id},
            //    success: function (response) {
            //      var obj=$.parseJSON(response);
            //      if(obj.status=="success")
            //      {
            //        $('.customer_id').val(obj.customer_id);
            //        $('#stripe_payment_modal_for_createcustomer').modal('hide');
            //        if(action=="buy_skiptrace")
            //        {
            //           $('.confirm_for_submit').val(1);
            //           $('form#lead-file-form').submit();
            //        }
            //        else if(action=="search_data")
            //        {
            //             $('.buy_now').trigger('click');
            //        }
            //      }
            //      else
            //      {
            //        toastr.error(obj.message,'Error');

            //      }
            //    }
            // });
            // Submit the form
            // form.submit();
        }
    </script> --}}
@endsection
