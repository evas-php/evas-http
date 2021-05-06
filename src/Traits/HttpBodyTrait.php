<?php
/**
 * Трейт тела http запроса/ответа.
 * @package evas-php\evas-http
 * @author Egor Vasyakin <egor@evas-php.com>
 */
namespace Evas\Http\Traits;

use Evas\Http\HttpException;

trait HttpBodyTrait
{
    /** @var string тело */
    public $body = '';

    /** @var mixed распарсенное тело */
    public $parsedBody;

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
     * Получение распарсенного тела.
     * @param bool|null повторить парсинг
     * @return mixed
     * @throws HttpException
     */
    public function getParsedBody(bool $reload = false)
    {
        if (empty($this->parsedBody) || true === $reload) {
            $type = $this->getHeader('Content-Type');
            $body = $this->getBody();
            try {
                if ('application/json' === $type) {
                    $this->parsedBody = json_decode($body);
                }
            } catch (\Exception $e) {
                throw new HttpException("Failed to parse $type body: $body");
            }
            if (empty($this->parsedBody)) {
                $this->parsedBody = $body;
            }
        }
        return $this->parsedBody;
    }

    /**
     * Получение тела с преобразованием json.
     * @return object|null
     * @throws HttpException
     */
    public function getJsonParsedBody(): ?object
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
