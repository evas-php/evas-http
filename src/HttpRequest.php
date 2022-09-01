<?php
/**
 * Класс http запроса.
 * @package evas-php\evas-http
 * @author Egor Vasyakin <egor@evas-php.com>
 */
namespace Evas\Http;

use Evas\Http\Interfaces\RequestInterface;
use Evas\Http\Interfaces\UriInterface;
use Evas\Http\Traits\HttpBodyTrait;
use Evas\Http\Traits\HttpCookiesTrait;
use Evas\Http\Traits\HttpHeadersTrait;
use Evas\Http\Traits\UploadedFilesTrait;
use Evas\Http\Uri;

class HttpRequest implements RequestInterface
{
    /**
     * Подключаем трейты тела, заголовков, списка cookies, списка загруженных файлов.
     */
    use HttpBodyTrait, HttpHeadersTrait, HttpCookiesTrait, UploadedFilesTrait;

    /** @var string метод */
    protected $method;
    /** @var string uri */
    protected $uri;

    /** @var array параметры POST */
    protected $post = [];
    /** @var array параметры GET */
    protected $query = [];

    /** @var string ip пользователя */
    protected $userIp;

    /**
     * Установка метода.
     * @param string
     * @return self
     */
    public function withMethod(string $method): RequestInterface
    {
        $this->method = strtoupper($method);
        return $this;
    }

    /**
     * Установка uri.
     * @param UriInterface|string uri
     * @param bool|null сохранить ли http Host
     * @return self
     * @throws \InvalidArgumentException
     */
    public function withUri($uri, bool $preserveHost = false): RequestInterface
    {
        if ($uri instanceof UriInterface) {
            $this->uri = &$uri;
        } else if (is_string($uri) || null === $uri) {
            $this->uri = new Uri($uri);
        } else {
            throw new \InvalidArgumentException(sprintf(
                'Uri must be type string or UriInterface, %s given',
                PhpHelper::getType($uri)
            ));
        }
        if (!$preserveHost || $this->hasHeader('Host')) {
            $this->updateHostFromUri();
        }
        return $this;
    }

    /**
     * Обновление http-заголовка Host по Uri.
     */
    private function updateHostFromUri()
    {
        $host = $this->uri->getHost();
        $port = $this->uri->getPort();
        if (empty($host)) return;
        if (!empty($port)) $host .= ':' . $port;
        $this->withHeader('Host', $host);
    }

    /**
     * Установка параметров POST.
     * @param array
     * @return self
     */
    public function withPost(array $post): RequestInterface
    {
        $this->post = &$post;
        return $this;
    }

    /**
     * Установка параметров GET.
     * @param array
     * @return self
     */
    public function withQuery(array $query): RequestInterface
    {
        $this->query = &$query;
        return $this;
    }

    /**
     * Установка ip пользователя.
     * @param string
     * @return self
     */
    public function withUserIp(string $userIp): RequestInterface
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
     * Проверка на совпадение метода.
     * @param string проверяемый метод
     * @return bool
     */
    public function isMethod(string $method): bool
    {
        return $this->method === strtoupper($method);
    }

    /**
     * Получение uri.
     * @return UriInterface
     */
    public function getUri(): UriInterface
    {
        return $this->uri;
    }

    /**
     * Получение цели запроса.
     * @return string
     */
    public function getRequestTarget(): string
    {
        return (string) $this->uri;
    }

    /**
     * Получение пути из uri.
     * @return string|null
     */
    public function getPath(): ?string
    {
        return $this->uri->getPath();
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
        return $this->_getParamsList($this->post, $names);
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
        return $this->_getParamsList($this->query, $names);
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
        return $this->_getParamsList($params[$this->method], $names);
    }


    /**
     * Получение ip пользователя.
     * @return string|null
     */
    public function getUserIp(): ?string
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
    protected function _getParamsList(array &$params, array $names): array
    {
        $data = [];
        foreach ($names as &$name) {
            $data[] = $params[$name] ?? null;
        }
        return $data;
    }

    /**
     * Создание или заполнение объекта запроса из глобальных свойств php.
     * @param HttpRequest|null уже существующий объект http-запроса, если его нужно переконфигурировать
     * @return static
     */
    public static function createFromGlobals(HttpRequest &$instanse = null): HttpRequest
    {
        if (empty($instanse)) {
            $instanse = new static;
        }
        $instanse->withMethod($_SERVER['REQUEST_METHOD'])
        ->withUri($_SERVER['REQUEST_URI'])
        ->withHeaders(static::getAllHeaders())
        ->withUserIp($_SERVER['REMOTE_ADDR'])
        ->withPost($_POST)
        ->withQuery($_GET)
        ->withCookies($_COOKIE)
        ->withUploadedFiles($_FILES)
        ->withBody(file_get_contents('php://input'));
        return $instanse;
    }

    /**
     * Получение заголовков запроса из getallheaders или $_SERVER.
     * @return array
     */
    protected static function getAllHeaders(): array
    {
        if (function_exists('getallheaders')) {
            return getallheaders();
        }
        $headers = [];
        foreach ($_SERVER as $key => &$value) {
            if (preg_match('/^HTTP_(.+)$/', $key, $matches)) {
                $name = $matches[1];
                if ('REFERRER' === $name) $name = 'referer';
                $name = str_replace('_', '-', $name);
                $headers[$name] = $value;
            }
        }
        return $headers;
    }

    /**
     * Получение версии ос и браузера из юзер агента.
     * @param string user agent
     * @return array [browser,os]
     */
    public function parseUserAgent(): array
    {
        $ua = $this->getHeader('User-Agent');
        if (empty($ua)) return [null, null];
        $parts = preg_split("/^([^(]+)\/\S+?\s\(([^;)]+)?;?([^;)]+)?;?[^)]+?\) ?(.+\/\S+?)?\S?(.+\/\S+?)?$/m", $ua, -1, PREG_SPLIT_DELIM_CAPTURE);
        $browser = $parts[1] ?? null;
        $os = (isset($parts[2]) ? $parts[2] : null)
            . (isset($parts[3]) ? ' '. $parts[3] : null);
        return [$browser, $os ?? null];
    }
}
