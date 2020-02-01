<?php
/**
 * @package evas-php/evas-http
 */
namespace Evas\Http;
/**
 * Интерфейс ответа.
 * @author Egor Vasyakin <egor@evas-php.com>
 * @since 1.0
 */
interface ResponseInterface
{
    /**
     * Установка кода статуса.
     * @param int
     * @throws Exception
     * @return self
     */
    public function withStatusCode(int $code);

    /**
     * Установка тела.
     * @param string
     * @return self
     */
    public function withBody(string $body);

    /**
     * Установка тела с преобразованием в json.
     * @param mixed
     * @return self
     */
    public function withBodyJson($body);



    /**
     * Получение кода статуса.
     * @return int
     */
    public function getStatusCode(): int;

    /**
     * Получение тела.
     * @return string
     */
    public function getBody(): string;



    /**
     * Отправка.
     * @param int|null код статуса
     * @param string|null тело
     * @param array|null заголовки
     */
    public function send(int $code = null, string $body = null, array $headers = null);

    /**
     * Отправка с преобразованием тела в json.
     * @param int|null код статуса
     * @param mixed|null тело
     * @param array|null заголовки
     */
    public function sendJson(int $code = null, $body = null, array $headers = null);
}
