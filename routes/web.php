<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function () {
//     return redirect(route('login'));
// });
Route::get('/index', 'SiteController@index')->name('index');
Route::get('/home', 'SiteController@index')->name('index');
Route::get('/', 'SiteController@index')->name('index');


Auth::routes(['verify' => true]);
Route::get('/email-verify', 'Auth\LoginController@showverify')->name('email.verify');
Route::post('/verfication/resend', 'Auth\LoginController@verficationResend')->name('verification.resend');
Route::get('email/verify', 'Auth\VerificationController@show')->name('verification.notice');
Route::get('logout', 'Auth\LoginController@logout')->name('logout');

Route::get('register', 'Auth\RegisterController@register');
Route::post('register', 'Auth\RegisterController@store')->name('register');

Route::get('login', 'Auth\LoginController@vliewLogin')->name('login');

Route::get('register', 'Auth\RegisterController@register');
Route::get('register', 'Auth\RegisterController@register');
Route::post('/webhook/stripe-subscription', 'WebhookController@stripeSubscription')->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class])->name('webhook.stripeSubscritpion');
Route::post('/webhook/account-webhook', 'WebhookController@accountWebhook')->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class])->name('webhook.accountWebhook');

Route::post('/contact', 'SiteController@contact')->name('contact');

// Route::get('/checkout/success', function () {
//     return view('checkout.success');
// })->name('checkout.success');

// Route::get('/checkout/cancel', function () {
//     return view('checkout.cancel');
// })->name('checkout.cancel');


