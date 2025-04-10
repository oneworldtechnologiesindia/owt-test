<!-- ========== Left Sidebar Start ========== -->
<div class="vertical-menu">

    <div data-simplebar class="h-100">

        <!--- Sidemenu -->
        <div id="sidebar-menu"
            class="sidebar-{{ $loginUser->role_type == 1 ? 'admin' : ($loginUser->role_type == 2 ? 'dealer' : 'customer') }}">
            <!-- Left Menu Start -->
            <ul class="metismenu list-unstyled" id="side-menu-test">


                @if ($loginUser->role_type == 1)
                    <li class="{{ request()->routeIs('home') ? 'mm-active' : '' }}">
                        <a href="{{ route('home') }}" class="waves-effect">
                            <i class="bx bx-home-circle"></i>
                            <span>@lang('translation.Dashboard')</span>
                        </a>
                    </li>

                    <li class="{{ request()->routeIs('dealers.edit') ? 'mm-active' : '' }}">
                        @php
                            $isActiveDealer =
                                request()->routeIs('dealers.create') ||
                                request()->routeIs('dealers.edit') ||
                                request()->routeIs('dealers');
                        @endphp
                        <a href="javascript: void(0);" class="waves-effect has-submenu" data-bs-toggle="collapse"
                            data-bs-target="#dealer-submenu">
                            <i class="bx bx-user-pin"></i>
                            <span>@lang('translation.Dealers')</span>
                            <span class="float-end icon-chevron">
                                <i class="bx {{ $isActiveDealer ? 'bx-chevron-down' : 'bx-chevron-right' }}"></i>
                            </span>
                        </a>

                        <ul class="sub-menu collapse {{ $isActiveDealer ? 'show' : '' }}"
                            aria-expanded="{{ $isActiveDealer ? 'true' : 'false' }}" id="dealer-submenu">
                            <li class="{{ request()->routeIs('dealers.edit') ? 'mm-active' : '' }}">
                                <a href="{{ route('dealers') }}">@lang('translation.All_Dealers')</a>
                            </li>
                            <li>
                                <a href="{{ route('dealers.create') }}">@lang('translation.Add_New')</a>
                            </li>
                        </ul>
                    </li>

                    <li class="{{ request()->routeIs('customers') ? 'mm-active' : '' }}">
                        <a href="{{ route('customers') }}" class="waves-effect">
                            <i class="bx bx-user"></i>
                            <span>@lang('translation.Customers')</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('emaillog') }}" class="waves-effect">
                            <i class="bx bxs-inbox"></i>
                            <span class="hide-menu">@lang('translation.Inbox')</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('plan_type') }}" class="waves-effect">
                            <i class="bx bx-package"></i>
                            <span class="hide-menu">@lang('translation.Plan Types')</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('subscription-log') }}" class="waves-effect">
                            <i class="bx bxs-inbox"></i>
                            <span class="hide-menu">@lang('translation.Subscription Invoices')</span>
                        </a>
                    </li>
                    <li
                        class="{{ request()->routeIs('dealerEnquiryView') || request()->routeIs('customerEnquiryView') ? 'mm-active' : '' }}">
                        <a href="{{ route('dealer-enquiry') }}" class="waves-effect">
                            <i class="bx bx-detail"></i>
                            <span
                                class="badge rounded-pill text-bg-success float-end purchase-enquiry-badge">@lang('translation.New')</span>
                            <span>@lang('translation.Purchase_Enquiries')</span>
                        </a>
                    </li>

                    <li class="{{ request()->routeIs('appointment') ? 'mm-active' : '' }}">
                        <a href="{{ route('appointment') }}" class="waves-effect">
                            <i class="bx bx-calendar"></i>
                            <span
                                class="badge rounded-pill text-bg-success float-end appointment-badge">@lang('translation.New')</span>
                            <span>@lang('translation.Appointments')</span>
                        </a>
                    </li>

                    <li class="{{ request()->routeIs('sales') ? 'mm-active' : '' }}">
                        <a href="{{ route('sales') }}" class="waves-effect">
                            <i class="bx bx-chart"></i>
                            <span
                                class="badge rounded-pill text-bg-success float-end order-badge">@lang('translation.New')</span>
                            <span>@lang('translation.Sales')</span>
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('purcahse-payment-log') ? 'mm-active' : '' }}">
                        <a href="{{ route('purcahse-payment-log') }}" class="waves-effect">
                            <i class="bx bx-detail"></i>
                            <span class="hide-menu">@lang('translation.Order Payments')</span>
                        </a>
                    </li>

                    <li class="{{ request()->routeIs('brand') ? 'mm-active' : '' }}">
                        <a href="{{ route('brand') }}" class="waves-effect">
                            <i class="bx bx-extension"></i>
                            <span>@lang('translation.Brands')</span>
                        </a>
                    </li>

                    <li
                        class="{{ request()->routeIs('product.edit') || request()->routeIs('product.view') ? 'mm-active' : '' }}">
                        @php
                            $isActiveProduct =
                                request()->routeIs('product') ||
                                request()->routeIs('product.create') ||
                                request()->routeIs('product.edit') ||
                                request()->routeIs('product.view') ||
                                request()->routeIs('product-type') ||
                                request()->routeIs('product-category') ||
                                request()->routeIs('product-connection') ||
                                request()->routeIs('product-execution');
                        @endphp
                        <a href="javascript: void(0);" class="waves-effect has-submenu" data-bs-toggle="collapse"
                            data-bs-target="#product-submenu">
                            <i class="bx bx-shopping-bag"></i>
                            <span>@lang('translation.Products')</span>
                            <span class="float-end icon-chevron">
                                <i class="bx {{ $isActiveProduct ? 'bx-chevron-down' : 'bx-chevron-right' }}"></i>
                            </span>
                        </a>
                        <ul class="sub-menu collapse {{ $isActiveProduct ? 'show' : '' }}"
                            aria-expanded="{{ $isActiveProduct ? 'true' : 'false' }}" id="product-submenu">
                            <li
                                class="{{ request()->routeIs('product.edit') || request()->routeIs('product.view') ? 'mm-active' : '' }}">
                                <a href="{{ route('product') }}">@lang('translation.All_Products')</a>
                            </li>
                            <li>
                                <a href="{{ route('product.create') }}">@lang('translation.Add_New')</a>
                            </li>
                            <li>
                                <a href="{{ route('product-type') }}">@lang('translation.Types')</a>
                            </li>
                            <li>
                                <a href="{{ route('product-category') }}">@lang('translation.Categories')</a>
                            </li>
                            <li>
                                <a href="{{ route('product-connection') }}">@lang('translation.Connections')</a>
                            </li>
                            <li>
                                <a href="{{ route('product-execution') }}">@lang('translation.Execution')</a>
                            </li>
                        </ul>
                    </li>

                    <li class="{{ request()->routeIs('document') ? 'mm-active' : '' }}">
                        <a href="{{ route('document') }}" class="waves-effect">
                            <i class="bx bx-notepad"></i>
                            <span>@lang('translation.Documents')</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('profile') }}" class="waves-effect">
                            <i class="bx bx-user"></i>
                            <span class="hide-menu">@lang('translation.Profile')</span>
                        </a>
                    </li>

                    <li class="{{ request()->routeIs('ad') || request()->routeIs('ad.preview') ? 'mm-active' : '' }}">
                        <a href="{{ route('ad') }}" class="waves-effect">
                            <i class="mdi mdi-google-ads"></i>
                            <span>@lang('translation.ads')</span>
                        </a>
                    </li>
                @endif
                @if ($loginUser->role_type == 3)
                    {{-- <li>
                        <a href="{{ route('product-list') }}" class="waves-effect">
                            <i class="bx bx-shopping-bag"></i>
                            <span class="hide-menu">{{ __('Search Product') }}</span>
                        </a>
                    </li> --}}
                    <li class="{{ request()->routeIs('customer.home') ? 'mm-active' : '' }}">
                        <a href="{{ route('customer.home') }}" class="waves-effect">
                            <i class="bx bx-home-circle"></i>
                            <span>@lang('translation.Dashboard')</span>
                        </a>
                    </li>
                    <li class="{{ request()->get('search') ? 'mm-active' : '' }}">
                        <a href="{{ route('customer.enquiry.create', ['search' => 1]) }}" class="waves-effect">
                            <i class="bx bxs-search"></i>
                            <span>@lang('translation.Search')</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('customer.emaillog') }}" class="waves-effect">
                            <i class="bx bxs-inbox"></i>
                            <span class="hide-menu">@lang('translation.Inbox')</span>
                        </a>
                    </li>
                    <li
                        class="{{ request()->routeIs('appointment.create') || request()->routeIs('enquiry.create') || request()->routeIs('customerEnquiryView') ? 'mm-active' : '' }}">
                        @php
                            $isActiveCustomerActivities =
                                request()->routeIs('customer.enquiry') ||
                                request()->routeIs('customer.enquiry.create') ||
                                request()->routeIs('customer.customerEnquiryView') ||
                                request()->routeIs('customer.appointment') ||
                                request()->routeIs('customer.appointment.create') ||
                                request()->routeIs('customer.purchases');
                        @endphp
                        <a href="javascript: void(0);" class="waves-effect has-submenu" data-bs-toggle="collapse"
                            data-bs-target="#customer-activities-submenu">
                            <i class="bx bx-store-alt"></i>
                            <span>@lang('translation.My_Activities')</span>
                            <span class="float-end icon-chevron">
                                <i
                                    class="bx {{ $isActiveCustomerActivities ? 'bx-chevron-down' : 'bx-chevron-right' }}"></i>
                            </span>
                        </a>
                        <ul class="sub-menu collapse {{ $isActiveCustomerActivities ? 'show' : '' }}"
                            aria-expanded="{{ $isActiveCustomerActivities ? 'true' : 'false' }}"
                            id="customer-activities-submenu">
                            <li
                                class="{{ request()->routeIs('customer.enquiry.create') || request()->routeIs('customer.customerEnquiryView') ? 'mm-active' : '' }}">
                                <a href="{{ route('customer.enquiry') }}">
                                    <span
                                        class="badge rounded-pill text-bg-success float-end purchase-enquiry-badge">@lang('translation.New')</span>
                                    @lang('translation.My_Purchase_Enquiries')
                                </a>
                            </li>
                            <li class="{{ request()->routeIs('customer.appointment.create') ? 'mm-active' : '' }}">
                                <a href="{{ route('customer.appointment') }}">
                                    <span
                                        class="badge rounded-pill text-bg-success float-end appointment-badge">@lang('translation.New')</span>
                                    @lang('translation.My_Appointments')
                                </a>
                            </li>
                            {{-- <li>
                                <a href="{{ route('calendar') }}">{{ __('My Calendar') }}</a>
                            </li> --}}
                            <li>
                                <a href="{{ route('customer.purchases') }}">
                                    <span
                                        class="badge rounded-pill text-bg-success float-end order-badge">@lang('translation.New')</span>
                                    @lang('translation.My_Purchases')
                                </a>
                            </li>
                        </ul>
                    </li>
                    {{-- <li
                        class="{{ request()->routeIs('enquiry.create') || request()->routeIs('customerEnquiryView') ? 'mm-active' : '' }}">
                        <a href="{{ route('enquiry') }}" class="waves-effect">
                            <i class="bx bxs-detail"></i>
                            <span class="hide-menu">{{ __('Purchase Enquires') }}</span>
                        </a>
                    </li> --}}
                @endif

                @if ($loginUser->role_type == 2)
                    <li class="{{ request()->routeIs('dealer.home') ? 'mm-active' : '' }}">
                        <a href="{{ route('dealer.home') }}" class="waves-effect">
                            <i class="bx bx-home-circle"></i>
                            <span>@lang('translation.Dashboard')</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('dealer.customer-analysis') }}" class="waves-effect">
                            <i class='bx bx-bar-chart-square'></i>
                            <span class="hide-menu">@lang('translation.Customer_Analysis')</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('dealer.dealer-brand') }}" class="waves-effect">
                            <i class="bx bx-extension"></i>
                            <span class="hide-menu">@lang('translation.My_Brands')</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('dealer.dealer-product') }}" class="waves-effect">
                            <i class="bx bx-shopping-bag"></i>
                            <span class="hide-menu">@lang('translation.My_Products')</span>
                        </a>
                    </li>
                    <li>
                        @php
                            $isActiveDealerInbox = request()->routeIs('dealer.emaillog');
                        @endphp
                        <a href="javascript: void(0);" class="waves-effect has-submenu" data-bs-toggle="collapse"
                            data-bs-target="#dealer-inbox-submenu">
                            <i class="bx bxs-inbox"></i>
                            <span>@lang('translation.Inbox')</span>
                            <span class="float-end icon-chevron">
                                <i class="bx {{ $isActiveDealerInbox ? 'bx-chevron-down' : 'bx-chevron-right' }}"></i>
                            </span>
                        </a>
                        <ul class="sub-menu collapse {{ $isActiveDealerInbox ? 'show' : '' }}"
                            aria-expanded="{{ $isActiveDealerInbox ? 'true' : 'false' }}" id="dealer-inbox-submenu">
                            <li>
                                <a href="{{ route('dealer.emaillog') }}">@lang('translation.Messages')</a>
                            </li>
                            {{-- <li>
                                <a href="{{ route('dealer.sales') }}">@lang('translation.Invoices')</a>
                            </li> --}}
                        </ul>
                    </li>
                    <li>
                        <a href="{{ route('dealer.calendar') }}" class="waves-effect">
                            <i class="bx bx-calendar"></i>
                            <span
                                class="badge rounded-pill text-bg-success float-end calendar-badge">@lang('translation.New')</span>
                            <span class="hide-menu">@lang('translation.Calendar')</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('dealer.appointment') }}" class="waves-effect">
                            <i class="bx bx-notepad"></i>
                            <span
                                class="badge rounded-pill text-bg-success float-end appointment-badge">@lang('translation.New')</span>
                            <span class="hide-menu">@lang('translation.Appointments')</span>
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('dealer.dealerEnquiryView') ? 'mm-active' : '' }}">
                        <a href="{{ route('dealer.dealer-enquiry') }}" class="waves-effect">
                            <i class="bx bx-detail"></i>
                            <span
                                class="badge rounded-pill text-bg-success float-end purchase-enquiry-badge">@lang('translation.New')</span>
                            <span class="hide-menu">@lang('translation.Purchase_Enquiries')</span>
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('dealer.purcahse-payment-log') ? 'mm-active' : '' }}">
                        <a href="{{ route('dealer.purcahse-payment-log') }}" class="waves-effect">
                            <i class="bx bx-detail"></i>
                            <span class="hide-menu">@lang('translation.Order Payments')</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('dealer.sales') }}" class="waves-effect">
                            <i class="bx bx-chart"></i>
                            <span
                                class="badge rounded-pill text-bg-success float-end order-badge">@lang('translation.New')</span>
                            <span class="hide-menu">@lang('translation.Sales')</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('dealer.contacts') }}" class="waves-effect">
                            <i class="bx bx-id-card"></i>
                            <span class="hide-menu">@lang('translation.Contacts')</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('dealer.subscription-log') }}" class="waves-effect">
                            <i class="bx bxs-inbox"></i>
                            <span class="hide-menu">@lang('translation.Subscription Invoices')</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('dealer.profile') }}" class="waves-effect">
                            <i class="bx bx-user"></i>
                            <span class="hide-menu">@lang('translation.Profile')</span>
                        </a>
                    </li>
                @endif

                <li>
                    <a href="javascript:void(0)" class="waves-effect"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="bx bx-log-out"></i>
                        <span class="hide-menu">@lang('translation.Logout')</span>
                    </a>
                </li>
            </ul>
        </div>
        <!-- Sidebar -->
    </div>
</div>
<!-- Left Sidebar End -->
