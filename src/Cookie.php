<?php
/**
 * Класс свойства cookie.
 * @package evas-php\evas-http
 * @author Egor Vasyakin <egor@evas-php.com>
 */
namespace Evas\Http;

class Cookie
{
    /** @var string имя свойства */
    public $name;
    /** @var string|int значение свойства */
    public $value;

    /** @var int метка времени истечения свойства */
    public $expires;
    /** @var int время истечения свойства */
    public $maxAge;

    /** @var string|null путь действия */
    public $path;
    /** @var string|null домен действия */
    public $host;

    /** @var bool поддержка только защищенного соединения */
    public $secure;
    /** @var bool поддержка только http протокола */
    public $httpOnly;

    /** @static array маппинг значений свойств по умолчанию */
    public static $defaults = [];

    /**
     * Установка значений свойств по умолчанию
     * @param array|null маппинг значений свойств или null для сброса
     */
    public static function setDefaults(array $defaults = null)
    {
        static::$defaults = $defaults ?? [];
    }

    /**
     * Конструктор.
     * @param string имя свойства
     * @param array|null маппинг других параметров свойства
     */
    public function __construct(string $name, array $props = null)
    {
        $this->name = $name;
        foreach (static::$defaults as $name => $value) {
            $this->$name = $value;
        }
        if (!empty($props)) foreach ($props as $name => $value) {
            if (!empty($value)) $this->$name = $value;
        }
    }

    /**
     * Установка значения совйства cookie.
     * @param значение
     * @return self
     */
    public function withValue($value): Cookie
    {
        $this->value = $value;
        return $this;
    }

    /**
     * Установка json значения свойства cookie.
     * @param mixed значение
     * @return self
     */
    public function withJsonValue($value): Cookie
    {
        return $this->withValue(json_encode($value));
    }

    /**
     * Установка serialize значения свойства cookie.
     * @param mixed значение
     * @return self
     */
    public function withSerializeValue($value): Cookie
    {
        return $this->withValue(serialize($value));
    }

    /**
     * Установка пути свойства cookie.
     * @param string путь
     * @return self
     */
    public function withPath(string $path): Cookie
    {
        $this->path = $path;
        return $this;
    }

    /**
     * Установка хоста свойства cookie.
     * @param string хост
     * @return self
     */
    public function withHost(string $host): Cookie
    {
        $this->host = $host;
        return $this;
    }

    /**
     * Установка времени действия свойства cookie.
     * @param int метка времени
     * @return self
     */
    public function withExpires(int $expires): Cookie
    {
        $this->expires = $expires;
        return $this;
    }

    /**
     * Установка времени действия свойства cookie относительно текущего времени.
     * @param int сдвиг метки времени
     * @return self
     */
    public function withExpiresRelatively(int $relatively): Cookie
    {
        return $this->withExpires(time() + $relatively);
    }

    /**
     * Установка времени действия свойства cookie относительно текущего времени.
     * @param int сдвиг метки времени
     * @return self
     */
    public function withMaxAge(int $age): Cookie
    {
        $this->maxAge = $age;
        return $this;
    }

    /**
     * Установка поддержки защищеного соединения для свойства cookie.
     * @return self
     */
    public function withSecure(): Cookie
    {
        $this->secure = true;
        return $this;
    }

    /**
     * Установка поддержки только http соединения для свойства cookie.
     * @return self
     */
    public function withHttpOnly(): Cookie
    {
        $this->httpOnly = true;
        return $this;
    }

    /**
     * Преобразование объекта cookie в строку http-заголовка.
     * @return string
     */
    public function __toString(): string
    {
        $str = "$this->name=$this->value";
        if (!empty($this->expires)) {
            $str .= sprintf('; Expires=%s GMT', date('r', $this->expires));
        } else if (!empty($this->maxAge)) {
            $str .= "; Max-Age=$this->maxAge";
        }
        if (!empty($this->path)) $str .= "; Path=$this->path";
        if (!empty($this->host)) $str .= "; Domain=$this->host";
        if (true === $this->secure) $str .= '; Secure';
        if (true === $this->httpOnly) $str .= '; HttpOnly';
        return $str;
    }
}
