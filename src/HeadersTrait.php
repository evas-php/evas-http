<?php
/**
 * @package evas-php\evas-http
 */
namespace Evas\Http;

/**
 * Трейт заголовков.
 * @author Egor Vasyakin <egor@evas-php.com>
 * @since 1.0
 */
 trait HeadersTrait
 {
    /**
     * @var array заголовки
     */
    public $headers = [];

    /**
     * Установка заголовков.
     * @param array
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
     * Получение заголовков.
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
     * Получение заголовка.
     * @param string имя
     * @return string|null значение
     */
    public function getHeader(string $name): ?string
    {
        return $this->headers[strtolower($name)] ?? null;
    }
 }
