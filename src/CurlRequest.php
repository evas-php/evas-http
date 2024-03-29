<?php
/**
 * Класс curl запроса.
 * @package evas-php\evas-http
 * @author Egor Vasyakin <egor@evas-php.com>
 */
namespace Evas\Http;

use Evas\Http\CurlResponse;
use Evas\Http\HttpException;
use Evas\Http\HttpRequest;

class CurlRequest extends HttpRequest
{
    /** @static array маппинг типов прокси */
    public static $proxyTypesMap = [
        'http' => CURLPROXY_HTTP,
        'socks4' => CURLPROXY_SOCKS4,
        'socks5' => CURLPROXY_SOCKS5,
        'socks4a' =>  CURLPROXY_SOCKS4A,
        'socks5hostname' => CURLPROXY_SOCKS5_HOSTNAME,
    ];
    
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
     * Очистка curl handler.
     * @return self
     */
    public function reset()
    {
        if (!empty($this->ch)) curl_reset($this->ch);
        return $this;
    }

    /**
     * Установка user agent.
     * @param string user agent
     * @return self
     */
    public function withUserAgent(string $user_agent)
    {
        curl_setopt($this->getCh(), CURLOPT_USERAGENT, $user_agent);
        return $this;
    }

    /**
     * Установка времени ожидания ответа.
     * @param int время в секундах
     * @return self
     */
    public function withTimeout(int $timeout)
    {
        curl_setopt($this->getCh(), CURLOPT_CONNECTTIMEOUT, $timeout);
        return $this;
    }

    /**
     * Установка прокси.
     * @param array данные прокси
     * @return self
     * @throws HttpException
     */
    public function withProxy(array $proxy) {
        extract($proxy);
        if (!isset($type) || !isset($ip) || !isset($port)) {
            throw new HttpException('Curl proxy not has type, ip or host');
        }
        // установка адреса
        $address = sprintf(
            strrpos($type, 'socks') !== false ? '%sh://%s:%s' : '%s://%s:%s', 
            $type, $ip, $port
        );
        curl_setopt($this->getCh(), CURLOPT_PROXY, $address);
        // установка логина/пароля
        if (!empty($login)) curl_setopt($this->getCh(), CURLOPT_PROXYUSERPWD, "$login:$password");
        // установка типа
        $proxyType = static::$proxyTypesMap[$type] ?? null;
        if (!empty($proxyType)) {
            // if ($proxyType === 'socks5') curl_setopt($this->getCh(), CURLOPT_SOCKS5_AUTH, CURLAUTH_BASIC | CURLAUTH_GSSAPI | CURLAUTH_NONE );
            curl_setopt($this->getCh(), CURLOPT_PROXYTYPE, $proxyType);
        }
        return $this;
    }

    /**
     * Преобразование данных в query-чать uri.
     * @param array|object|null данные
     * @return string
     */
    public static function prepareDataToUriQuery($data = null): string
    {
        return empty($data) ? '' : ('?' . http_build_query($data));
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
        $this->prepareSend();
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
