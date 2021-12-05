<?php
/**
 * Трейт тела http запроса/ответа.
 * @package evas-php\evas-http
 * @author Egor Vasyakin <egor@evas-php.com>
 */
namespace Evas\Http\Traits;

use Evas\Http\HttpException;

if (!defined('EVAS_DECODE_JSON_HTTP_BODY_INTO_ARRAY')) {
    define('EVAS_DECODE_JSON_HTTP_BODY_INTO_ARRAY', true);
}

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
        if (!$this->hasHeader('Content-Type')) {
            $this->withHeader('Content-Type', 'application/json');
        }
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
     * @param string|null явный тип данных для преобразования
     * @return mixed
     * @throws HttpException
     */
    public function getParsedBody(string $_type = null)
    {
        if (empty($this->parsedBody) || true === $reload) {
            $type = $this->getHeader('Content-Type');
            $body = $this->getBody();
            try {
                if (false !== strpos($type, 'application/json') || $_type === 'json') {
                    $this->parsedBody = json_decode($body, EVAS_DECODE_JSON_HTTP_BODY_INTO_ARRAY);
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
}
