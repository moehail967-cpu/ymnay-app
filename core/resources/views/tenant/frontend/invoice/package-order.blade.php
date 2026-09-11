<!doctype html>
<html lang="{{ app()->getLocale() }}" dir="{{ function_exists('get_lang_direction') ? get_lang_direction() : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <title>{{__('Package Invoice')}}</title>
    <style>

        body * {
            font-family: "dejavusans", "freeserif", sans-serif;
        }
        @if(function_exists('get_lang_direction') && get_lang_direction() == 'rtl')
        body { direction: rtl; text-align: right; }
        td, th { text-align: right; }
        @endif
        table, td, th {
            border: 1px solid #ddd;
            text-align: left;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        th, td {
            padding: 15px;
        }

        #pdf_content_wrapper {
            max-width: 1000px;
        }

        .cart-total-table-wrap .title {
            font-size: 25px;
            margin-bottom: 20px;
        }

        .billing-wrap ul {
            margin: 0;
            padding: 0;
            list-style: none;
        }
    </style>
</head>
<body>
<div id="pdf_content_wrapper">
    <div class="cart-table-wrapper cart-wrapper">
        @php
            $logoId = get_static_option('site_logo');
            $imageData = get_attachment_image_by_id($logoId, null, false);
            $logoUrl = $imageData['img_url'] ?? null;
            $base64Image = '';
            if ($logoUrl) {
                $imageContents = false;
                if (ini_get('allow_url_fopen')) {
                    $imageContents = @file_get_contents($logoUrl);
                }
                if ($imageContents === false && function_exists('curl_version')) {
                    $ch = curl_init($logoUrl);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                    $imageContents = curl_exec($ch);
                    curl_close($ch);
                }
                if ($imageContents !== false) {
                    $ext = strtolower(pathinfo(parse_url($logoUrl, PHP_URL_PATH), PATHINFO_EXTENSION));
                    $mimeTypes = ['png' => 'image/png', 'jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'gif' => 'image/gif', 'bmp' => 'image/bmp', 'svg' => 'image/svg+xml'];
                    $mimeType = $mimeTypes[$ext] ?? 'image/png';
                    $base64Image = 'data:' . $mimeType . ';base64,' . base64_encode($imageContents);
                }
            }
        @endphp
        @if($base64Image)
            <img src="{{ $base64Image }}" alt="Site Logo" style="max-width: 200px;">
        @endif

        <div class="package-info-wrap">
            @if(!empty($payment_details))
                <h2 class="main_title">{{__('Package Information')}}</h2>
                <ul>
                    <li><strong>{{__('Order ID: ')}}</strong> #{{$payment_details->id ?? ''}}</li>
                    <li><strong>{{__('Order Date: ')}}</strong> {{date_format($payment_details->created_at,'d M Y')}}</li>
                    <li><strong>{{__('Package Name: ')}}</strong> {{$payment_details->package_name ?? ''}}</li>
                    <li><strong>{{__('Package Price: ')}}</strong> {{$payment_details->package_price ?? ''}}</li>
                </ul>
            @endif
        </div>
    </div>

    <div class="cart-total-table-wrap">
        <h2 class="title">{{__('Billing Summary')}}</h2>
        <div class="cart-total-table">
            <table class="table table-bordered">
                <tbody>
                <tr>
                    <th>{{__('Billing Name')}}</th>
                    <td>{{$payment_details->name ?? ''}}</td>
                </tr>
                <tr>
                    <th>{{__('Billing Email')}}</th>
                    <td>{{$payment_details->email ?? ''}}</td>
                </tr>
                <tr>
                    <th>{{__('Total')}}</th>
                    <td>{{$payment_details->package_price ?? ''}}</td>
                </tr>
                <tr>
                    <th>{{__('Package Start Date')}}</th>
                    <td>{{$payment_details->start_date ?? ''}}</td>
                </tr>
                <tr>
                    <th>{{__('Package Expire Date')}}</th>
                    <td>{{$payment_details->expire_date ?? ''}}</td>
                </tr>
                <tr>
                    <th>{{__('Payment Gateway')}}</th>
                    <td>{{str_replace('_', ' ', $payment_details->package_gateway ?? '')}}</td>
                </tr>
                <tr>
                    <th>{{__('Payment Status')}}</th>
                    <td>{{$payment_details->payment_status ?? ''}}</td>
                </tr>
                <tr>
                    <th>{{__('Transaction ID')}}</th>
                    <td>{{$payment_details->transaction_id ?? ''}}</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>
