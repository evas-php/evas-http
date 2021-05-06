<?php
/**
 * Класс загруженного файла.
 * @package evas-php\evas-http
 * @author Egor Vasyakin <egor@evas-php.com>
 */
namespace Evas\Http;

use Evas\Http\Interfaces\UploadedFileInterface;

class UploadedFile implements UploadedFileInterface
{
    /** @var string|null клиентское имя файла */
    public $name;

    /** @var string|null клиентское расширение имени файла */
    public $extension;

    /** @var string|null клиентский тип файла */
    public $mimeType;

    /** @var string временный путь файла */
    public $tmpPath;

    /** @var string|null ошибка загрузки файла */
    public $error;

    /** @var int размер файла */
    public $size;

    /** @var string путь файла после перемещения */
    public $movedPath;

    /**
     * Конструктор.
     * @param array маппинг свойств файла
     */
    public function __construct(array $props)
    {
        $this->name = $props['name'] ?? null;
        $this->mimeType = $props['type'] ?? null;
        $this->tmpPath = $props['tmp_name'] ?? null;
        $this->error = $props['error'] ?? null;
        $this->size = $props['size'] ?? null;
        if (!empty($this->name)) {
            $this->extension = strtolower(@pathinfo($this->name, PATHINFO_EXTENSION));
        }
    }

    /**
     * Перемещение загруженного файла.
     * @param string новый путь
     */
    public function move(string $to)
    {
        $dir = dirname($to);
        if (!is_dir($dir)) mkdir($dir, 0777, true);

        $extension = substr($to, strrpos($to, '.') + 1);
        if ($extension != $this->extension) {
            $to .= ".$this->extension";
        }
        $this->movedPath = $to;
        return move_uploaded_file($this->tmpPath, $to);
    }

    /**
     * Получение размера файла.
     * @return int
     */
    public function getSize(): int
    {
        return $this->size;
    }

    /**
     * Получение ошибки файла.
     * @return string|null
     */
    public function getError(): ?string
    {
        return $this->error;
    }

    /**
     * Получение клиентского имени файла.
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Получение расширения клиентского имени файла.
     * @return string|null
     */
    public function getExtension(): ?string
    {
        return $this->extension;
    }

    /**
     * Получение клиентского типа файла.
     * @return string|null
     */
    public function getMediaType(): ?string
    {
        return $this->mimeType;
    }

    /**
     * Получение пути файла после перемещения.
     * @return string|null
     */
    public function getMovedPath(): ?string
    {
        return $this->movedPath;
    }
}
