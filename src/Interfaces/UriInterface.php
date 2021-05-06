<?php
/**
 * Интерфейс uri.
 * @package evas-php\evas-http
 * @author Egor Vasyakin <egor@evas-php.com>
 * @link http://tools.ietf.org/html/rfc3986 (the URI specification)
 */
namespace Evas\Http\Interfaces;

interface UriInterface
{
    /**
     * Получение схемы uri.
     * @return string|null uri scheme
     */
    public function getScheme(): ?string;

    /**
     * Получение authority компонента uri.
     * Формат: "[user-info@]host[:port]"
     * @return string|null uri authority
     */
    public function getAuthority(): ?string;

    /**
     * Получение компонента пользовательской информации uri.
     * Формат: "username[:password]"
     * @return string|null uri userinfo
     */
    public function getUserInfo(): ?string;

    /**
     * Получение хоста uri.
     * @return string|null uri host
     */
    public function getHost(): ?string;

    /**
     * Получение порта uri.
     * @return int|null uri port
     */
    public function getPort(): ?int;

    /**
     * Получение пути uri.
     * @return string|null uri path
     */
    public function getPath(): ?string;

    /**
     * Получение query строки uri.
     * @return string|null uri query string
     */
    public function getQuery(): ?string;

    /**
     * Получение fragment компонента uri
     * @return string|null uri fragment
     */
    public function getFragment(): ?string;

    /**
     * Установка схемы uri.
     * @param string схема uri
     * @return self
     */
    public function withScheme(string $scheme): UriInterface;

    /**
     * Установка информации пользователя uri.
     * @param string имя пользователя
     * @param null|string пароль
     * @return self
     */
    public function withUserInfo(string $user, string $password = null): UriInterface;

    /**
     * Установка хоста uri.
     * @param string хост
     * @return self
     */
    public function withHost(string $host): UriInterface;

    /**
     * Установка порта uri.
     * @param null|int порт или null для сброса на порт по умолчанию
     * @return self
     */
    public function withPort(int $port = null): UriInterface;

    /**
     * Установка пути uri.
     *
     * Путь может быть 1 - пустым, 2 - абсолютным (начинается с косой черты) 
     * или 3 - без корня (не начинающийся с косой черты). 
     * Реализации ДОЛЖНЫ поддерживать все три синтаксиса.
     * 
     * Если путь предназначен для определения домена, а не пути, он должен 
     * начинаться с косой черты ("/"). Предполагается, что пути, не 
     * начинающиеся с косой черты («/»), относятся к некоторому базовому пути, 
     * известному приложению или потребителю.
     *
     * @param string|null путь
     * @return self
     */
    public function withPath(string $path = null): UriInterface;

    /**
     * Установка query строки uri.
     * @param string|null query строка
     * @return self
     */
    public function withQuery(string $query = null): UriInterface;

    /**
     * Установка query строки uri из маппинга.
     * @param array|null маппинг свойств query
     * @return self
     */
    public function withQueryParams(array $params = null): UriInterface;

    /**
     * Установка фрагмента uri.
     * @param string|null фрагмент uri
     * @return self
     */
    public function withFragment(string $fragment = null): UriInterface;

    /**
     * Получение uri строки.
     * - Если схема присутствует, она ДОЛЖНА быть дополнена суффиксом ":".
     * - Если авторитет присутствует, он ДОЛЖЕН иметь префикс "//".
     * - Путь можно объединять без разделителей. Но есть два случая, когда путь 
     *   должен быть скорректирован, чтобы сделать ссылку URI действительной, 
     *   поскольку PHP не позволяет генерировать исключение в __toString ():
     *     - Если путь не имеет корневого каталога и есть полномочия, путь 
     *       ДОЛЖЕН иметь префикс "/".
     *     - Если путь начинается с более чем одного символа "/" и права доступа 
     *       отсутствуют, начальная косая черта ДОЛЖНА быть уменьшена до единицы.
     * - Если запрос присутствует, он ДОЛЖЕН иметь префикс «?».
     * - Если фрагмент присутствует, он ДОЛЖЕН иметь префикс "#".
     * @see http://tools.ietf.org/html/rfc3986#section-4.1
     * @return string
     */
    public function __toString();
}
