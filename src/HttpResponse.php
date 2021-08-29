<?php
/**
 * Абстрактный класс http ответа.
 * @package evas-php\evas-http
 * @author Egor Vasyakin <egor@evas-php.com>
 */
namespace Evas\Http;

use Evas\Http\Cookie;
use Evas\Http\HttpException;
use Evas\Http\Interfaces\ResponseInterface;
use Evas\Http\Traits\HttpBodyTrait;
use Evas\Http\Traits\HttpHeadersTrait;
use Evas\Http\Traits\HttpSetCookieTrait;

abstract class HttpResponse implements ResponseInterface
{
    /**
     * Подключаем трейты тела, заголовков, установки cookie.
     */
    use HttpBodyTrait, HttpHeadersTrait, HttpSetCookieTrait;

    /** @static array маппинг статусов ответа */
    const HTTP_STATUSES = [
        '101' => 'Web Socket Protocol Handshake',
        '200' => 'OK',
        '400' => 'Bad Request',
        '401' => 'Unauthorized',
        '403' => 'Forbidden',
        '404' => 'Not Found',
        '500' => 'Internal Server Error',
    ];

    /** @static string ошибка о ненайденом статусе ответа */
    const ERROR_HTTP_STATUS_NOT_FOUND = 'Http status not found';

    /** @var int код статуса */
    public $statusCode = 200;

    /** @var string текст статуса */
    public $statusText = 'OK';

    /**
     * Установка кода статуса.
     * @param int код статуса
     * @param string|null кастомный текст статуса
     * @return self
     * @throws HttpException
     */
    public function withStatusCode(int $code, string $statusText = null): object
    {
        $this->statusCode = $code;
        return $this->withStatusText($statusText);
    }

    /**
     * Установка текста статуса.
     * @param string|null кастомный текст статуса
     * @return self
     * @throws HttpException
     */
    public function withStatusText(string $statusText = null): object
    {
        $this->statusText = $statusText ?? static::HTTP_STATUSES[$this->statusCode] ?? null;
        if (! $this->statusText) {
            throw new HttpException(static::ERROR_HTTP_STATUS_NOT_FOUND . " $this->statusCode");
        }
        return $this;
    }

    /**
     * Запись в тело ответа.
     * Псевдоним для метода withAddedBody.
     * @param string данные
     * @return self
     */
    public function write(string $message): object
    {
        return $this->withAddedBody($message);
    }
    

    /**
     * Получение кода статуса.
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * Получение текста статуса.
     * @return string|null
     */
    public function getStatusText(): ?string
    {
        return $this->statusText ?? static::HTTP_STATUSES[$this->statusCode] ?? null;
    }


    /**
     * Отправка.
     * @param int|null код статуса
     * @param string|null тело
     * @param array|null заголовки
     */
    public function send(int $code = null, string $body = null, array $headers = null)
    {
        if (!empty($code)) $this->withStatusCode($code);
        if (!empty($headers)) $this->withAddedHeaders($headers);
        if (!empty($body)) $this->write($body);
        $type = $this->getHeader('Content-Type');
        if (false !== strpos($type, 'application/json')) {
            $this->withBodyJson($this->getBody());
        }
        if (method_exists($this, 'realSend')) {
            return $this->realSend();
        }
    }

    /**
     * Отправка с преобразованием тела в json.
     * @param int|null код статуса
     * @param mixed|null тело
     * @param array|null заголовки
     */
    public function sendJson(int $code = null, $body = null, array $headers = null)
    {
        $this->withHeader('Content-Type', 'application/json');
        return $this->send($code, $body, $headers);
    }

    /**
     * Редирект.
     * @param string uri редиректа
     */
    public function redirect(string $to)
    {
        return $this->withHeader('Location', $to)->send();
    }
}
