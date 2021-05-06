<?php
/**
 * Класс uri.
 * @package evas-php\evas-http
 * @author Egor Vasyakin <egor@evas-php.com>
 */
namespace Evas\Http;

use Evas\Http\Interfaces\UriInterface;

class Uri implements UriInterface
{
    const HTTP_DEFAULT_HOST = 'localhost';

    private static $defaultPorts = [
        'http'  => 80,
        'https' => 443,
        'ftp' => 21,
        'gopher' => 70,
        'nntp' => 119,
        'news' => 119,
        'telnet' => 23,
        'tn3270' => 23,
        'imap' => 143,
        'pop' => 110,
        'ldap' => 389,
    ];

    /** @var string Uri scheme */
    private $scheme;

    /** @var string|null Uri user info */
    private $userInfo;

    /** @var string Uri host */
    private $host;

    /** @var int|null Uri port */
    private $port;

    /** @var string Uri path */
    private $path = '';

    /** @var string|null Uri query string */
    private $query;

    /** @var string|null Uri fragment */
    private $fragment;

    /**
     * Конструктор.
     * @param string|null uri для парсинга
     */
    public function __construct(string $uri = null)
    {
        if (!empty($uri)) {
            $parts = parse_url($uri);
            if (false === $parts) {
                throw new \InvalidArgumentException("Unable to parse URI: $uri");
            }
            $this->applyParts($parts);
        }
    }

    public function __toString()
    {
        return self::composeComponents(
            $this->scheme,
            $this->getAuthority(),
            $this->path,
            $this->query,
            $this->fragment
        );
    }

    public static function composeComponents(string $scheme = null, string $authority = null, string $path = null, string $query = null, string $fragment = null)
    {
        $uri = '';
        if (!empty($scheme)) $uri .= $scheme . ':';
        if (!empty($authority) || $scheme === 'file') $uri .= '//' . $authority;
        if ((!empty($authority) || 'file' === $scheme)
            && !empty($path) && '/' !== $path[0]) {
            $path = '/' . $path;
        }
        $uri .= $path;
        if (!empty($query)) $uri .= '?' . $query;
        if (!empty($fragment)) $uri .= '#' . $fragment;
        return $uri;
    }

    /**
     * Создание URI из частей uri разобранных с помощью parse_uri.
     * @param array части uri
     * @return static
     */
    public static function createFromUriParts(array $parts): UriInterface
    {
        return (new self())->applyParts($parts);
    }

    /**
     * Проверка является ли порт URI портом по умолчанию.
     * @return bool
     */
    public function isDefaultPort(): bool
    {
        return $this->getPort() === null
            || (isset(self::$defaultPorts[$this->getScheme()]) 
                && $this->getPort() === self::$defaultPorts[$this->getScheme()]);
    }

    /**
     * Проверка на сетевой uri.
     * @return bool
     */
    public function isNetwork(): bool
    {
        return !empty($this->getScheme()) && !empty($this->getAuthority());
    }

    /**
     * Проверка uri на абсолютный.
     * @return bool
     */
    public function isAbsolute(): bool
    {
        return empty($this->getScheme()) 
            && empty($this->getAuthority())
            && !empty($this->getPath()) && '/' === $this->getPath()[0];
    }

    /**
     * Проверка uri на относительный.
     * @return bool
     */
    public function isRelative(): bool
    {
        return empty($this->getScheme())
            && empty($this->getAuthority())
            && (empty($this->getPath()) || '/' !== $this->getPath()[0]);
    }



    /**
     * Получение схемы uri.
     * @return string|null uri scheme
     */
    public function getScheme(): ?string
    {
        return $this->scheme;
    }

    /**
     * Получение authority компонента uri.
     * Формат: "[user-info@]host[:port]"
     * @return string|null uri authority
     */
    public function getAuthority(): ?string
    {
        $a = $this->host;
        if (!empty($this->userInfo)) $a = $this->userInfo . '@' . $a;
        if (!empty($this->port)) $a .= ':' . $this->port;
        return $a;
    }

