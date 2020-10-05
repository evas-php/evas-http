<?php
/**
 * @package evas-php\evas-http
 */
namespace Evas\Http;

use Evas\Http\HttpException;

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
    public function withBody(string $body): object
    {
        $this->body = $body;
        return $this;
    }

    /**
     * Добавление тела.
     * @param string
     * @return self
     */
    public function withAddedBody(string $body): object
    {
        $this->body .= $body;
        return $this;
    }

    /**
     * Установка тела с преобразованием в json.
     * @param mixed
     * @return self
     */
    public function withBodyJson($body): object
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
     * @throws HttpException
     */
    public function getBodyJson(): ?object
    {
        $body = $this->getBody();
        try {
            $decoded = json_decode($body);
            return $decoded ?? null;
        } catch (\Exception $e) {
            throw new HttpException("Failed to parse json body: $body");
        }
    }
}
