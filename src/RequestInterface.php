<?php
/**
 * @package evas-php\evas-http
 */
namespace Evas\Http;

/**
 * Интерфейс запроса.
 * @author Egor Vasyakin <egor@evas-php.com>
 * @since 1.0
 */
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
     * @param string
     * @return self
     */
    public function withUri(string $uri);

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
     * Получение uri.
     * @return string
     */
    public function getUri(): string;

    /**
     * Получение пути из uri.
     * @return string
     */
    public function getPath(): string;

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
