<?php
/**
 * Интерфейс запроса.
 * @package evas-php\evas-http
 * @author Egor Vasyakin <egor@evas-php.com>
 */
namespace Evas\Http\Interfaces;

use Evas\Http\Interfaces\UriInterface;

interface RequestInterface
{
    /**
     * Установка метода.
     * @param string
     * @return self
     */
    public function withMethod(string $method);

    /**
     * Установка uri.
     * @param UriInterface|string
     * @return self
     * @throws \InvalidArgumentException
     */
    public function withUri($uri);

    /**
     * Установка параметров POST.
     * @param array
     * @return self
     */
    public function withPost(array $post);

    /**
     * Установка параметров GET.
     * @param array
     * @return self
     */
    public function withQuery(array $query);

    /**
     * Установка ip пользователя.
     * @param string
     * @return self
     */
    public function withUserIp(string $user_ip);



    /**
     * Получение метода
     * @return string
     */
    public function getMethod(): string;

    /**
     * Проверка на совпадение метода.
     * @param string проверяемый метод
     * @return bool
     */
    public function isMethod(string $method): bool;

    /**
     * Получение uri.
     * @return UriInterface
     */
    public function getUri(): UriInterface;

    /**
     * Получение цели запроса.
     * @return string
     */
    public function getRequestTarget(): string;

    /**
     * Получение пути из uri.
     * @return string|null
     */
    public function getPath(): ?string;

    /**
     * Получение параметра/параметров POST.
     * @param string|array|null имя или массив имен
     * @return string|array значение или маппинг значений по именам
     */
    public function getPost($name = null);

    /**
     * Получение параметров POST списком.
     * @param array имена
     * @return array значения
     */
    public function getPostList(array $names): array;

    /**
     * Получение параметра/параметров GET.
     * @param string|array|null имя или массив имен
     * @return string|array значение или маппинг значений по именам
     */
    public function getQuery($name = null);

    /**
     * Получение параметров GET списком.
     * @param array имена
     * @return array значения
     */
    public function getQueryList(array $names): array;

    /**
     * Получение параметра/параметров по методу запроса.
     * @param string|array|null имя или массив имен
     * @return string|array значение или маппинг значений по именам
     */
    public function getParams($name = null);

    /**
     * Получение параметров списком по методу запроса.
     * @param array имена
     * @return array значения
     */
    public function getParamsList(array $names): array;

    /**
     * Получение ip пользователя.
     * @return string
     */
    public function getUserIp(): ?string;
}
