<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Invoice</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            text-indent: 0;
            font-family: "Trebuchet MS", sans-serif;
        }

        body {
            padding: 60px 80px;
            font-family: "Trebuchet MS", sans-serif;
        }

        p {
            font-size: 15px;
            line-height: 24px;
            font-weight: normal;
        }

        h3 {
            font-size: 17px;
            line-height: 24px;
        }

        .align-right {
            text-align: right;
        }

        table {
            width: 100%
        }

        table.title-invoice {
            border-width: 1px 0;
            border-color: #78349D;
            margin: 60px auto 40px auto;
            text-align: center
        }

        table.customer-info {
            font-size: 14px;
            line-height: 24px;
        }

        .align-right p strong {
            width: 150px;
            display: inline-block;
            text-align: left;
        }

        .align-right p span {
            width: 50px;
            display: inline-block;
            text-align: right;
        }

        table.invoice-body-main {
            margin-top: 35px;
        }

        table.invoice-body-main thead td,
        table.invoice-body-main thead tr,
        table.invoice-body-main tbody td,
        table.invoice-body-main tbody tr {
            border-color: #999999;
            border-style: solid;
            border-width: 2px;
            font-size: 12px;
        }

        table.invoice-body-main tfoot tr td:last-child {
            border-color: #999999;
            border-style: solid;
            border-width: 0 0 2px 0;
        }

        table.invoice-body-main tfoot tr td,
        table.special-invoice-table tr td {
            font-size: 12px;
            padding: 2px 0;
        }

        table.invoice-body-main thead tr th {
            background: #C4E5EA;
            border-collapse: collapse;
            font-size: 12px;
        }

        table.invoice-body-main tbody tr:nth-child(even) {
            background: #F2F2F2;
        }

        table.invoice-body-main thead tr th {
            padding: 2px 0;
            font-weight: bold;
        }

        table.invoice-body-main tbody tr td {
            padding: 2px 2px;
        }

        table.invoice-body-main tbody td:first-child {
            width: 40%
        }

        table.invoice-body-main tbody td:last-child {
            width: 20%
        }

        table.invoice-body-main tbody td:nth-child(3) {
            width: 30%
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        table.special-invoice-table tr td {
            border-color: #000;
            border-style: solid;
            border-width: 0 0 2px 0;
        }

        table.special-invoice-table tr td:first-child {
            border-color: #fff;
        }

        table.invoice-total tr td strong {
            font-size: 15px;
        }

        table.invoice-total tr td {
            padding: 10px 0;
        }

        table.invoice-total tr td:first-child {
            font-size: 15px;
        }

        table.invoice-body-main tfoot tr td strong {
            font-size: 11px;
        }

        table.invoice-total tr td:last-child {
            background: #C4E5EA;
            border-color: #000;
            border-style: solid;
            border-width: 0 0 2px 0;
            font-weight: bold;
        }

        div.invoice-bottom-div {
            border-width: 2px 0 0 0;
            border-color: #78349D;
            padding-top: 20px;
            margin-top: 80px;
            border-style: solid;
        }

        table.invoice-bottom tr p {
            font-size: 9px;
            line-height: 18px;
        }
    </style>
</head>

<body>
    <div class="invoice-container">
        <table border="0" cellspacing="0" cellpadding="0" class="dealer-info">
            <tr style="vertical-align: top;">
                <td>
                    <h3>{{ getPDFTranslation('CPS Networks', $loggedInUserCountry) }}</h3>
                    <p>Mooslängstr.1</p>
                    <p>82178 Puchheim</p>
                    <p>{{ getPDFTranslation('Website', $loggedInUserCountry) }}: www.HiFi-Quest.com</p>
                    <p>{{ getPDFTranslation('E-Mail', $loggedInUserCountry) }}: contact@HiFi-Quest.com</p>
                </td>
                <td class="align-right">
                    <img width="130" height="auto" src="{{URL::asset('/assets/images/logo-dark.png')}}">
                </td>
            </tr>
        </table>
        <table border="0" cellspacing="0" cellpadding="0" class="title-invoice">
            <tr>
                <td>
                    <h3>{{ getPDFTranslation('RECHNUNG', $loggedInUserCountry) }}</h3>
                </td>
            </tr>
        </table>
        <table border="0" cellspacing="0" cellpadding="0" class="customer-info">
            <tr>
                <td>
                    <h4>{{ getPDFTranslation('Rechnungsadresse', $loggedInUserCountry) }}</h4>
                    <p>{{ $dealer->first_name }} {{ $dealer->last_name }}</p>
                    <p>{{ $dealer->street }} {{ $dealer->house_number }}</p>
                    <p>{{ $dealer->zipcode }} {{ $loggedInUserCountry }}</p>
                    <p><a href="mailto:{{ $dealer->email }}">{{ $dealer->email }}</a></p>
                </td>
                <td class="align-right">
                    <p><strong>{{ getPDFTranslation('Datum', $loggedInUserCountry) }}:</strong>
                        <span>{{ getDateFormateView(date('Y-m-d')) }}</span>
                    </p>
                    <p><strong>{{ getPDFTranslation('Händler-ID', $loggedInUserCountry) }}:</strong> <span>{{ $dealer->display_id }}</span></p>
                    <p><strong>{{ getPDFTranslation('Rechnung Nr', $loggedInUserCountry) }}.:</strong> <span>{{ $s_log->inv_number }}</span></p>
                </td>
            </tr>
        </table>
        <table cellspacing="0" class="invoice-body-main">
            <thead>
                <tr>
                    <th>{{ getPDFTranslation('Bezeichnung', $loggedInUserCountry) }}</th>
                    <th>{{ getPDFTranslation('Menge', $loggedInUserCountry) }}</th>
                    <th>{{ getPDFTranslation('Einzelpreis', $loggedInUserCountry) }}</th>
                    <th>{{ getPDFTranslation('Gesamtpreis', $loggedInUserCountry) }}</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $currency = '€';
                if ($package->plan_currency == "2") {
                    $currency = "$";
                }
                $package_charge=$package->price;
                if($s_log->refundable_amount){
                    $package_charge=$package->price - $s_log->refundable_amount;
                }
                ?>

                <tr>
                    <td>{{ getPDFTranslation('HiFi Quest Nutzungsgebühr', $loggedInUserCountry) }}</td>
                    <td class="text-center">1</td>
                    <td class="text-center">{{ formatCurrencyOutput($package_charge,$package->plan_currency,true) }} </td>
                    <td class="text-right">
                        {{formatCurrencyOutput($package_charge,$package->plan_currency,true)}}
                </tr>
                <?php
                $netValue = $package_charge;
                ?>
                @foreach ($orders as $order)
                    <tr>
                        <td>{{ getPDFTranslation('Invoice', $loggedInUserCountry) }}: {{ $order->invoice_number }}</td>
                        <td class="text-center">{{ $order->qty }}</td>
                        <td class="text-center">{{formatCurrencyOutput($order->site_commission,$package->plan_currency,true)}}</td>
                        <td class="text-right">
                            {{formatCurrencyOutput($order->site_commission,$package->plan_currency,true)}}
                            <?php
                             $netValue+=$order->site_commission
                            ?>
                        </td>
                    </tr>
                @endforeach
                @php($tablerow = count($orders))
                @for ($i = $tablerow; $i <= 10; $i++)
                    <tr>
                        <td></td>
                        <td class="text-center"></td>
                        <td class="text-center"></td>
                        <td class="text-right">{{formatCurrencyOutput(00,$package->plan_currency,true)}}</td>
                    </tr>
                @endfor
            </tbody>
            @if($package->plan_currency==1)
                <tfoot>
                    @php($taxpercentage = 19.00);
                    @php($amount_without_tax = $netValue - ($taxpercentage / 100) * $netValue)
                    @php($taxvalue = $netValue - $amount_without_tax)
                    @php($tax_amount = $s_log->tax ?? 0)
                    <tr>
                        <td colspan="2"></td>
                        <td class="text-right"><strong>{{ getPDFTranslation('Gesamt', $loggedInUserCountry) }}</strong></td>
                        <td class="text-right">{{formatCurrencyOutput($amount_without_tax,$package->plan_currency,true)}}</td>
                    </tr>
                    {{-- <tr>
                        <td colspan="2"></td>
                        <td class="text-right"><strong>@lang('translation.MwSt')</strong></td>
                        <td class="text-right">{{formatCurrencyOutput($taxvalue,$package->plan_currency,true)}}</td>
                    </tr> --}}
                    @if($tax_amount)
                        <tr>
                            <td colspan="2"></td>
                            <td class="text-right"><strong>{{ getPDFTranslation('MwSt', $loggedInUserCountry) }}</strong></td>
                            <td class="text-right">{{formatCurrencyOutput($tax_amount,$package->plan_currency,true)}}</td>
                        </tr>
                    @endif
                    <tr>
                        <td colspan="2"></td>
                        <td class="text-right"><strong>{{ getPDFTranslation('Nettobetrag', $loggedInUserCountry) }}</strong></td>
                        <td class="text-right">
                            {{formatCurrencyOutput($netValue,$package->plan_currency,true)}}
                        </td>
                    </tr>
                    {{-- <tr>
                        <td colspan="2"></td>
                        <td class="text-right"><strong>@lang('translation.MwSt').</strong></td>
                        <td class="text-right">{{ number_format((float) $taxpercentage, 2, '.', '') }}%</td>
                    </tr> --}}
                </tfoot>
            @else
                @php($tax_amount = $s_log->tax ?? 0)
                <tfoot>
                <tr>
                    <td colspan="2"></td>
                    <td class="text-right"><strong>{{ getPDFTranslation('Gesamt', $loggedInUserCountry) }}</strong></td>
                    <td class="text-right">{{formatCurrencyOutput($netValue,$package->plan_currency,true)}}</td>
                </tr>
                @if($tax_amount)
                    <tr>
                        <td colspan="2"></td>
                        <td class="text-right"><strong>{{ getPDFTranslation('MwSt', $loggedInUserCountry) }}</strong></td>
                        <td class="text-right">{{formatCurrencyOutput($tax_amount,$package->plan_currency,true)}}</td>
                    </tr>
                @endif
                <tr>
                    <td colspan="2"></td>
                    <td class="text-right"><strong>{{ getPDFTranslation('Nettobetrag', $loggedInUserCountry) }}</strong></td>
                    <td class="text-right">
                        {{formatCurrencyOutput($netValue + $tax_amount,$package->plan_currency,true)}}
                    </td>
                </tr>
            </tfoot>
            @endif

        </table>
        {{-- <table class="special-invoice-table" border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td colspan="2" style="width: 65%;"></td>
                <td class="text-right" style="width: 15%;"><strong>@lang('translation.zzgl. MwSt.')</strong></td>
                <td class="text-right" style="width: 20%;">{{ number_format((float) $taxvalue, 2, '.', '') }}</td>
            </tr>
        </table> --}}
        <table class="invoice-total" border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td colspan="2" style="width: 68%;">{{ getPDFTranslation('Herzlichen Dank für die Nutzung von HiFi Quest', $loggedInUserCountry) }}</td>
                <td class="text-right" style="width: 12%;"><strong>{{ getPDFTranslation('Rechnungsbetrag', $loggedInUserCountry) }}</strong></td>
                <td class="text-right" style="width: 20%;">
                    {{formatCurrencyOutput($netValue + $tax_amount,$package->plan_currency,true)}}
                </td>
            </tr>
        </table>
        <div class="invoice-bottom-div">
            <table class="invoice-bottom" border="0" cellspacing="0" cellpadding="0">
                <tr style="vertical-align: top;">
                    <td style="width: 20%">
                        <p>{{ getPDFTranslation('CPS Networks GbR', $loggedInUserCountry) }}</p>
                        <p>{{ getPDFTranslation('Mooslängstr', $loggedInUserCountry) }}</p>
                        <p>{{ getPDFTranslation('Puchheim', $loggedInUserCountry) }}</p>
                    </td>
                    <td style="width: 30%">
                        <p>{{ getPDFTranslation('Email', $loggedInUserCountry) }}: {{ env('APP_CONTACT_EMAIL', 'contact@HiFi-Quest.com') }}</p>
                        <p>{{ getPDFTranslation('Internet', $loggedInUserCountry) }}: {{ env('APP_WEBSITE_URL', 'www.HiFi-Quest.com') }}</p>
                    </td>
                    <td style="width: 27%">
                        <p>{{ getPDFTranslation('Solarisbank AG', $loggedInUserCountry) }}</p>
                        <p>{{ getPDFTranslation('invoice_iban', $loggedInUserCountry) }}</p>
                        <p>{{ getPDFTranslation('invoice_bic', $loggedInUserCountry) }}</p>
                        <p>{{ getPDFTranslation('invoice_KTO_Inh', $loggedInUserCountry) }}</p>
                    </td>
                    <td style="width: 23%">
                        <p>{{ getPDFTranslation('invoice_ust', $loggedInUserCountry) }}</p>
                        {{-- <p>@lang('translation.Finanzamt FFB')</p> --}}
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>

</html>
