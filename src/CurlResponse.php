<?php
/**
 * Класс curl ответа.
 * @package evas-php\evas-http
 * @author Egor Vasyakin <egor@evas-php.com>
 */
namespace Evas\Http;

use Evas\Http\CurlRequest;
use Evas\Http\HttpException;
use Evas\Http\Traits\HttpBodyTrait;
use Evas\Http\Traits\HttpCookiesTrait;
use Evas\Http\Traits\HttpHeadersTrait;

class CurlResponse
{
    /**
     * Подключаем расширение тела http.
     * Подключаем расширение заголовков http.
     * Подключаем расширение маппинга cookies http.
     */
    use HttpBodyTrait {
        withAddedBody as protected;
        withBodyJson as protected;
    }
    use HttpCookiesTrait;
    use HttpHeadersTrait;

    /** @var int код ответа */
    public $code;

    /**
     * Получение кода ответа.
     * @return int
     */
    public function getCode(): int
    {
        return $this->code;
    }

    /**
     * Конструктор.
     * @param CurlRequest
     * @throws HttpException
     */
    public function __construct(CurlRequest &$request)
    {
        $ch = $request->prepareSend()->getCh();
        try {
            $body = curl_exec($ch);
            if (curl_error($ch)) {
                throw new HttpException(curl_error($ch));
            }
        } catch (\Exception $e) {
            throw new HttpException($e->getMessage());
        }
        // $responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $responseCode = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
        $headers = curl_getinfo($ch, CURLINFO_HEADER_OUT);

        $headers = static::parseHeadersString($headers);
        $this->code = $responseCode;
        $this->withBody($body);
        $this->withHeaders($headers);
        $this->withHeader('Content-Type', $contentType);
        $cookies = $this->getHeader('Cookie');
        if (!empty($cookies)) {
            $cookies = static::parseCookieHeaderLine($cookies);
            $this->withCookies($cookies);
        }
    }

    /**
     * Парсинг строки заголовоков http.
     * @param string строка заголовков
     * @return array маппинг заголовков http
     */
    public static function parseHeadersString(string $headersString): array
    {
        $headersString = str_replace("\r", '', $headersString);
        $headersList = explode("\n", $headersString);
        array_shift($headersList);
        $headers = [];
        foreach ($headersList as $line) {
            $colonPos = strpos($line, ':');
            $name = trim(substr($line, 0, $colonPos));
            $value = trim(substr($line, $colonPos + 1));
            if (!empty($name)) $headers[$name] = $value;
        }
        return $headers;
    }

    /**
     * Парсинг заголовка cookie.
     * @param string строка cookie
     * @return маппинг cookies
     */
    public static function parseCookieHeaderLine(string $cookieHeader): array
    {
        $items = explode(';', $cookieHeader);
        $cookies = [];
        foreach ($items as &$item) {
            $equalPos = strpos($item, '=');
            $name = trim(substr($item, 0, $equalPos));
            $value = trim(substr($item, $equalPos + 1));
            if (!empty($name)) {
                $cookies[$name] = $value;
            }
        }
        return $cookies;
    }
}