Route::prefix('admin')->group(function () {
    Route::post('/signup-get', 'DocumentController@signupGet')->name('document.signupGet');
    /* Admin Page */
    Route::middleware(['auth', 'verified', 'is_admin'])->group(function () {

        Route::group(['prefix' => 'dealer'], function () {
            Route::get('/index', 'DealerController@index')->name('dealers');
            Route::get('/', 'DealerController@index')->name('dealers');
            Route::get('/create', 'DealerController@create')->name('dealers.create');
            Route::get('/edit/{id?}', 'DealerController@edit')->name('dealers.edit');
            Route::get('/get', 'DealerController@get')->name('dealer.list');
            Route::get('/detail', 'DealerController@detail')->name('dealer.detail');
            Route::post('/addupdate', 'DealerController@addupdate')->name('dealer.addupdate');
            Route::post('/delete', 'DealerController@delete')->name('dealer.delete');
            Route::post('/updatefield/{id}', 'DealerController@updatefield')->name('dealer.updatefield');
            Route::get('/updatefield/{id}', 'DealerController@updatefield')->name('dealer.updatefield');
            Route::post('/update-distributor-status', 'DealerController@updateDistributorStatus')->name('dealer.updateDistributorStatus');
        });

        Route::group(['prefix' => 'customer'], function () {
            Route::get('/index', 'CustomerController@index')->name('customers');
            Route::get('/', 'CustomerController@index')->name('customers');
            Route::get('/get', 'CustomerController@get')->name('customer.list');
            Route::get('/detail', 'CustomerController@detail')->name('customer.detail');
            Route::post('/addupdate', 'CustomerController@addupdate')->name('customer.addupdate');
            Route::post('/delete', 'CustomerController@delete')->name('customer.delete');
            Route::post('/updatefield/{id}', 'CustomerController@updatefield')->name('customer.updatefield');
        });

        Route::group(['prefix' => 'document'], function () {
            Route::get('/index', 'DocumentController@index')->name('document');
            Route::get('/', 'DocumentController@index')->name('document');
            Route::post('/get', 'DocumentController@get')->name('document.get');
            Route::post('/addupdate', 'DocumentController@addUpdate')->name('document.addupdate');
            // Route::post('/delete', 'DocumentController@delete')->name('documents.delete');
        });

        Route::group(['prefix' => 'brand'], function () {
            Route::get('/index', 'BrandController@index')->name('brand');
            Route::get('/', 'BrandController@index')->name('brand');
            Route::get('/get', 'BrandController@get')->name('brand.list');
            Route::get('/detail', 'BrandController@detail')->name('brand.detail');
            Route::post('/addupdate', 'BrandController@addupdate')->name('brand.addupdate');
            Route::post('/delete', 'BrandController@delete')->name('brand.delete');
        });

        Route::group(['prefix' => 'product-type'], function () {
            Route::get('/index', 'ProductTypeController@index')->name('product-type');
            Route::get('/', 'ProductTypeController@index')->name('product-type');
            Route::get('/get', 'ProductTypeController@get')->name('product-type.list');
            Route::get('/detail', 'ProductTypeController@detail')->name('product-type.detail');
            Route::post('/addupdate', 'ProductTypeController@addupdate')->name('product-type.addupdate');
            Route::post('/delete', 'ProductTypeController@delete')->name('product-type.delete');
        });

        Route::group(['prefix' => 'product-category'], function () {
            Route::get('/index', 'ProductCategoryController@index')->name('product-category');
            Route::get('/', 'ProductCategoryController@index')->name('product-category');
            Route::get('/get', 'ProductCategoryController@get')->name('product-category.list');
            Route::get('/detail', 'ProductCategoryController@detail')->name('product-category.detail');
            Route::post('/addupdate', 'ProductCategoryController@addupdate')->name('product-category.addupdate');
            Route::post('/delete', 'ProductCategoryController@delete')->name('product-category.delete');
        });

        Route::group(['prefix' => 'product-connection'], function () {
            Route::get('/index', 'ProductConnectionController@index')->name('product-connection');
            Route::get('/', 'ProductConnectionController@index')->name('product-connection');
            Route::get('/get', 'ProductConnectionController@get')->name('product-connection.list');
            Route::get('/detail', 'ProductConnectionController@detail')->name('product-connection.detail');
            Route::post('/addupdate', 'ProductConnectionController@addupdate')->name('product-connection.addupdate');
            Route::post('/delete', 'ProductConnectionController@delete')->name('product-connection.delete');
        });

        Route::group(['prefix' => 'product-execution'], function () {
            Route::get('/index', 'ProductExecutionController@index')->name('product-execution');
            Route::get('/', 'ProductExecutionController@index')->name('product-execution');
            Route::get('/get', 'ProductExecutionController@get')->name('product-execution.list');
            Route::get('/detail', 'ProductExecutionController@detail')->name('product-execution.detail');
            Route::post('/addupdate', 'ProductExecutionController@addupdate')->name('product-execution.addupdate');
            Route::post('/delete', 'ProductExecutionController@delete')->name('product-execution.delete');
        });

        Route::group(['prefix' => 'product'], function () {
            Route::get('/index', 'ProductController@index')->name('product');
            Route::get('/', 'ProductController@index')->name('product');
            Route::get('/create', 'ProductController@create')->name('product.create');
            Route::get('/edit/{id?}', 'ProductController@edit')->name('product.edit');
            Route::get('/view/{id?}', 'ProductController@view')->name('product.view');
            Route::get('/detail', 'ProductController@detail')->name('product.detail');
            Route::post('/addupdate', 'ProductController@addupdate')->name('product.addupdate');
            Route::post('/delete', 'ProductController@delete')->name('product.delete');
            Route::post('/import', 'ProductController@import')->name('product.import');
        });

        Route::post('/country-dealer-filter', 'HomeController@countryDealerFilter')->name('countryDealerFilter');

        //plan types
        Route::group(['prefix' => 'plan-type'], function () {
            Route::get('/index', 'PlanTypeController@index')->name('plan_type');
            Route::get('/', 'PlanTypeController@index')->name('plan_type');
            Route::get('/get', 'PlanTypeController@get')->name('plan_type.list');
            Route::get('/detail', 'PlanTypeController@detail')->name('plan_type.detail');
            Route::post('/addupdate', 'PlanTypeController@addupdate')->name('plan_type.addupdate');
        });

        Route::group(['prefix' => 'ads'], function () {
            Route::get('/index', 'AdController@index')->name('ad');
            Route::get('/', 'AdController@index')->name('ad');
            Route::get('/get', 'AdController@get')->name('ad.list');
            Route::get('/detail', 'AdController@detail')->name('ad.detail');
            Route::post('/addupdate', 'AdController@addupdate')->name('ad.addupdate');
            Route::post('/delete', 'AdController@delete')->name('ad.delete');
            Route::get('/preview', 'AdController@preview')->name('ad.preview');
            Route::post('/preview/fetch', 'AdController@previewFetch')->name('ad.preview.fetch');
        });
    });

    /* All Role Page */
    Route::middleware(['auth', 'verified', 'is_admin'])->group(function () {

        // Common routes
        //Language Translation
        Route::get('/home', 'HomeController@index')->name('home');
        Route::get('/', 'HomeController@index')->name('home');
        Route::post('/dashoard-info', 'HomeController@dashboardDataInfo')->name('home.dashboardDataInfo');
        Route::post('/summary-chart', 'HomeController@summaryChartFilter')->name('home.summaryChartFilter');
        Route::post('/top-sales-chart', 'HomeController@topSalesChartFilter')->name('home.topSalesChartFilter');

        Route::get('/profile', 'HomeController@profile')->name('profile');
        // Route::get('/subscription', 'HomeController@subscription')->name('subscription');
        Route::post('/postsub', 'HomeController@postsub')->name('postsub');
        Route::get('/profileDetail', 'HomeController@profileDetail')->name('profileDetail');
        Route::post('/updateProfile', 'HomeController@updateProfile')->name('updateProfile');
        Route::post('/dealer/contract-update', 'DealerController@ContractUpdate')->name('dealer.contractUpdate');
        Route::get('/feedback', 'DealerController@feedback')->name('dealer.feedback');
        Route::post('/getDealerOfBrand', 'DealerBrandController@getDealerOfBrand')->name('getDealerOfBrand');

        Route::group(['prefix' => 'emaillog'], function () {
            Route::get('/index', 'EmaillogController@index')->name('emaillog');
            Route::get('/', 'EmaillogController@index')->name('emaillog');
            Route::get('/get', 'EmaillogController@get')->name('emaillog.list');
            Route::get('/detail', 'EmaillogController@detail')->name('emaillog.detail');
        });

        Route::group(['prefix' => 'dealer-brand'], function () {
            Route::get('/index', 'DealerBrandController@index')->name('dealer-brand');
            Route::get('/', 'DealerBrandController@index')->name('dealer-brand');
            Route::get('/create', 'DealerBrandController@create')->name('dealer-brand.create');
            Route::get('/get', 'DealerBrandController@get')->name('dealer-brand.list');
            Route::get('/get/product', 'DealerBrandController@getProduct')->name('dealer-brand.productList');
            Route::post('/addupdate', 'DealerBrandController@addupdate')->name('dealer-brand.addupdate');
            Route::post('/delete', 'DealerBrandController@delete')->name('dealer-brand.delete');
            Route::get('/getBrandList', 'DealerBrandController@getBrandList')->name('dealer-brand.getBrandList');
        });

        Route::group(['prefix' => 'product'], function () {
            // Route::get('/product-list', 'ProductController@productlist')->name('product-list');
            Route::get('/get', 'ProductController@get')->name('product.list');
        });

        Route::group(['prefix' => 'enquiry'], function () {
            Route::get('/index', 'PurchaseEnquiryController@index')->name('enquiry');
            Route::get('/', 'PurchaseEnquiryController@index')->name('enquiry');
            Route::get('/view/{id}', 'PurchaseEnquiryController@adminEnquiryView')->name('adminEnquiryView');
            Route::get('/get', 'PurchaseEnquiryController@get')->name('enquiry.list');
            Route::get('/detail', 'PurchaseEnquiryController@detail')->name('enquiry.detail');
            Route::post('/addupdate', 'PurchaseEnquiryController@addupdate')->name('enquiry.addupdate');
            Route::post('/delete', 'PurchaseEnquiryController@delete')->name('enquiry.delete');
            Route::post('/dealerdelete', 'PurchaseEnquiryController@delaerDelete')->name('enquiry.delaerDelete');
            Route::post('/updatefield/{id}', 'PurchaseEnquiryController@updatefield')->name('enquiry.updatefield');
            Route::get('/create', 'PurchaseEnquiryController@create')->name('enquiry.create');
            Route::post('/get-product', 'PurchaseEnquiryController@getProduct')->name('enquiry.getProduct');
            Route::post('/get-productinfo', 'PurchaseEnquiryController@getProductInfo')->name('enquiry.getProductInfo');
            Route::post('/get-filterinfo', 'PurchaseEnquiryController@getFilterOptions')->name('enquiry.getFilterOptions');
            Route::get('/enquiry-documents', 'PurchaseEnquiryController@getEnquiryDocuments')->name('enquiry.getEnquiryDocuments');
            Route::post('/getDealerRatingUrl', 'PurchaseEnquiryController@getDealerRatingUrl')->name('enquiry.getDealerRatingUrl');
            Route::post('/getDealerInfo', 'PurchaseEnquiryController@getDealerInfo')->name('enquiry.getDealerInfo');
        });

        Route::group(['prefix' => 'purchase-enquiry'], function () {
            Route::get('/index', 'PurchaseEnquiryController@dealerEnquiry')->name('dealer-enquiry');
            Route::get('/', 'PurchaseEnquiryController@dealerEnquiry')->name('dealer-enquiry');
            Route::get('/view/{id}', 'PurchaseEnquiryController@dealerEnquiryView')->name('dealerEnquiryView');
            Route::get('/getDealerEnquiry', 'PurchaseEnquiryController@getDealerEnquiry')->name('getDealerEnquiry');
            Route::get('/getDetailEnquiry', 'PurchaseEnquiryController@getDetailEnquiry')->name('getDetailEnquiry');
            Route::post('/sendOffer', 'PurchaseEnquiryController@sendOffer')->name('sendOffer');
            Route::get('/getOfferList', 'PurchaseEnquiryController@getOfferList')->name('getOfferList');
            Route::get('/get-enquiry-products-list', 'PurchaseEnquiryController@getEnquiryProductList')->name('getEnquiryProductList');
            Route::get('/get-offer-products-list', 'PurchaseEnquiryController@getOfferProductList')->name('getOfferProductList');
            Route::get('/getofferDetail', 'PurchaseEnquiryController@getofferDetail')->name('getofferDetail');
            Route::post('/updateOfferStatus', 'PurchaseEnquiryController@updateOfferStatus')->name('updateOfferStatus');
            Route::post('/getCheckoutSession', 'PurchaseEnquiryController@getCheckoutSession')->name('getCheckoutSession');
            Route::get('/checkout/success', 'PurchaseEnquiryController@checkoutSuccess')->name('purchase.checkout.success');
            Route::get('/checkout/cancel', 'PurchaseEnquiryController@checkoutCancel')->name('purchase.checkout.cancel');
        });

        Route::group(['prefix' => 'calendar'], function () {
            Route::get('/index', 'AppointmentDealerController@calendarIndex')->name('calendar');
            Route::get('/', 'AppointmentDealerController@calendarIndex')->name('calendar');
            Route::post('/event/addupdate', 'AppointmentDealerController@eventAddupdate')->name('calendar.eventAddupdate');
            Route::post('/event/delete', 'AppointmentDealerController@eventDelete')->name('calendar.eventDelete');
        });

        Route::group(['prefix' => 'appointment'], function () {
            Route::get('/index', 'AppointmentDealerController@appointmentIndex')->name('appointment');
            Route::get('/', 'AppointmentDealerController@appointmentIndex')->name('appointment');
            Route::get('/getlist', 'AppointmentDealerController@getAppointment')->name('appointment.list');
            Route::post('/getproducts', 'AppointmentDealerController@getProducts')->name('appointment.products');
            Route::get('/create', 'AppointmentDealerController@create')->name('appointment.create');
            Route::post('/addupdate', 'AppointmentDealerController@addupdate')->name('appointment.addupdate');
            Route::get('/getCustomerList', 'AppointmentDealerController@getCustomerList')->name('appointment.getCustomerList');
            Route::post('/updateStatus', 'AppointmentDealerController@updateStatus')->name('appointment.updateStatus');
            Route::post('/updateRating', 'AppointmentDealerController@updateRating')->name('appointment.updateRating');
            Route::post('/reschedule-appointment', 'AppointmentDealerController@rescheduleAppointment')->name('appointment.rescheduleAppointment');
            Route::post('/get-time-data', 'AppointmentDealerController@getTimePickerData')->name('appointment.getTimePickerData');
        });

        Route::group(['prefix' => 'dealer-product'], function () {
            Route::get('/index', 'ProductController@dealerProduct')->name('dealer-product');
            Route::get('/', 'ProductController@dealerProduct')->name('dealer-product');
            Route::post('/filter-options', 'ProductController@getFilterOptions')->name('getFilterOptions');
            Route::get('/getDealerProduct', 'ProductController@getDealerProduct')->name('getDealerProduct');
            Route::get('/getDealerProductAttributes', 'ProductController@getDealerProductAttributes')->name('getDealerProductAttributes');
            Route::post('/addDealerProductAttributes', 'ProductController@addDealerProductAttributes')->name('addDealerProductAttributes');
        });

        Route::group(['prefix' => 'sales'], function () {
            Route::get('/index', 'OrderController@salesIndex')->name('sales');
            Route::get('/', 'OrderController@salesIndex')->name('sales');
            Route::get('/salesget', 'OrderController@salesGet')->name('sales.list');
            Route::get('/purchaseget', 'OrderController@purchaseGet')->name('purchase.list');
            Route::get('/order-detail', 'OrderController@getDetailOrder')->name('getDetailOrder');
            Route::get('/get-order-products-list', 'OrderController@getOrderProductList')->name('getOrderProductList');
            Route::post('/addupdate', 'OrderController@addupdate')->name('sales.addupdate');
            Route::post('/confirm-order-payment', 'OrderController@confirmOrderPayment')->name('sales.confirmOrderPayment');
            Route::post('/order-shipping', 'OrderController@orderShipping')->name('sales.orderShipping');
            Route::post('/order-canceled', 'OrderController@orderCanceled')->name('sales.orderCanceled');
            Route::post('/delete', 'OrderController@delete')->name('sales.delete');
            Route::get('/invoice', 'OrderController@getInvoice')->name('sales.getInvoice');
        });

        Route::group(['prefix' => 'contacts'], function () {
            Route::get('/index', 'ContactController@contactsIndex')->name('contacts');
            Route::get('/', 'ContactController@contactsIndex')->name('contacts');
            Route::post('/addupdate', 'ContactController@addupdate')->name('contacts.addupdate');
            Route::get('/get', 'ContactController@get')->name('contacts.list');
            Route::get('/detail', 'ContactController@detail')->name('contacts.detail');
            Route::post('/delete', 'ContactController@delete')->name('contacts.delete');
            Route::post('/import/csv', 'ContactController@uploadContacts')->name('contacts.import.csv');
        });

        Route::group(['prefix' => 'purchases'], function () {
            Route::get('/index', 'OrderController@purchaseIndex')->name('purchases');
            Route::get('/', 'OrderController@purchaseIndex')->name('purchases');
            Route::get('/get', 'OrderController@get')->name('purchases.list');
            Route::get('/detail', 'OrderController@detail')->name('purchases.detail');
            Route::post('/addupdate', 'OrderController@addupdate')->name('purchases.addupdate');
            Route::post('/addRatingUrl', 'OrderController@addRatingUrl')->name('purchases.addRatingUrl');
            Route::post('/delete', 'OrderController@delete')->name('purchases.delete');
        });

        Route::group(['prefix' => 'subscription'], function () {
            Route::get('/index', 'SubscriptionController@index')->name('subscription');
            Route::get('/', 'SubscriptionController@index')->name('subscription');
            Route::post('/subscribe', 'SubscriptionController@processSubscription')->name('subscription.subscribe');
            Route::get('/checkout/success', 'SubscriptionController@success')->name('checkout.success');
            Route::get('/checkout/cancel', 'SubscriptionController@cancel')->name('checkout.cancel');
        });
        Route::group(['prefix' => 'stripe'], function () {
            Route::get('/authorize-account', 'StripeController@authorizeStripeAccount')->name('stripe.authorize-account');
            Route::get('/authorize-return', 'StripeController@authorize_return')->name('stripe.authorize-return');
            // Route::get('/authorize-refresh', 'StripeController@authorize_refresh')->name('stripe.authorize-refresh');
            Route::get('/create_invoice', 'StripeController@create_invoice')->name('stripe.create_invoice');
        });
        Route::group(['prefix' => 'order-payments'], function () {
            Route::get('/index', 'OrderPaymentLogController@index')->name('purcahse-payment-log');
            Route::get('/', 'OrderPaymentLogController@index')->name('purcahse-payment-log');
            Route::get('/get', 'OrderPaymentLogController@get')->name('purcahse-payment-log.list');
            Route::get('/detail', 'DealerController@detail')->name('purcahse-payment-log.detail');
        });
        Route::group(['prefix' => 'subscription-log'], function () {
            Route::get('/index', 'SubscriptionLogController@index')->name('subscription-log');
            Route::get('/', 'SubscriptionLogController@index')->name('subscription-log');
            Route::get('/get', 'SubscriptionLogController@get')->name('subscription-log.list');
            Route::get('/invoice', 'SubscriptionLogController@getInvoice')->name('subscription-log.getInvoice');
        });

        // For Customer Analysis Chart
        Route::group(['prefix' => 'customer-analysis'], function () {
            Route::get('/index', 'CustomerAnalysisController@index')->name('customer-analysis');
            Route::get('/', 'CustomerAnalysisController@index')->name('customer-analysis');
            Route::get('/age-wise-chart', 'CustomerAnalysisController@getAgeChartData')->name('customer-analysis.age-wise-chart');
            Route::get('/gender-wise-chart', 'CustomerAnalysisController@getGenderWise')->name('customer-analysis.gender-wise-chart');
            Route::get('/city-wise-chart', 'CustomerAnalysisController@getCityWise')->name('customer-analysis.city-wise-chart');
            Route::get('/zipcode-wise-chart', 'CustomerAnalysisController@getZipcodeWise')->name('customer-analysis.zipcode-wise-chart');
            Route::get('/brand-wise-chart', 'CustomerAnalysisController@getBrandWise')->name('customer-analysis.brand-wise-chart');
            Route::get('/category-wise-chart', 'CustomerAnalysisController@getCategoryWise')->name('customer-analysis.category-wise-chart');
            Route::get('/product-type-wise-chart', 'CustomerAnalysisController@getProducTypeWise')->name('customer-analysis.product-type-wise-chart');
            Route::get('/product-wise-chart', 'CustomerAnalysisController@getProducWise')->name('customer-analysis.product-wise-chart');
        });
    });
});

