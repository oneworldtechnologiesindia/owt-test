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
                    <h3>{{ $dealer->company_name }}</h3>
                    <p>{{ $dealer->street }} {{ $dealer->house_number }}</p>
                    <p>{{ $dealer->zipcode }} {{ $dealer->city }}</p>
                    {{-- <p>{{ $dealer->zipcode }} {{ $dealer->country }}</p> --}}
                    <p>{{ getPDFTranslation('Email', $loggedInUserCountry) }}: <a href="mailto:{{ $dealer->email }}">{{ $dealer->email }}</a></p>
                </td>
                <td class="align-right">
                    <img width="130" height="auto" src="{{ URL::asset('/assets/images/logo-dark.png') }}">
                </td>
            </tr>
        </table>
        <table border="0" cellspacing="0" cellpadding="0" class="title-invoice">
            <tr>
                <td>
                    <h3>{{ getPDFTranslation('AUFTRAGSBESTÄTIGUNG', $loggedInUserCountry) }}</h3>
                </td>
            </tr>
        </table>
        <table border="0" cellspacing="0" cellpadding="0" class="customer-info">
            <tr>
                <td>
                    <h4>{{ getPDFTranslation('Rechnungsadresse', $loggedInUserCountry) }}</h4>
                    <p>{{ $customer->first_name }} {{ $customer->last_name }}</p>
                    <p>{{ $customer->street }} {{ $customer->house_number }}</p>
                    <p>{{ $customer->zipcode }} {{ $customer->city }}</p>
                    <p><a href="mailto:{{ $customer->email }}">{{ $customer->email }}</a></p>
                </td>
                <td class="align-right">
                    <p><strong>{{ getPDFTranslation('Datum', $loggedInUserCountry) }}:</strong>
                        <span>{{ getDateFormateView($orders[0]->created_at) }}</span>
                    </p>
                    <p><strong>{{ getPDFTranslation('Kunden Nr', $loggedInUserCountry) }}.:</strong> <span>{{ $customer->display_id }}</span></p>
                    <p><strong>{{ getPDFTranslation('Auftrag Nr', $loggedInUserCountry) }}.:</strong> <span>{{ $orders[0]->invoice_number }}</span></p>
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
                <?php $currency = !empty($orders[0]->currency) ? $orders[0]->currency : 'eur'; ?>
                @foreach ($orders as $order)
                    <tr>
                        <td>{{ $order->brand_name }}: {{ $order->product_name }}</td>
                        <td class="text-center">{{ $order->qty }}</td>
                        <td class="text-center">
                            {{ formatCurrencyOutput($order->offer_amount, $order->currency, true) }}
                        </td>
                        <td class="text-right">
                            {{ formatCurrencyOutput($order->qty * $order->offer_amount, $currency, true) }}

                            {{-- {{ number_format((float) ($order->qty * $order->offer_amount), 2, '.', '') }} --}}
                        </td>
                    </tr>
                @endforeach
                @php($tablerow = count($orders))
                @for ($i = $tablerow; $i <= 10; $i++)
                    <tr>
                        <td></td>
                        <td class="text-center"></td>
                        <td class="text-center"></td>
                        <td class="text-right">{{ formatCurrencyOutput(00, $currency, true) }}</td>
                    </tr>
                @endfor
            </tbody>
            @php($taxpercentage = $orders[0]->vat_rate)
            @php($netValue = $orders[0]->amount + ($taxpercentage / 100) * $orders[0]->offer_amount)
            @php($taxvalue = $orders[0]->vat_amount)
            <tfoot>
                <tr>
                    <td colspan="2"></td>
                    <td class="text-right"><strong>{{ getPDFTranslation('Gesamt', $loggedInUserCountry) }}</strong></td>
                    <td class="text-right">
                        {{ formatCurrencyOutput($orders[0]->amount, $currency, true) }}
                        {{-- {{ number_format((float) $orders[0]->amount, 2, '.', '') }} --}}
                    </td>
                </tr>
                <tr>
                    <td colspan="2"></td>
                    <td class="text-right"><strong>{{ getPDFTranslation('Nettobetrag', $loggedInUserCountry) }}</strong></td>
                    <td class="text-right">
                        {{ formatCurrencyOutput($netValue, $currency, true) }}
                        {{-- {{ number_format((float) $netValue, 2, '.', '') }} --}}
                    </td>
                </tr>
                <tr>
                    <td colspan="2"></td>
                    <td class="text-right"><strong>{{ getPDFTranslation('MwSt', $loggedInUserCountry) }}.</strong></td>
                    <td class="text-right">{{ number_format((float) $taxpercentage, 2, '.', '') }}%</td>
                </tr>
            </tfoot>
        </table>
        <table class="special-invoice-table" border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td colspan="2" style="width: 65%;"></td>
                <td class="text-right" style="width: 15%;"><strong>{{ getPDFTranslation('zzgl. MwSt.', $loggedInUserCountry) }}</strong></td>
                <td class="text-right" style="width: 20%;">
                    {{ formatCurrencyOutput($taxvalue, $currency, true) }}
                    {{-- {{ number_format((float) $taxvalue, 2, '.', '') }} --}}
                </td>
            </tr>
        </table>
        <table class="invoice-total" border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td colspan="2" style="width: 68%;">{{ getPDFTranslation('Herzlichen Dank für die Nutzung von HiFi Quest', $loggedInUserCountry) }}</td>
                <td class="text-right" style="width: 12%;"><strong>{{ getPDFTranslation('Rechnungsbetrag', $loggedInUserCountry) }}</strong></td>
                <td class="text-right" style="width: 20%;">
                    {{-- {{ number_format((float) $orders[0]->amount, 2, '.', '') }} --}}
                    {{ formatCurrencyOutput($orders[0]->amount + $taxvalue, $currency, true) }}
                </td>
            </tr>
        </table>
        <div class="invoice-bottom-div">
            {{-- <table class="invoice-bottom" border="0" cellspacing="0" cellpadding="0">
                <tr style="vertical-align: top;">
                    <td style="width: 20%">
                        <p>@lang('translation.CPS Networks GbR')</p>
                        <p>@lang('translation.Mooslängstr')</p>
                        <p>@lang('translation.Puchheim')</p>
                    </td>
                    <td style="width: 30%">
                        <p>@lang('translation.Email'): {{ env('APP_CONTACT_EMAIL', 'contact@HiFi-Quest.com') }}</p>
                        <p>@lang('translation.Internet'): {{ env('APP_WEBSITE_URL', 'www.HiFi-Quest.com') }}</p>
                    </td>
                    <td style="width: 27%">
                        <p>@lang('translation.Solarisbank AG')</p>
                        <p>@lang('translation.invoice_iban')</p>
                        <p>@lang('translation.invoice_bic')</p>
                        <p>@lang('translation.invoice_KTO_Inh')</p>
                    </td>
                    <td style="width: 23%">
                        <p>@lang('translation.invoice_ust')</p>
                        <p>@lang('translation.Finanzamt FFB')</p>
                    </td>
                </tr>
            </table> --}}
            <table class="invoice-bottom" border="0" cellspacing="0" cellpadding="0">
                <tr style="vertical-align: top;">
                    <td style="width: 20%">
                        <p>{{ $dealer->company_name }}</p>
                        <p>{{ $dealer->street }} {{ $dealer->house_number }}</p>
                        <p>{{ $dealer->zipcode }} {{ $dealer->city }}</p>
                    </td>
                    <td style="width: 30%">
                        <p>{{ getPDFTranslation('Email', $loggedInUserCountry) }}: {{ $dealer->email }}</p>
                        {{-- <p>@lang('translation.Internet'): {{$dealer->}}</p> --}}
                    </td>
                    <td style="width: 27%">
                        <p>{{ $dealer->bank_name }}</p>
                        <p>IBAN: {{ $dealer->iban }}</p>
                        <p>BIC: {{ $dealer->bic }}</p>
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
