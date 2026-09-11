<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ __('Product Import Report') }} - {{ get_static_option('site_title') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            font-family: 'Open Sans', sans-serif;
            box-sizing: border-box;
        }
        .mail-container {
            max-width: 650px;
            margin: 0 auto;
            text-align: center;
            background-color: #f2f2f2;
            padding: 40px 0;
        }
        .logo-wrapper img {
            max-width: 200px;
        }
        .inner-wrap {
            background-color: #fff;
            margin: 20px 40px;
            padding: 30px 25px;
            text-align: left;
            box-shadow: 0 0 20px 0 rgba(0,0,0,0.01);
        }
        .inner-wrap p {
            font-size: 14px;
            line-height: 24px;
            color: #656565;
            margin: 0 0 10px;
        }
        h2 {
            font-size: 20px;
            margin: 0 0 5px;
            color: #333;
        }
        .status-badge {
            display: inline-block;
            padding: 6px 16px;
            border-radius: 4px;
            font-size: 13px;
            font-weight: 600;
            text-transform: uppercase;
            margin-bottom: 20px;
        }
        .status-success {
            background-color: #d4edda;
            color: #155724;
        }
        .status-partial {
            background-color: #fff3cd;
            color: #856404;
        }
        .status-failed, .status-error {
            background-color: #f8d7da;
            color: #721c24;
        }
        .stats-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .stats-table td {
            padding: 10px 15px;
            font-size: 14px;
            border-bottom: 1px solid #eee;
        }
        .stats-table td:first-child {
            color: #555;
            font-weight: 600;
            width: 60%;
        }
        .stats-table td:last-child {
            text-align: right;
            font-weight: 700;
            color: #333;
        }
        .stats-table tr:last-child td {
            border-bottom: none;
        }
        .count-success { color: #28a745 !important; }
        .count-failed { color: #dc3545 !important; }
        .count-skipped { color: #ffc107 !important; }
        .failure-section {
            margin-top: 25px;
        }
        .failure-section h3 {
            font-size: 16px;
            color: #333;
            margin: 0 0 10px;
            padding-bottom: 8px;
            border-bottom: 2px solid #f0f0f0;
        }
        .failure-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 13px;
        }
        .failure-table th {
            background-color: #f8f9fa;
            color: #333;
            padding: 8px 12px;
            text-align: left;
            border-bottom: 2px solid #dee2e6;
            font-weight: 600;
        }
        .failure-table td {
            padding: 8px 12px;
            border-bottom: 1px solid #eee;
            color: #555;
            vertical-align: top;
        }
        .failure-table tr:nth-child(even) {
            background-color: #fafafa;
        }
        .failure-table .row-num {
            font-weight: 600;
            color: #dc3545;
            white-space: nowrap;
            width: 60px;
        }
        .more-errors {
            background-color: #fff3cd;
            padding: 10px 15px;
            border-radius: 4px;
            font-size: 13px;
            color: #856404;
            margin-top: 10px;
            text-align: center;
        }
        footer {
            margin-top: 10px;
            font-size: 13px;
            color: #999;
        }
    </style>
</head>
<body>
<div class="mail-container">
    <div class="logo-wrapper">
        <a href="{{ url('/') }}">
            @php
                $site_logo = get_static_option('site_logo');
                $image = get_attachment_image_by_id($site_logo);
                $image_url = !empty($image) ? $image['img_url'] : '';
            @endphp
            <img src="{{ $image_url }}" alt="logo" style="width: 200px; height: auto">
        </a>
    </div>

    <div class="inner-wrap">
        <h2>{{ __('Product Import Report') }}</h2>

        @if($report['status'] === 'success')
            <span class="status-badge status-success">{{ __('Completed Successfully') }}</span>
        @elseif($report['status'] === 'partial')
            <span class="status-badge status-partial">{{ __('Completed with Errors') }}</span>
        @elseif($report['status'] === 'error')
            <span class="status-badge status-error">{{ __('Import Error') }}</span>
        @else
            <span class="status-badge status-failed">{{ __('Import Failed') }}</span>
        @endif

        <p>{{ $report['message'] }}</p>

        @if($report['total_rows'] > 0)
            <table class="stats-table">
                <tr>
                    <td>{{ __('Total Rows Processed') }}</td>
                    <td>{{ $report['total_rows'] }}</td>
                </tr>
                <tr>
                    <td>{{ __('Successfully Imported') }}</td>
                    <td class="count-success">{{ $report['imported'] }}</td>
                </tr>
                <tr>
                    <td>{{ __('Failed') }}</td>
                    <td class="count-failed">{{ $report['failed'] }}</td>
                </tr>
                <tr>
                    <td>{{ __('Skipped') }}</td>
                    <td class="count-skipped">{{ $report['skipped'] }}</td>
                </tr>
            </table>
        @endif

        @if(!empty($report['reasons']))
            <div class="failure-section">
                <h3>{{ __('Failure Details') }}</h3>
                <table class="failure-table">
                    <thead>
                        <tr>
                            <th>{{ __('Row') }}</th>
                            <th>{{ __('Reason') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach(array_slice($report['reasons'], 0, 50) as $failure)
                            <tr>
                                <td class="row-num">
                                    @if($failure['row'] > 0)
                                        #{{ $failure['row'] }}
                                    @else
                                        --
                                    @endif
                                </td>
                                <td>{{ $failure['reason'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                @if(count($report['reasons']) > 50)
                    <div class="more-errors">
                        {{ __('... and') }} {{ count($report['reasons']) - 50 }} {{ __('more errors. Please check the application logs for full details.') }}
                    </div>
                @endif
            </div>
        @endif

        <p style="margin-top: 25px; font-size: 12px; color: #999;">
            {{ __('This is an automated report generated at') }} {{ now()->format('Y-m-d H:i:s') }}
        </p>
    </div>

    <footer>
        {!! get_footer_copyright_text() !!}
    </footer>
</div>
</body>
</html>
