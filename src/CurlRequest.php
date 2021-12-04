<?php
/**
 * Класс curl запроса.
 * @package evas-php\evas-http
 * @author Egor Vasyakin <egor@evas-php.com>
 */
namespace Evas\Http;

use Evas\Http\CurlResponse;
use Evas\Http\HttpRequest;

class CurlRequest extends HttpRequest
{
    /** @var resource */
    protected $ch;

    /**
     * Получение ресурса curl-запроса.
     * @return resource
     */
    public function getCh()
    {
        if (empty($this->ch)) {
            $this->ch = curl_init();
            curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($this->ch, CURLINFO_HEADER_OUT , true);
        }
        return $this->ch;
    }

    /**
     * Преобразование данных в query-чать uri.
     * @param array|object|null данные
     * @return string
     */
    public static function prepareDataToUriQuery($data = null): string
    {
        if (empty($data)) return '';
        assert(is_array($data) || is_object($data));
        $parts = [];
        foreach ($data as $key => $value) {
            $parts[] = urlencode($key) .'='. urlencode($value);
        }
        return '?' . implode('&', $parts);
    }

    /**
     * Подготовка curl запроса к отправке.
     * @return self
     */
    public function prepareSend(): CurlRequest
    {
        $ch = $this->getCh();
        $method = $this->getMethod();
        $uri = $this->getUri();

        // добавляем данные запроса
        if ('GET' !== $method) {
            // $type = $this->getHeader('Content-Type');
            // if (false !== strpos($type, 'application/json')) {
            //     $this->withBodyJson($this->getBody());
            // }
            $body = $this->getBody();

            if ('POST' === $method) curl_setopt($ch, CURLOPT_POST, 1);
            else curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
            $this->withHeader('Content-Length', mb_strlen($body));
        }

        // устанавливаем cookie
        if (!empty($this->cookies)) {
            $cookies = [];
            foreach ($this->cookies as $name => $value) {
                $cookies[] = "$name=$value";
            }
            $this->withHeader('Cookie', implode(';', $cookies));
        }
        $headers = $this->getHeadersLines();

        // устанавливаем url и заголовки
        $uri .= static::prepareDataToUriQuery($this->getQuery());
        curl_setopt($ch, CURLOPT_URL, $uri);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        return $this;
    }

    /**
     * Отправка curl запроса.
     * @return CurlResponse
     */
    public function send(): CurlResponse
    {
        return new CurlResponse($this);
    }

    /**
     * Деструктор. Очищаем ресурс curl.
     */
    public function __destruct()
    {
        unset($this->ch);
    }
}
