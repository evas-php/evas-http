<?php
/**
 * @package evas-php/evas-http
 */
namespace Evas\Http;

use Evas\Http\BodyTrait;
use Evas\Http\HeadersTrait;
use Evas\Http\RequestInterface;

/**
 * Класс запроса.
 * @author Egor Vasyakin <egor@evas-php.com>
 * @since 1.0
 */
class Request implements RequestInterface
{
    /**
     * Подключаем трейты тела и заголовков.
     */
    use BodyTrait, HeadersTrait;


    /**
     * @var string метод
     */
    public $method;

    /**
     * @var string uri
     */
    public $uri;

    /**
     * @var string путь из uri
     */
    public $path;

    /**
     * @var array параметры POST
     */
    public $post = [];

    /**
     * @var array параметры GET
     */
    public $query = [];

    /**
     * @var string ip пользователя
     */
    public $userIp;

    /**
     * Установка метода.
     * @param string
     * @return self
     */
    public function withMethod(string $method)
    {
        $this->method = $method;
        return $this;
    }

    /**
     * Установка uri.
     * @param string
     * @return self
     */
    public function withUri(string $uri)
    {
        $this->uri = $uri;
        $this->path = parse_url($uri, PHP_URL_PATH) ?? '';
        return $this;
    }

    /**
     * Установка параметров POST.
     * @param array
     * @return self
     */
    public function withPost(array $post)
    {
        $this->post = &$post;
        return $this;
    }

    /**
     * Установка параметров GET.
     * @param array
     * @return self
     */
    public function withQuery(array $query)
    {
        $this->query = &$query;
        return $this;
    }

    /**
     * Установка ip пользователя.
     * @param string
     * @return self
     */
    public function withUserIp(string $userIp)
    {
        $this->userIp = $userIp;
        return $this;
    }



    /**
     * Получение метода
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * Получение uri.
     * @return string
     */
    public function getUri(): string
    {
        return $this->uri;
    }

    /**
     * Получение пути из uri.
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * Получение параметра/параметров POST.
     * @param string|array|null имя или массив имен
     * @return string|array значение или маппинг значений по именам
     */
    public function getPost($name = null)
    {
        return $this->_getParams($this->post, $name);
    }

    /**
     * Получение параметров POST списком.
     * @param array имена
     * @return array значения
     */
    public function getPostList(array $names): array
    {
        return $this->_getListParams($this->post, $names);
    }

    /**
     * Получение параметра/параметров GET.
     * @param string|array|null имя или массив имен
     * @return string|array значение или маппинг значений по именам
     */
    public function getQuery($name = null)
    {
        return $this->_getParams($this->query, $name);
    }

    /**
     * Получение параметров GET списком.
     * @param array имена
     * @return array значения
     */
    public function getQueryList(array $names): array
    {
        return $this->_getListParams($this->query, $names);
    }

    /**
     * Получение параметра/параметров по методу запроса.
     * @param string|array|null имя или массив имен
     * @return string|array значение или маппинг значений по именам
     */
    public function getParams($name = null)
    {
        $params =  ['GET' => &$this->query, 'POST' => &$this->post];
        return $this->_getParams($params[$this->method], $name);
    }

    /**
     * Получение параметров списком по методу запроса.
     * @param array имена
     * @return array значения
     */
    public function getParamsList(array $names): array
    {
        $params =  ['GET' => &$this->query, 'POST' => &$this->post];
        return $this->_getListParams($params[$this->method], $names);
    }


    /**
     * Получение ip пользователя.
     * @return string
     */
    public function getUserIp(): string
    {
        return $this->userIp;
    }


    /**
     * Получение параметра/параметров из маппинга.
     * @param array ссылка на маппинг параметров
     * @param string|array|null имя или массив имен
     * @return string|array значение или маппинг значений по именам
     */
    protected function _getParams(array &$params, $name = null)
    {
        if (is_string($name)) {
            return $params[$name] ?? null;
        }
        if (is_array($name)) {
            $data = [];
            foreach ($name as &$subname) {
                $data[$subname] = $params[$subname] ?? null;
            }
            return $data;
        }
        return $params;
    }

    /**
     * Получение параметров списком.
     * @param array имена
     * @return array значения
     */
    protected function _getListParams(array &$params, array $names)
    {
        $data = [];
        foreach ($names as &$name) {
            $data[] = $params[$name] ?? null;
        }
        return $data;
    }
}
