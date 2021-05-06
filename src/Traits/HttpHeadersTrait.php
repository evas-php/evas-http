<?php
/**
 * Трейт заголовков http.
 * @package evas-php\evas-http
 * @author Egor Vasyakin <egor@evas-php.com>
 */
namespace Evas\Http\Traits;

trait HttpHeadersTrait
{
    /** @var array маппинг заголовков */
    public $headers = [];

    /**
     * Установка заголовков.
     * @param array маппинг заголовков
     * @return self
     */
    public function withHeaders(array $headers): object
    {
        foreach ($headers as $name => $value) {
            $this->withHeader($name, $value);
        }
        return $this;
    }

    /**
     * Установка заголовка.
     * @param string имя
     * @param string значение
     * @return self
     */
    public function withHeader(string $name, string $value): object
    {
        $this->headers[strtolower($name)] = $value;
        return $this;
    }

    /**
     * Установка заголовка в дополнение к имеющемуся.
     * @param array
     * @return self
     */
    public function withAddedHeader(string $name, string $value): object
    {
        $this->headers[strtolower($name)] = $this->getHeader($name) . $value;
        return $this;
    }

    /**
     * Проверка наличия заголовка.
     * @param string имя заголовка
     * @return bool
     */
    public function hasHeader(string $name): bool
    {
        return isset($this->headers[strtolower($name)]) ? true : false;
    }

    /**
     * Получение заголовка.
     * @param string имя
     * @return string|null значение
     */
    public function getHeader(string $name): ?string
    {
        return $this->headers[strtolower($name)] ?? null;
    }

    /**
     * Получение маппинга заголовков.
     * @param array|null имена
     * @return array маппинг значений по именам
     */
    public function getHeaders(array $names = null): array
    {
        if (is_array($names)) {
            $data = [];
            foreach ($names as &$name) {
                $data[$name] = $this->headers[strtolower($name)];
            }
            return $data;
        }
        return $this->headers;
    }

    /**
     * Получение строки ззаголовка.
     * @param string имя заголовка
     * @return string|null строка заголовка
     */
    public function getHeaderLine(string $name): ?string
    {
        $value = $this->getHeader($name) ?? null;
        if (empty($value)) return null;
        return "$name: $value";
    }

    /**
     * Получение строк заголовков.
     * @param array|null имена
     * @return array строки заголовков
     */
    public function getHeadersLines(array $names = null): array
    {
        if (empty($names)) $names = $this->getHeadersNames();
        $list = [];
        foreach ($names as &$name) {
            $list[] = $this->getHeaderLine($name);
        }
        return $list;
    }

    /**
     * Получение имен заголовков.
     * @return array
     */
    public function getHeadersNames(): array
    {
        return array_keys($this->headers);
    }
 }
