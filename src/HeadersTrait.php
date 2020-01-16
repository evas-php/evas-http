<?php
/**
 * @package evas-php/evas-http
 */
namespace Evas\Http;

/**
 * Трейт заголовков.
 * @author Egor Vasyakin <e.vasyakin@itevas.ru>
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
    public function withHeaders(array $headers)
    {
        $this->headers = &$headers;
        return $this;
    }

    /**
     * Установка заголовков поверх имеющихся.
     * @param array
     * @return self
     */
    public function withAddedHeaders(array $headers)
    {
        $this->headers = array_merge($this->headers, $headers);
        return $this;
    }

    /**
     * Установка заголовка.
     * @param string имя
     * @param string значение
     * @return self
     */
    public function withHeader(string $name, string $value)
    {
        $this->headers[$name] = $value;
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
                $data[$name] = $this->headers[$name];
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
    public function getHeader(string $name)
    {
        return $this->headers[$name] ?? null;
    }
 }
