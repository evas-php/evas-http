<?php
/**
 * Интерфейс загруженного файла.
 * @package evas-php\evas-http
 * @author Egor Vasyakin <egor@evas-php.com>
 */
namespace Evas\Http\Interfaces;

interface UploadedFileInterface
{
    /**
     * Перемещение загруженного файла.
     * @param string новый путь
     */
    public function move(string $to);

    /**
     * Получение размера файла.
     * @return int
     */
    public function getSize(): int;

    /**
     * Получение ошибки файла.
     * @return string|null
     */
    public function getError(): ?string;

    /**
     * Получение клиентского имени файла.
     * @return string|null
     */
    public function getName(): ?string;

    /**
     * Получение расширения клиентского имени файла.
     * @return string|null
     */
    public function getExtension(): ?string;

    /**
     * Получение клиентского типа файла.
     * @return string|null
     */
    public function getMediaType(): ?string;
}
