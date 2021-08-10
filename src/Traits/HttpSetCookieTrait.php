<?php
/**
 * Трейт установки http cookie.
 * @package evas-php\evas-http
 * @author Egor Vasyakin <egor@evas-php.com>
 */
namespace Evas\Http\Traits;

use Evas\Base\Help\PhpHelp;
use Evas\Http\Cookie;
use Evas\Http\Traits\HttpCookiesTrait;

trait HttpSetCookieTrait
{
    /**
     * Подключаем трейт поддержки списка cookies.
     */
    use HttpCookiesTrait {
        withCookies as protected;
        withCookie as protected;
    }

    /**
     * Установка нового свойства cookie.
     * @param string|Cookie имя cookie или объект Сookie
     * @param mixed|null значение cookie
     * @param int|null время жизни cookie
     * @param string|null путь
     * @param string|null хост
     * @param bool|null защищенное ли соединение 
     * @param bool|null поддержка только http
     * @return self
     * @throws \InvalidArgumentException
     */
    public function setCookie($name, $value = null, int $expires = null, string $path = null, string $host = null, bool $secure = null, bool $httpOnly = null): object
    {
        if (is_string($name)) {
            $props = compact('value', 'expires', 'path', 'host', 'secure', 'httpOnly');
            $cookie = (new Cookie($name, $props));
        } else if ($name instanceof Cookie) {
            $cookie = $name;
        } else {
            throw new \InvalidArgumentException(sprintf(
                'Argument 1 passed to %s() must be string or instance of %s, %s given',
                __METHOD__, Cookie::class, PhpHelp::getType($name)
            ));
        }
        $this->withCookie($cookie->name, $cookie);
        return $this;
    }

    /**
     * Запуск сборки свойства cookie.
     * @param string имя cookie
     * @param callable|array колбэек или массив свойств cookie
     * @return Cookie
     * @throws \InvalidArgumentException
     */
    public function buildCookie(string $name, $props = null): Cookie
    {
        if (is_callable($props)) {
            $cookie = new Cookie($name);
            $props = $props->bindTo($cookie);
            $props();
        } else if (PhpHelp::isAssoc($props)) {
            $cookie = new Cookie($name, $props);
        } else {
            throw new \InvalidArgumentException(sprintf(
                'Argument 2 passed to %s() must be array or closure, %s given',
                __METHOD__, PhpHelp::getType($props)
            ));
        }
        $this->setCookie($cookie);
        return $cookie;
    }
}
