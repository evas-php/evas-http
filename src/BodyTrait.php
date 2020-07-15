<?php
/**
 * @package evas-php\evas-http
 */
namespace Evas\Http;

/**
 * Трейт тела запроса/овтета.
 * @author Egor Vasyakin <egor@evas-php.com>
 * @since 1.0
 */
trait BodyTrait
{
    /**
     * @var string тело
     */
    public $body = '';

    /**
     * Установка тела.
     * @param string
     * @return self
     */
    public function withBody(string $body)
    {
        $this->body = $body;
        return $this;
    }

    /**
     * Установка тела с преобразованием в json.
     * @param mixed
     * @return self
     */
    public function withBodyJson($body)
    {
        return $this->withBody(json_encode($body));
    }

    /**
     * Получение тела.
     * @return string
     */
    public function getBody(): string
    {
        return $this->body;
    }

    /**
     * Получение тела с преобразованием json.
     * @return object|null
     */
    public function getBodyJson(): ?object
    {
        $decoded = json_decode($this->getBody());
        return $decoded ?? null;
    }
}