/* Dealer Role Page */
Route::prefix('dealer')->middleware(['auth', 'verified', 'is_dealer'])->as('dealer.')->group(function () {
    Route::middleware('check_dealer_subscription')->group(function () {
        Route::get('/home', 'HomeController@index')->name('home');
        Route::get('/', 'HomeController@index')->name('home');
        Route::post('/dashoard-info', 'HomeController@dashboardDataInfo')->name('home.dashboardDataInfo');
        Route::post('/summary-chart', 'HomeController@summaryChartFilter')->name('home.summaryChartFilter');
        Route::post('/top-sales-chart', 'HomeController@topSalesChartFilter')->name('home.topSalesChartFilter');

        Route::get('/profile', 'HomeController@profile')->name('profile');
        // Route::get('/subscription', 'HomeController@subscription')->name('subscription');
        Route::post('/postsub', 'HomeController@postsub')->name('postsub');
        Route::get('/profileDetail', 'HomeController@profileDetail')->name('profileDetail');
        Route::post('/updateProfile', 'HomeController@updateProfile')->name('updateProfile');
        Route::post('/dealer/contract-update', 'DealerController@ContractUpdate')->name('contractUpdate');
        Route::get('/feedback', 'DealerController@feedback')->name('feedback');
        Route::post('/getDealerOfBrand', 'DealerBrandController@getDealerOfBrand')->name('getDealerOfBrand');


        Route::group(['prefix' => 'emaillog'], function () {
            Route::get('/index', 'EmaillogController@index')->name('emaillog');
            Route::get('/', 'EmaillogController@index')->name('emaillog');
            Route::get('/get', 'EmaillogController@get')->name('emaillog.list');
            Route::get('/detail', 'EmaillogController@detail')->name('emaillog.detail');
        });


        Route::group(['prefix' => 'dealer-brand'], function () {
            Route::get('/index', 'DealerBrandController@index')->name('dealer-brand');
            Route::get('/', 'DealerBrandController@index')->name('dealer-brand');
            Route::get('/create', 'DealerBrandController@create')->name('dealer-brand.create');
            Route::get('/get', 'DealerBrandController@get')->name('dealer-brand.list');
            Route::get('/get/product', 'DealerBrandController@getProduct')->name('dealer-brand.productList');
            Route::post('/addupdate', 'DealerBrandController@addupdate')->name('dealer-brand.addupdate');
            Route::post('/delete', 'DealerBrandController@delete')->name('dealer-brand.delete');
            Route::get('/getBrandList', 'DealerBrandController@getBrandList')->name('dealer-brand.getBrandList');
        });

        Route::group(['prefix' => 'product'], function () {
            // Route::get('/product-list', 'ProductController@productlist')->name('product-list');
            Route::get('/get', 'ProductController@get')->name('product.list');
        });

        Route::group(['prefix' => 'enquiry'], function () {
            Route::get('/index', 'PurchaseEnquiryController@index')->name('enquiry');
            Route::get('/', 'PurchaseEnquiryController@index')->name('enquiry');
            Route::get('/view/{id}', 'PurchaseEnquiryController@customerEnquiryView')->name('customerEnquiryView');
            Route::get('/get', 'PurchaseEnquiryController@get')->name('enquiry.list');
            Route::get('/detail', 'PurchaseEnquiryController@detail')->name('enquiry.detail');
            Route::post('/addupdate', 'PurchaseEnquiryController@addupdate')->name('enquiry.addupdate');
            Route::post('/delete', 'PurchaseEnquiryController@delete')->name('enquiry.delete');
            Route::post('/dealerdelete', 'PurchaseEnquiryController@delaerDelete')->name('enquiry.delaerDelete');
            Route::post('/updatefield/{id}', 'PurchaseEnquiryController@updatefield')->name('enquiry.updatefield');
            Route::get('/create', 'PurchaseEnquiryController@create')->name('enquiry.create');
            Route::post('/get-product', 'PurchaseEnquiryController@getProduct')->name('enquiry.getProduct');
            Route::post('/get-productinfo', 'PurchaseEnquiryController@getProductInfo')->name('enquiry.getProductInfo');
            Route::post('/get-filterinfo', 'PurchaseEnquiryController@getFilterOptions')->name('enquiry.getFilterOptions');
            Route::get('/enquiry-documents', 'PurchaseEnquiryController@getEnquiryDocuments')->name('enquiry.getEnquiryDocuments');
            Route::post('/getDealerRatingUrl', 'PurchaseEnquiryController@getDealerRatingUrl')->name('enquiry.getDealerRatingUrl');
            Route::post('/getDealerInfo', 'PurchaseEnquiryController@getDealerInfo')->name('enquiry.getDealerInfo');
        });

        Route::group(['prefix' => 'purchase-enquiry'], function () {
            Route::get('/index', 'PurchaseEnquiryController@dealerEnquiry')->name('dealer-enquiry');
            Route::get('/', 'PurchaseEnquiryController@dealerEnquiry')->name('dealer-enquiry');
            Route::get('/view/{id}', 'PurchaseEnquiryController@dealerEnquiryView')->name('dealerEnquiryView');
            Route::get('/getDealerEnquiry', 'PurchaseEnquiryController@getDealerEnquiry')->name('getDealerEnquiry');
            Route::get('/getDetailEnquiry', 'PurchaseEnquiryController@getDetailEnquiry')->name('getDetailEnquiry');
            Route::post('/sendOffer', 'PurchaseEnquiryController@sendOffer')->name('sendOffer');
            Route::get('/getOfferList', 'PurchaseEnquiryController@getOfferList')->name('getOfferList');
            Route::get('/get-enquiry-products-list', 'PurchaseEnquiryController@getEnquiryProductList')->name('getEnquiryProductList');
            Route::get('/get-offer-products-list', 'PurchaseEnquiryController@getOfferProductList')->name('getOfferProductList');
            Route::get('/getofferDetail', 'PurchaseEnquiryController@getofferDetail')->name('getofferDetail');
            Route::post('/updateOfferStatus', 'PurchaseEnquiryController@updateOfferStatus')->name('updateOfferStatus');
            Route::post('/getCheckoutSession', 'PurchaseEnquiryController@getCheckoutSession')->name('getCheckoutSession');
            Route::get('/checkout/success', 'PurchaseEnquiryController@checkoutSuccess')->name('purchase.checkout.success');
            Route::get('/checkout/cancel', 'PurchaseEnquiryController@checkoutCancel')->name('purchase.checkout.cancel');
        });

        Route::group(['prefix' => 'calendar'], function () {
            Route::get('/index', 'AppointmentDealerController@calendarIndex')->name('calendar');
            Route::get('/', 'AppointmentDealerController@calendarIndex')->name('calendar');
            Route::post('/event/addupdate', 'AppointmentDealerController@eventAddupdate')->name('calendar.eventAddupdate');
            Route::post('/event/delete', 'AppointmentDealerController@eventDelete')->name('calendar.eventDelete');
        });

        Route::group(['prefix' => 'appointment'], function () {
            Route::get('/index', 'AppointmentDealerController@appointmentIndex')->name('appointment');
            Route::get('/', 'AppointmentDealerController@appointmentIndex')->name('appointment');
            Route::get('/getlist', 'AppointmentDealerController@getAppointment')->name('appointment.list');
            Route::post('/getproducts', 'AppointmentDealerController@getProducts')->name('appointment.products');
            Route::get('/create', 'AppointmentDealerController@create')->name('appointment.create');
            Route::post('/addupdate', 'AppointmentDealerController@addupdate')->name('appointment.addupdate');
            Route::get('/getCustomerList', 'AppointmentDealerController@getCustomerList')->name('appointment.getCustomerList');
            Route::post('/updateStatus', 'AppointmentDealerController@updateStatus')->name('appointment.updateStatus');
            Route::post('/updateRating', 'AppointmentDealerController@updateRating')->name('appointment.updateRating');
            Route::post('/reschedule-appointment', 'AppointmentDealerController@rescheduleAppointment')->name('appointment.rescheduleAppointment');
            Route::post('/get-time-data', 'AppointmentDealerController@getTimePickerData')->name('appointment.getTimePickerData');
        });

        Route::group(['prefix' => 'dealer-product'], function () {
            Route::get('/index', 'ProductController@dealerProduct')->name('dealer-product');
            Route::get('/', 'ProductController@dealerProduct')->name('dealer-product');
            Route::post('/filter-options', 'ProductController@getFilterOptions')->name('getFilterOptions');
            Route::get('/getDealerProduct', 'ProductController@getDealerProduct')->name('getDealerProduct');
            Route::get('/getDealerProductAttributes', 'ProductController@getDealerProductAttributes')->name('getDealerProductAttributes');
            Route::post('/addDealerProductAttributes', 'ProductController@addDealerProductAttributes')->name('addDealerProductAttributes');
        });

        Route::group(['prefix' => 'sales'], function () {
            Route::get('/index', 'OrderController@salesIndex')->name('sales');
            Route::get('/', 'OrderController@salesIndex')->name('sales');
            Route::get('/salesget', 'OrderController@salesGet')->name('sales.list');
            Route::get('/purchaseget', 'OrderController@purchaseGet')->name('purchase.list');
            Route::get('/order-detail', 'OrderController@getDetailOrder')->name('getDetailOrder');
            Route::get('/get-order-products-list', 'OrderController@getOrderProductList')->name('getOrderProductList');
            Route::post('/addupdate', 'OrderController@addupdate')->name('sales.addupdate');
            Route::post('/confirm-order-payment', 'OrderController@confirmOrderPayment')->name('sales.confirmOrderPayment');
            Route::post('/order-shipping', 'OrderController@orderShipping')->name('sales.orderShipping');
            Route::post('/order-canceled', 'OrderController@orderCanceled')->name('sales.orderCanceled');
            Route::post('/delete', 'OrderController@delete')->name('sales.delete');
            Route::get('/invoice', 'OrderController@getInvoice')->name('sales.getInvoice');
        });

        Route::group(['prefix' => 'contacts'], function () {
            Route::get('/index', 'ContactController@contactsIndex')->name('contacts');
            Route::get('/', 'ContactController@contactsIndex')->name('contacts');
            Route::post('/addupdate', 'ContactController@addupdate')->name('contacts.addupdate');
            Route::get('/get', 'ContactController@get')->name('contacts.list');
            Route::get('/detail', 'ContactController@detail')->name('contacts.detail');
            Route::post('/delete', 'ContactController@delete')->name('contacts.delete');
            Route::post('/import/csv', 'ContactController@uploadContacts')->name('contacts.import.csv');
        });

        Route::group(['prefix' => 'purchases'], function () {
            Route::get('/index', 'OrderController@purchaseIndex')->name('purchases');
            Route::get('/', 'OrderController@purchaseIndex')->name('purchases');
            Route::get('/get', 'OrderController@get')->name('purchases.list');
            Route::get('/detail', 'OrderController@detail')->name('purchases.detail');
            Route::post('/addupdate', 'OrderController@addupdate')->name('purchases.addupdate');
            Route::post('/addRatingUrl', 'OrderController@addRatingUrl')->name('purchases.addRatingUrl');
            Route::post('/delete', 'OrderController@delete')->name('purchases.delete');
        });

        Route::group(['prefix' => 'order-payments'], function () {
            Route::get('/index', 'OrderPaymentLogController@index')->name('purcahse-payment-log');
            Route::get('/', 'OrderPaymentLogController@index')->name('purcahse-payment-log');
            Route::get('/get', 'OrderPaymentLogController@get')->name('purcahse-payment-log.list');
            Route::get('/detail', 'DealerController@detail')->name('purcahse-payment-log.detail');
        });
        Route::group(['prefix' => 'subscription-log'], function () {
            Route::get('/index', 'SubscriptionLogController@index')->name('subscription-log');
            Route::get('/', 'SubscriptionLogController@index')->name('subscription-log');
            Route::get('/get', 'SubscriptionLogController@get')->name('subscription-log.list');
            Route::get('/invoice', 'SubscriptionLogController@getInvoice')->name('subscription-log.getInvoice');
        });

        Route::post('/country-dealer-filter', 'HomeController@countryDealerFilter')->name('countryDealerFilter');

        // For Customer Analysis Chart
        Route::group(['prefix' => 'customer-analysis'], function () {
            Route::get('/index', 'CustomerAnalysisController@index')->name('customer-analysis');
            Route::get('/', 'CustomerAnalysisController@index')->name('customer-analysis');
            Route::get('/age-wise-chart', 'CustomerAnalysisController@getAgeChartData')->name('customer-analysis.age-wise-chart');
            Route::get('/gender-wise-chart', 'CustomerAnalysisController@getGenderWise')->name('customer-analysis.gender-wise-chart');
            Route::get('/city-wise-chart', 'CustomerAnalysisController@getCityWise')->name('customer-analysis.city-wise-chart');
            Route::get('/zipcode-wise-chart', 'CustomerAnalysisController@getZipcodeWise')->name('customer-analysis.zipcode-wise-chart');
            Route::get('/brand-wise-chart', 'CustomerAnalysisController@getBrandWise')->name('customer-analysis.brand-wise-chart');
            Route::get('/category-wise-chart', 'CustomerAnalysisController@getCategoryWise')->name('customer-analysis.category-wise-chart');
            Route::get('/product-type-wise-chart', 'CustomerAnalysisController@getProducTypeWise')->name('customer-analysis.product-type-wise-chart');
            Route::get('/product-wise-chart', 'CustomerAnalysisController@getProducWise')->name('customer-analysis.product-wise-chart');
        });
    });

    Route::group(['prefix' => 'subscription'], function () {
        Route::get('/index', 'SubscriptionController@index')->name('subscription');
        Route::get('/', 'SubscriptionController@index')->name('subscription');
        Route::post('/subscribe', 'SubscriptionController@processSubscription')->name('subscription.subscribe');
        Route::get('/checkout/success', 'SubscriptionController@success')->name('checkout.success');
        Route::get('/checkout/cancel', 'SubscriptionController@cancel')->name('checkout.cancel');
    });
    Route::group(['prefix' => 'stripe'], function () {
        Route::get('/authorize-account', 'StripeController@authorizeStripeAccount')->name('stripe.authorize-account');
        Route::get('/authorize-return', 'StripeController@authorize_return')->name('stripe.authorize-return');
        // Route::get('/authorize-refresh', 'StripeController@authorize_refresh')->name('stripe.authorize-refresh');
        Route::get('/create_invoice', 'StripeController@create_invoice')->name('stripe.create_invoice');
    });

    /* delete account */
    Route::post('/delete-account', 'DealerController@deleteAccount')->name('deleteAccount');
});

