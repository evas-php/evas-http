<?php
/**
 * @package evas-php/evas-http
 */
namespace Evas\Http;

use \Exception;
use Evas\Http\BodyTrait;
use Evas\Http\HeadersTrait;
use Evas\Http\ResponseInterface;

/**
 * Класс ответа.
 * @author Egor Vasyakin <egor@evas-php.com>
 * @since 1.0
 */
class Response implements ResponseInterface
{
    /**
     * Подключаем трейты тела и заголовков.
     */
    use BodyTrait, HeadersTrait;

    /**
     * @static array маппинг статусов ответа
     */
    const HTTP_STATUSES = [
        '101' => 'Web Socket Protocol Handshake',
        '200' => 'OK',
        '400' => 'Bad Request',
        '401' => 'Unauthorized',
        '403' => 'Forbidden',
        '404' => 'Not Found',
        '500' => 'Internal Server Error',
    ];

    /**
     * @static string ошибка о ненайденом статусе ответа
     */
    const ERROR_HTTP_STATUS_NOT_FOUND = 'Http status not found';

    /**
     * @var int код статуса
     */
    public $statusCode = 200;

    /**
     * @var string текст статуса
     */
    public $statusText = 'OK';

    /**
     * Установка кода статуса.
     * @param int
     * @throws Exception
     * @return self
     */
    public function withStatusCode(int $code)
    {
        $this->statusCode = $code;
        $this->statusText = static::HTTP_STATUSES[$this->statusCode] ?? null;
        if (! $this->statusText) {
            throw new Exception(static::ERROR_HTTP_STATUS_NOT_FOUND . " $this->statusCode");
        }
        return $this;
    }

    /**
     * Запись в тело ответа.
     * @param string данные
     * @return self
     */
    public function write(string $message)
    {
        $this->body .= $message;
        return $this;
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
     * Отправка.
     * @param int|null код статуса
     * @param string|null тело
     * @param array|null заголовки
     */
    public function send(int $code = null, string $body = null, array $headers = null)
    {
        if ($code) $this->withStatusCode($code);
        if ($body) $this->write($body);
        if ($headers) $this->withAddedHeaders($headers);
    }

    /**
     * Отправка с преобразованием тела в json.
     * @param int|null код статуса
     * @param mixed|null тело
     * @param array|null заголовки
     */
    public function sendJson(int $code = null, $body = null, array $headers = null)
    {
        if ($body) $this->withBodyJson($body);
        return $this->send($code, null, $headers);
    }

    /**
     * Редирект.
     * @param string куда
     */
    public function redirect(string $to)
    {
        return $this->withHeader('Location', $to)->send();
    }
}
