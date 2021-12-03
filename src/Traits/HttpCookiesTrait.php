<?php
/**
 * Трейт списка http cookies.
 * @package evas-php\evas-http
 * @author Egor Vasyakin <egor@evas-php.com>
 */
namespace Evas\Http\Traits;

use Evas\Http\Cookie;

trait HttpCookiesTrait
{
    /** @var array маппинг свойств cookie */
    protected $cookies = [];

    /**
     * Установка свойств cookie.
     * @param array маппинг свойств cookie
     * @return self
     */
    public function withCookies(array $cookies): object
    {
        foreach ($cookies as $name => $value) {
            $this->withCookie($name, $value);
        }
        return $this;
    }

    /**
     * Установка свойства cookie.
     * @param string имя свойства cookie
     * @param mixed значение
     * @return self
     */
    public function withCookie(string $name, $value): object
    {
        $this->cookies[$name] = $value;
        return $this;
    }

    /**
     * Получение всех свойств cookie.
     * @return array
     */
    public function getCookies(): array
    {
        return $this->cookies;
    }

    /**
     * Получение свойства cookie.
     * @param string имя свойства cookie
     * @return mixed|null значение
     */
    public function getCookie(string $name)
    {
        return $this->cookies[$name] ?? null;
    }

    /**
     * Проверка наличия свойства cookie.
     * @param string имя свойства cookie
     * @return bool
     */
    public function hasCookie(string $name): bool
    {
        return isset($this->cookies[$name]) ? true : false;
    }
}