/* Customer Role Page */
Route::prefix('customer')->middleware(['auth', 'verified', 'is_customer'])->as('customer.')->group(function () {

    Route::get('/home', 'HomeController@index')->name('home');
    Route::get('/', 'HomeController@index')->name('home');
    Route::post('/dashoard-info', 'HomeController@dashboardDataInfo')->name('home.dashboardDataInfo');

    Route::get('/profile', 'HomeController@profile')->name('profile');
    // Route::get('/subscription', 'HomeController@subscription')->name('subscription');
    Route::post('/postsub', 'HomeController@postsub')->name('postsub');
    Route::get('/profileDetail', 'HomeController@profileDetail')->name('profileDetail');
    Route::post('/updateProfile', 'HomeController@updateProfile')->name('updateProfile');
    Route::post('/dealer/contract-update', 'DealerController@ContractUpdate')->name('contractUpdate');
    Route::get('/feedback', 'DealerController@feedback')->name('feedback');
    Route::post('/getDealerOfBrand', 'DealerBrandController@getDealerOfBrand')->name('getDealerOfBrand');


    Route::group(['prefix' => 'emaillog'], function () {
        Route::get('/index', 'EmaillogController@index')->name('emaillog');
        Route::get('/', 'EmaillogController@index')->name('emaillog');
        Route::get('/get', 'EmaillogController@get')->name('emaillog.list');
        Route::get('/detail', 'EmaillogController@detail')->name('emaillog.detail');
    });


    Route::group(['prefix' => 'dealer-brand'], function () {
        Route::get('/index', 'DealerBrandController@index')->name('dealer-brand');
        Route::get('/', 'DealerBrandController@index')->name('dealer-brand');
        Route::get('/create', 'DealerBrandController@create')->name('dealer-brand.create');
        Route::get('/get', 'DealerBrandController@get')->name('dealer-brand.list');
        Route::get('/get/product', 'DealerBrandController@getProduct')->name('dealer-brand.productList');
        Route::post('/addupdate', 'DealerBrandController@addupdate')->name('dealer-brand.addupdate');
        Route::post('/delete', 'DealerBrandController@delete')->name('dealer-brand.delete');
        Route::get('/getBrandList', 'DealerBrandController@getBrandList')->name('dealer-brand.getBrandList');
    });

    Route::group(['prefix' => 'product'], function () {
        // Route::get('/product-list', 'ProductController@productlist')->name('product-list');
        Route::get('/get', 'ProductController@get')->name('product.list');
    });

    Route::group(['prefix' => 'enquiry'], function () {
        Route::get('/index', 'PurchaseEnquiryController@index')->name('enquiry');
        Route::get('/', 'PurchaseEnquiryController@index')->name('enquiry');
        Route::get('/view/{id}', 'PurchaseEnquiryController@customerEnquiryView')->name('customerEnquiryView');
        Route::get('/get', 'PurchaseEnquiryController@get')->name('enquiry.list');
        Route::get('/detail', 'PurchaseEnquiryController@detail')->name('enquiry.detail');
        Route::post('/addupdate', 'PurchaseEnquiryController@addupdate')->name('enquiry.addupdate');
        Route::post('/delete', 'PurchaseEnquiryController@delete')->name('enquiry.delete');
        Route::post('/dealerdelete', 'PurchaseEnquiryController@delaerDelete')->name('enquiry.delaerDelete');
        Route::post('/updatefield/{id}', 'PurchaseEnquiryController@updatefield')->name('enquiry.updatefield');
        Route::get('/create', 'PurchaseEnquiryController@create')->name('enquiry.create');
        Route::post('/get-product', 'PurchaseEnquiryController@getProduct')->name('enquiry.getProduct');
        Route::post('/get-productinfo', 'PurchaseEnquiryController@getProductInfo')->name('enquiry.getProductInfo');
        Route::post('/get-filterinfo', 'PurchaseEnquiryController@getFilterOptions')->name('enquiry.getFilterOptions');
        Route::get('/enquiry-documents', 'PurchaseEnquiryController@getEnquiryDocuments')->name('enquiry.getEnquiryDocuments');
        Route::post('/getDealerRatingUrl', 'PurchaseEnquiryController@getDealerRatingUrl')->name('enquiry.getDealerRatingUrl');
        Route::post('/getDealerInfo', 'PurchaseEnquiryController@getDealerInfo')->name('enquiry.getDealerInfo');
    });

    Route::group(['prefix' => 'purchase-enquiry'], function () {
        Route::get('/index', 'PurchaseEnquiryController@dealerEnquiry')->name('dealer-enquiry');
        Route::get('/', 'PurchaseEnquiryController@dealerEnquiry')->name('dealer-enquiry');
        Route::get('/view/{id}', 'PurchaseEnquiryController@dealerEnquiryView')->name('dealerEnquiryView');
        Route::get('/getDealerEnquiry', 'PurchaseEnquiryController@getDealerEnquiry')->name('getDealerEnquiry');
        Route::get('/getDetailEnquiry', 'PurchaseEnquiryController@getDetailEnquiry')->name('getDetailEnquiry');
        Route::post('/sendOffer', 'PurchaseEnquiryController@sendOffer')->name('sendOffer');
        Route::get('/getOfferList', 'PurchaseEnquiryController@getOfferList')->name('getOfferList');
        Route::get('/get-enquiry-products-list', 'PurchaseEnquiryController@getEnquiryProductList')->name('getEnquiryProductList');
        Route::get('/get-offer-products-list', 'PurchaseEnquiryController@getOfferProductList')->name('getOfferProductList');
        Route::get('/getofferDetail', 'PurchaseEnquiryController@getofferDetail')->name('getofferDetail');
        Route::post('/updateOfferStatus', 'PurchaseEnquiryController@updateOfferStatus')->name('updateOfferStatus');
        Route::post('/getCheckoutSession', 'PurchaseEnquiryController@getCheckoutSession')->name('getCheckoutSession');
        Route::get('/checkout/success', 'PurchaseEnquiryController@checkoutSuccess')->name('purchase.checkout.success');
        Route::get('/checkout/cancel', 'PurchaseEnquiryController@checkoutCancel')->name('purchase.checkout.cancel');
    });

    Route::group(['prefix' => 'calendar'], function () {
        Route::get('/index', 'AppointmentDealerController@calendarIndex')->name('calendar');
        Route::get('/', 'AppointmentDealerController@calendarIndex')->name('calendar');
        Route::post('/event/addupdate', 'AppointmentDealerController@eventAddupdate')->name('calendar.eventAddupdate');
        Route::post('/event/delete', 'AppointmentDealerController@eventDelete')->name('calendar.eventDelete');
    });

    Route::group(['prefix' => 'appointment'], function () {
        Route::get('/index', 'AppointmentDealerController@appointmentIndex')->name('appointment');
        Route::get('/', 'AppointmentDealerController@appointmentIndex')->name('appointment');
        Route::get('/getlist', 'AppointmentDealerController@getAppointment')->name('appointment.list');
        Route::post('/getproducts', 'AppointmentDealerController@getProducts')->name('appointment.products');
        Route::get('/create', 'AppointmentDealerController@create')->name('appointment.create');
        Route::post('/addupdate', 'AppointmentDealerController@addupdate')->name('appointment.addupdate');
        Route::get('/getCustomerList', 'AppointmentDealerController@getCustomerList')->name('appointment.getCustomerList');
        Route::post('/updateStatus', 'AppointmentDealerController@updateStatus')->name('appointment.updateStatus');
        Route::post('/updateRating', 'AppointmentDealerController@updateRating')->name('appointment.updateRating');
        Route::post('/reschedule-appointment', 'AppointmentDealerController@rescheduleAppointment')->name('appointment.rescheduleAppointment');
        Route::post('/get-time-data', 'AppointmentDealerController@getTimePickerData')->name('appointment.getTimePickerData');
    });

    Route::group(['prefix' => 'dealer-product'], function () {
        Route::get('/index', 'ProductController@dealerProduct')->name('dealer-product');
        Route::get('/', 'ProductController@dealerProduct')->name('dealer-product');
        Route::post('/filter-options', 'ProductController@getFilterOptions')->name('getFilterOptions');
        Route::get('/getDealerProduct', 'ProductController@getDealerProduct')->name('getDealerProduct');
        Route::get('/getDealerProductAttributes', 'ProductController@getDealerProductAttributes')->name('getDealerProductAttributes');
        Route::post('/addDealerProductAttributes', 'ProductController@addDealerProductAttributes')->name('addDealerProductAttributes');
    });

    Route::group(['prefix' => 'sales'], function () {
        Route::get('/index', 'OrderController@salesIndex')->name('sales');
        Route::get('/', 'OrderController@salesIndex')->name('sales');
        Route::get('/salesget', 'OrderController@salesGet')->name('sales.list');
        Route::get('/purchaseget', 'OrderController@purchaseGet')->name('purchase.list');
        Route::get('/order-detail', 'OrderController@getDetailOrder')->name('getDetailOrder');
        Route::get('/get-order-products-list', 'OrderController@getOrderProductList')->name('getOrderProductList');
        Route::post('/addupdate', 'OrderController@addupdate')->name('sales.addupdate');
        Route::post('/confirm-order-payment', 'OrderController@confirmOrderPayment')->name('sales.confirmOrderPayment');
        Route::post('/order-shipping', 'OrderController@orderShipping')->name('sales.orderShipping');
        Route::post('/order-canceled', 'OrderController@orderCanceled')->name('sales.orderCanceled');
        Route::post('/delete', 'OrderController@delete')->name('sales.delete');
        Route::get('/invoice', 'OrderController@getInvoice')->name('sales.getInvoice');
    });

    Route::group(['prefix' => 'contacts'], function () {
        Route::get('/index', 'ContactController@contactsIndex')->name('contacts');
        Route::get('/', 'ContactController@contactsIndex')->name('contacts');
        Route::post('/addupdate', 'ContactController@addupdate')->name('contacts.addupdate');
        Route::get('/get', 'ContactController@get')->name('contacts.list');
        Route::get('/detail', 'ContactController@detail')->name('contacts.detail');
        Route::post('/delete', 'ContactController@delete')->name('contacts.delete');
        Route::post('/import/csv', 'ContactController@uploadContacts')->name('contacts.import.csv');
    });

    Route::group(['prefix' => 'purchases'], function () {
        Route::get('/index', 'OrderController@purchaseIndex')->name('purchases');
        Route::get('/', 'OrderController@purchaseIndex')->name('purchases');
        Route::get('/get', 'OrderController@get')->name('purchases.list');
        Route::get('/detail', 'OrderController@detail')->name('purchases.detail');
        Route::post('/addupdate', 'OrderController@addupdate')->name('purchases.addupdate');
        Route::post('/addRatingUrl', 'OrderController@addRatingUrl')->name('purchases.addRatingUrl');
        Route::post('/delete', 'OrderController@delete')->name('purchases.delete');
    });

    Route::group(['prefix' => 'subscription'], function () {
        Route::get('/index', 'SubscriptionController@index')->name('subscription');
        Route::get('/', 'SubscriptionController@index')->name('subscription');
        Route::post('/subscribe', 'SubscriptionController@processSubscription')->name('subscription.subscribe');
        Route::get('/checkout/success', 'SubscriptionController@success')->name('checkout.success');
        Route::get('/checkout/cancel', 'SubscriptionController@cancel')->name('checkout.cancel');
    });
    Route::group(['prefix' => 'stripe'], function () {
        Route::get('/authorize-account', 'StripeController@authorizeStripeAccount')->name('stripe.authorize-account');
        Route::get('/authorize-return', 'StripeController@authorize_return')->name('stripe.authorize-return');
        // Route::get('/authorize-refresh', 'StripeController@authorize_refresh')->name('stripe.authorize-refresh');
        Route::get('/create_invoice', 'StripeController@create_invoice')->name('stripe.create_invoice');
    });
    Route::group(['prefix' => 'order-payments'], function () {
        Route::get('/index', 'OrderPaymentLogController@index')->name('purcahse-payment-log');
        Route::get('/', 'OrderPaymentLogController@index')->name('purcahse-payment-log');
        Route::get('/get', 'OrderPaymentLogController@get')->name('purcahse-payment-log.list');
        Route::get('/detail', 'DealerController@detail')->name('purcahse-payment-log.detail');
    });
    Route::group(['prefix' => 'subscription-log'], function () {
        Route::get('/index', 'SubscriptionLogController@index')->name('subscription-log');
        Route::get('/', 'SubscriptionLogController@index')->name('subscription-log');
        Route::get('/get', 'SubscriptionLogController@get')->name('subscription-log.list');
        Route::get('/invoice', 'SubscriptionLogController@getInvoice')->name('subscription-log.getInvoice');
    });

    // For Customer Analysis Chart
    Route::group(['prefix' => 'customer-analysis'], function () {
        Route::get('/index', 'CustomerAnalysisController@index')->name('customer-analysis');
        Route::get('/', 'CustomerAnalysisController@index')->name('customer-analysis');
        Route::get('/age-wise-chart', 'CustomerAnalysisController@getAgeChartData')->name('customer-analysis.age-wise-chart');
        Route::get('/gender-wise-chart', 'CustomerAnalysisController@getGenderWise')->name('customer-analysis.gender-wise-chart');
        Route::get('/city-wise-chart', 'CustomerAnalysisController@getCityWise')->name('customer-analysis.city-wise-chart');
        Route::get('/zipcode-wise-chart', 'CustomerAnalysisController@getZipcodeWise')->name('customer-analysis.zipcode-wise-chart');
        Route::get('/brand-wise-chart', 'CustomerAnalysisController@getBrandWise')->name('customer-analysis.brand-wise-chart');
        Route::get('/category-wise-chart', 'CustomerAnalysisController@getCategoryWise')->name('customer-analysis.category-wise-chart');
        Route::get('/product-type-wise-chart', 'CustomerAnalysisController@getProducTypeWise')->name('customer-analysis.product-type-wise-chart');
        Route::get('/product-wise-chart', 'CustomerAnalysisController@getProducWise')->name('customer-analysis.product-wise-chart');
    });

    Route::post('/country-dealer-filter', 'HomeController@countryDealerFilter')->name('countryDealerFilter');

    /* delete account */
    Route::post('/delete-account', 'CustomerController@deleteAccount')->name('deleteAccount');
});

/* Comman Routes */
Route::group(['prefix' => 'notification', 'middleware' => 'auth'], function () {
    Route::post('/get', 'MyNotification@getNotification')->name('notification.getNotification');
});
Route::middleware('auth')->group(function () {
    Route::get('/change-password', 'PasswordController@index')->name('change-password');
    Route::post('/passwords/update', 'PasswordController@update')->name('passwords.update');
});

Route::get('lang/{locale}', 'HomeController@lang');


// Clear all cache
Route::get('/clear', function () {
    Artisan::call('cache:clear');
    Artisan::call('view:clear');
    Artisan::call('route:clear');
    Artisan::call('clear-compiled');
    Artisan::call('config:cache');
    dd("Cache is cleared");
});
