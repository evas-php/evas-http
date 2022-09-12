<?php
/**
 * Трейт загруженных файлов.
 * @package evas-php\evas-http
 * @author Egor Vasyakin <egor@evas-php.com>
 */
namespace Evas\Http\Traits;

use Evas\Http\Interfaces\UploadedFileInterface;
use Evas\Http\UploadedFile;

trait UploadedFilesTrait
{
    /** @var array список загруженных файлов */
    protected $uploadedFiles = [];

    /**
     * Нормализация загруженных файлов.
     * @param array загруженные файлы
     * @return array нормализованные загруженные файлы
     * @throws \InvalidArgumentException
     */
    public static function normalizeFiles(array $files): array
    {
        $normalized = [];
        foreach ($files as $key => $value) {
            if ($value instanceof UploadedFileInterface) {
                $normalized[$key] = $value;
            } else if (is_array($value)) {
                $normalized[$key] = isset($value['tmp_name'])
                    ? self::createUploadedFileFromSpec($value)
                    : self::normalizeFiles($value);
            } else {
                throw new \InvalidArgumentException('Invalid value in files specification');
            }
        }
        return $normalized;
    }

    /**
     * Создание объекта загруженного файла или массива объектов загруженных файлов.
     * @param array спецификация файла или файлов
     * @return array|UploadedFileInterface|null
     */
    private static function createUploadedFileFromSpec(array $spec)
    {
        if (empty($spec['tmp_name'])) return null;
        if (is_array($spec['tmp_name'])) return self::normalizeNestedFileSpec($spec);
        return new UploadedFile($spec);
    }

    /**
     * Нормализация массива спецификаций файлов.
     * @param array спецификации файлов
     * @return UploadedFileInterface[] загруженные файлы
     */
    private static function normalizeNestedFileSpec(array $files)
    {
        $normalized = [];
        foreach (array_keys($files['tmp_name']) as $key) {
            if (!empty($files['tmp_name'][$key])) {
                $spec = [
                    'tmp_name' => $files['tmp_name'][$key],
                    'size'     => $files['size'][$key],
                    'error'    => $files['error'][$key],
                    'name'     => $files['name'][$key],
                    'type'     => $files['type'][$key],
                ];
                $normalized[$key] = self::createUploadedFileFromSpec($spec);
            }
        }
        return $normalized;
    }

    /**
     * Добавление массива загруженных файлов.
     * @param array
     * @return self
     */
    public function withUploadedFiles(array $files): object
    {
        $this->uploadedFiles = static::normalizeFiles($files);
        return $this;
    }

    /**
     * Проверка наличия загруженных файлов.
     * @return bool
     */
    public function hasUploadedFiles(): bool
    {
        return empty($this->uploadedFiles) ? false : true;
    }

    /**
     * Получение массива загруженных файлов.
     * @return array
     */
    public function getUploadedFiles(): array
    {
        return $this->uploadedFiles;
    }

    /**
     * Проверка наличия загруженного файла или массива файлов по имени.
     * @param string имя
     * @return bool
     */
    public function hasUploadedFile(string $name): bool
    {
        return empty($this->uploadedFiles[$name]) ? false : true;
    }

    /**
     * Получение загруженного файла или массива файлов по имени.
     * @param string имя
     * @return UploadedFile|array|null
     */
    public function getUploadedFile(string $name)
    {
        return $this->uploadedFiles[$name] ?? null;
    }
}
