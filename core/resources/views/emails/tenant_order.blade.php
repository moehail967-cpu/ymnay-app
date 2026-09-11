


 <!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <title>{{__('Payment Success For')}} {{get_static_option('site_title')}}</title>
    <style>
        *{
            font-family: 'Montserrat', sans-serif;
        }
        body {
            background-color: #fdfdfd;
        }
        .mail-container {
            max-width: 650px;
            margin: 50px auto;
            text-align: center;
        }

        .mail-container .logo-wrapper {
            padding: 20px 0 20px;
            border-bottom: 5px solid {{get_static_option('site_color')}};
        }
        table {
            margin: 0 auto;
        }
        table {

            border-collapse: collapse;
            width: 100%;
        }

        table td, table th {
            border: 1px solid rgba(0,0,0,.05);
            padding: 10px 20px;
            background-color: #fafafa;
            text-align: left;
            font-size: 14px;
            text-transform: capitalize;
        }

        table tr:nth-child(even){background-color: #f2f2f2;}

        table tr:hover {background-color: #ddd;}

        table th {
            padding-top: 12px;
            padding-bottom: 12px;
            text-align: left;
            background-color: {{get_static_option('site_color')}};
            color: white;
        }
        footer {
            margin: 20px 0;
            font-size: 14px;
        }
        .main-content-wrap {
            background-color: #fff;
            box-shadow: 0 0 15px 0 rgba(0,0,0,.05);
            padding: 30px;
        }

        .main-content-wrap p {
            margin: 0;
            text-align: left;
            font-size: 14px;
            line-height: 26px;
        }

        .main-content-wrap p:first-child {
            margin-bottom: 10px;
        }

        .main-content-wrap .price-wrap {
            font-size: 60px;
            line-height: 70px;
            font-weight: 600;
            margin: 40px 0;
        }
        table {
            margin-bottom: 30px;
        }
        .logo-wrapper img{
            max-width: 200px;
        }

        .renew_heading{
            font-size: 20px;
        }
    </style>
</head>
<body>
<div class="mail-container">
    @php
        $logoId = get_static_option('site_logo');
        $imageData = get_attachment_image_by_id($logoId, null, false);
        $logoUrl = $imageData['img_url'] ?? null;

        function fetchImageContents($url) {
            if (filter_var($url, FILTER_VALIDATE_URL) === false) {
                return false;
            }

            if (ini_get('allow_url_fopen')) {
                $image = @file_get_contents($url);
                if ($image !== false) {
                    return $image;
                }
            }

            if (function_exists('curl_version')) {
                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

                // SSL bypass - **WARNING: only do this if you trust the URL!**
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

                $image = curl_exec($ch);
                if (curl_errno($ch)) {
                    echo "<p>cURL error: " . curl_error($ch) . "</p>";
                }
                curl_close($ch);
                if ($image !== false) {
                    return $image;
                }
            }

            return false;
        }
        $base64Image = '';
        if ($logoUrl) {
            $imageContents = fetchImageContents($logoUrl);
            if ($imageContents !== false) {
                $ext = strtolower(pathinfo(parse_url($logoUrl, PHP_URL_PATH), PATHINFO_EXTENSION));
                $mimeTypes = [
                    'png' => 'image/png',
                    'jpg' => 'image/jpeg',
                    'jpeg'=> 'image/jpeg',
                    'gif' => 'image/gif',
                    'bmp' => 'image/bmp',
                    'svg' => 'image/svg+xml',
                ];
                $mimeType = $mimeTypes[$ext] ?? 'image/png';
                $base64Image = 'data:' . $mimeType . ';base64,' . base64_encode($imageContents);
            } else {
                echo "<p>Failed to fetch image contents from URL.</p>";
            }
        } else {
            echo "<p>No valid logo URL found.</p>";
        }
    @endphp
    @if($base64Image)
        <img src="{{ $base64Image }}" alt="Site Logo" style="max-width: 200px;">
    @else
        <p><em>Logo image could not be loaded.</em></p>
    @endif
    <div class="main-content-wrap">
        <p>{{__('Hello')}}</p>


            <p>{{__('A payment from')}} {{$package->package_name}} {{__('was successful. Package ID')}} #{{$package->id}} ,
                {{__('package Name')}} "{{$package->package_name ?? ''}}" {{__('Paid Via')}} {{ucfirst(str_replace('_',' ',$package->package_gateway))}}</p>

        <div class="price-wrap">{{amount_with_currency_symbol($package->package_price)}}</div>
        <table>

            <tr>
                <td><strong>{{__('Package Name')}}</strong></td>
                <td>{{$package->package_name}}</td>
            </tr>

            <tr>
                <td><strong>{{__('Amount')}}</strong></td>
                <td>{{amount_with_currency_symbol($package->package_price)}}</td>
            </tr>

            <tr>
                <td><strong>{{__('User Name')}}</strong></td>
                <td>{{$package->name}}</td>
            </tr>

            <tr>
                <td><strong>{{__('User Email')}}</strong></td>
                <td>{{$package->email}}</td>
            </tr>



        </table>
    </div>
    <footer>
        {!! get_footer_copyright_text(\App\Helpers\LanguageHelper::default_slug()) !!}
    </footer>
</div>
</body>
</html>


