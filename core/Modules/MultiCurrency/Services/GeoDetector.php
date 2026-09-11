<?php

namespace Modules\MultiCurrency\Services;

use GeoIp2\Database\Reader;
use GeoIp2\Exception\AddressNotFoundException;
use Throwable;

class GeoDetector
{
    private string $dbPath;

    // ISO 3166-1 alpha-2 country → ISO 4217 currency code
    private array $map = [
        // North America
        'US' => 'USD', 'CA' => 'CAD', 'MX' => 'MXN',

        // Central & South America
        'BR' => 'BRL', 'AR' => 'ARS', 'CO' => 'COP', 'CL' => 'CLP',
        'PE' => 'PEN', 'EC' => 'USD', 'BO' => 'BOB', 'PY' => 'PYG',
        'UY' => 'UYU', 'VE' => 'VES', 'CR' => 'CRC', 'GT' => 'GTQ',
        'DO' => 'DOP', 'PA' => 'PAB', 'HN' => 'HNL', 'NI' => 'NIO',

        // Eurozone
        'DE' => 'EUR', 'FR' => 'EUR', 'IT' => 'EUR', 'ES' => 'EUR',
        'NL' => 'EUR', 'BE' => 'EUR', 'AT' => 'EUR', 'PT' => 'EUR',
        'FI' => 'EUR', 'IE' => 'EUR', 'GR' => 'EUR', 'LU' => 'EUR',
        'SK' => 'EUR', 'SI' => 'EUR', 'EE' => 'EUR', 'LV' => 'EUR',
        'LT' => 'EUR', 'MT' => 'EUR', 'CY' => 'EUR', 'HR' => 'EUR',

        // Europe — non-euro
        'GB' => 'GBP', 'CH' => 'CHF', 'SE' => 'SEK', 'DK' => 'DKK',
        'NO' => 'NOK', 'PL' => 'PLN', 'HU' => 'HUF', 'CZ' => 'CZK',
        'RO' => 'RON', 'BG' => 'BGN', 'RS' => 'RSD', 'UA' => 'UAH',
        'TR' => 'TRY', 'IS' => 'ISK', 'AL' => 'ALL', 'MK' => 'MKD',
        'BA' => 'BAM', 'MD' => 'MDL', 'GE' => 'GEL', 'AM' => 'AMD',
        'AZ' => 'AZN', 'BY' => 'BYN',

        // South Asia
        'IN' => 'INR', 'BD' => 'BDT', 'PK' => 'PKR', 'LK' => 'LKR',
        'NP' => 'NPR', 'MV' => 'MVR', 'BT' => 'BTN', 'AF' => 'AFN',

        // Southeast Asia
        'SG' => 'SGD', 'MY' => 'MYR', 'ID' => 'IDR', 'PH' => 'PHP',
        'TH' => 'THB', 'VN' => 'VND', 'MM' => 'MMK', 'KH' => 'KHR',
        'LA' => 'LAK', 'BN' => 'BND',

        // East Asia
        'CN' => 'CNY', 'JP' => 'JPY', 'KR' => 'KRW', 'HK' => 'HKD',
        'TW' => 'TWD', 'MO' => 'MOP', 'MN' => 'MNT',

        // Middle East & Gulf
        'AE' => 'AED', 'SA' => 'SAR', 'QA' => 'QAR', 'KW' => 'KWD',
        'BH' => 'BHD', 'OM' => 'OMR', 'JO' => 'JOD', 'EG' => 'EGP',
        'IL' => 'ILS', 'IQ' => 'IQD', 'LB' => 'LBP', 'YE' => 'YER',
        'PS' => 'ILS', 'SY' => 'SYP',

        // Central Asia
        'KZ' => 'KZT', 'UZ' => 'UZS', 'TM' => 'TMT', 'TJ' => 'TJS',
        'KG' => 'KGS',

        // Russia
        'RU' => 'RUB',

        // Africa
        'ZA' => 'ZAR', 'NG' => 'NGN', 'KE' => 'KES', 'GH' => 'GHS',
        'ET' => 'ETB', 'TZ' => 'TZS', 'UG' => 'UGX', 'MA' => 'MAD',
        'TN' => 'TND', 'DZ' => 'DZD', 'EG' => 'EGP', 'SD' => 'SDG',
        'AO' => 'AOA', 'CM' => 'XAF', 'CI' => 'XOF', 'SN' => 'XOF',
        'ZM' => 'ZMW', 'ZW' => 'ZWL', 'MZ' => 'MZN', 'RW' => 'RWF',

        // Oceania
        'AU' => 'AUD', 'NZ' => 'NZD', 'FJ' => 'FJD', 'PG' => 'PGK',
        'WS' => 'WST', 'TO' => 'TOP', 'VU' => 'VUV',
    ];

    public function __construct(string $dbPath = '')
    {
        $this->dbPath = $dbPath ?: storage_path('app/geoip/GeoLite2-Country.mmdb');
    }

    public function isAvailable(): bool
    {
        return file_exists($this->dbPath) && is_readable($this->dbPath);
    }

    public function detect(string $ip): ?string
    {
        if (!$this->isAvailable()) {
            return null;
        }

        try {
            $reader  = new Reader($this->dbPath);
            $record  = $reader->country($ip);
            $country = $record->country->isoCode;
            $reader->close();

            return isset($country, $this->map[$country]) ? $this->map[$country] : null;
        } catch (AddressNotFoundException) {
            return null;
        } catch (Throwable) {
            return null;
        }
    }

    public function dbPath(): string
    {
        return $this->dbPath;
    }
}