    /**
     * Получение компонента пользовательской информации uri.
     * Формат: "username[:password]"
     * @return string|null uri userinfo
     */
    public function getUserInfo(): ?string
    {
        return $this->userInfo;
    }

    /**
     * Получение хоста uri.
     * @return string|null uri host
     */
    public function getHost(): ?string
    {
        return $this->host;
    }

    /**
     * Получение порта uri.
     * @return int|null uri port
     */
    public function getPort(): ?int
    {
        return $this->port;
    }

    /**
     * Получение пути uri.
     * @return string|null uri path
     */
    public function getPath(): ?string
    {
        return $this->path;
    }

    /**
     * Получение query строки uri.
     * @return string|null uri query string
     */
    public function getQuery(): ?string
    {
        return $this->query;
    }

    /**
     * Получение fragment компонента uri
     * @return string|null uri fragment
     */
    public function getFragment(): ?string
    {
        return $this->fragment;
    }


    /**
     * Установка схемы uri.
     * @param string схема uri
     * @return self
     */
    public function withScheme(string $scheme): UriInterface
    {
        $this->scheme = strtolower($scheme);
        $this->removeDefaultPort();
        if (empty($this->host) && in_array($this->scheme, ['http', 'https'])) {
            $this->withHost(self::HTTP_DEFAULT_HOST);
        }
        return $this;
    }

    /**
     * Установка информации пользователя uri.
     * @param string имя пользователя
     * @param null|string пароль
     * @return self
     */
    public function withUserInfo(string $user, string $password = null): UriInterface
    {
        $this->userInfo = $user;
        if (!empty($password)) {
            $this->userInfo .= ':' . $password;
        }
        return $this;
    }

    /**
     * Установка хоста uri.
     * @param string хост
     * @return self
     */
    public function withHost(string $host): UriInterface
    {
        $this->host = strtolower($host);
        return $this;
    }

    /**
     * Установка порта uri.
     * @param null|int порт или null для сброса на порт по умолчанию
     * @return self
     */
    public function withPort(int $port = null): UriInterface
    {
        if (is_int($port) && (0 > $port || 0xffff < $port)) {
            throw new \InvalidArgumentException('Invalid port: $port. Must be between 0 and 65535');
        }
        $this->port = $port;
        $this->removeDefaultPort();
        return $this;
    }

    /**
     * Установка пути uri.
     * @param string|null путь
     * @return self
     */
    public function withPath(string $path = null): UriInterface
    {
        $this->path = $path;
        return $this;
    }

    /**
     * Установка query строки uri.
     * @param string|null query строка
     * @return self
     */
    public function withQuery(string $query = null): UriInterface
    {
        $this->query = $query;
        return $this;
    }

    /**
     * Установка query строки uri из маппинга.
     * @param array|null маппинг свойств query
     * @return self
     */
    public function withQueryParams(array $params = null): UriInterface
    {
        foreach ($params as $name => $value) {
            $parts[] = urlencode($name) .'='. urlencode($value);
        }
        if (!empty($parts)) {
            return $this->withQuery(implode('&', $parts));
        }
        return $this;
    }

    /**
     * Установка фрагмента uri.
     * @param string|null фрагмент uri
     * @return self
     */
    public function withFragment(string $fragment = null): UriInterface
    {
        $this->fragment = $fragment;
        return $this;
    }


    /**
     * Применение частей uri разобранных с помощью parse_url.
     * @param array части uri
     * @return self
     */
    private function applyParts(array $parts): UriInterface
    {
        $this->withScheme($parts['scheme'] ?? '');
        if (!empty($parts['user'])) {
            $this->withUserInfo($parts['user'], $parts['pass'] ?? null); 
        }
        $this->withHost($parts['host'] ?? '');
        $this->withPort($parts['port'] ?? null);
        $this->withPath($parts['path'] ?? null);
        $this->withQuery($parts['query'] ?? null);
        $this->withFragment($parts['fragment'] ?? null);
        $this->removeDefaultPort();
        return $this;
    }

    /**
     * Сброс порта по умолчанию.
     */
    private function removeDefaultPort()
    {
        if ($this->port !== null && $this->isDefaultPort()) {
            $this->port = null;
        }
    }
}
