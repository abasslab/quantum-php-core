<?php
/**
 * Quantum PHP Framework
 *
 * An open source software development framework for PHP
 *
 * @package Quantum
 * @author Arman Ag. <arman.ag@softberg.org>
 * @copyright Copyright (c) 2018 Softberg LLC (https://softberg.org)
 * @link http://quantum.softberg.org/
 * @since 2.6.0
 */

namespace Quantum\Libraries\Storage;

/**
 * Class LocalFileSystemAdapter
 * @package Quantum\Libraries\Storage
 */
class LocalFileSystemAdapter implements FilesystemAdapterInterface
{

    /**
     * @var LocalFileSystemAdapter|null
     */
    private static $instance = null;

    /**
     * Get Instance
     * @return \Quantum\Libraries\Storage\LocalFileSystemAdapter|null
     */
    public static function getInstance(): ?LocalFileSystemAdapter
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @inheritDoc
     */
    public function makeDirectory(string $dirname): bool
    {
        return mkdir($dirname);
    }

    /**
     * @inheritDoc
     */
    public function removeDirectory(string $dirname): bool
    {
        return rmdir($dirname);
    }

    /**
     * @inheritDoc
     */
    public function get(string $filename): string
    {
        return file_get_contents($filename);
    }

    /**
     * @inheritDoc
     */
    public function put(string $filename, string $content): string
    {
        return file_put_contents($filename, $content, LOCK_EX);
    }

    /**
     * @inheritDoc
     */
    public function append(string $filename, string $content): string
    {
        return file_put_contents($filename, $content, FILE_APPEND | LOCK_EX);
    }

    /**
     * @inheritDoc
     */
    public function rename(string $oldName, string $newName): bool
    {
        return rename($oldName, $newName);
    }

    /**
     * @inheritDoc
     */
    public function copy(string $source, string $dest): bool
    {
        return copy($source, $dest);
    }

    /**
     * @inheritDoc
     */
    public function exists(string $filename): bool
    {
        return file_exists($filename) && is_file($filename);
    }

    /**
     * @inheritDoc
     */
    public function size(string $filename)
    {
        return filesize($filename);
    }

    /**
     * @inheritDoc
     */
    public function lastModified(string $filename)
    {
        return filemtime($filename);
    }

    /**
     * @inheritDoc
     */
    public function remove(string $filename): bool
    {
        return unlink($filename);
    }

    /**
     * @inheritDoc
     */
    public function isFile(string $filename): bool
    {
        return is_file($filename);
    }

    /**
     * @inheritDoc
     */
    public function isDirectory(string $dirname): bool
    {
        return is_dir($dirname);
    }

    /**
     * Is Readable
     * @param string $filename
     * @return bool
     */
    public function isReadable(string $filename): bool
    {
        return is_readable($filename);
    }

    /**
     * Is Writable
     * @param string $filename
     * @return bool
     */
    public function isWritable(string $filename): bool
    {
        return is_writable($filename);
    }

    /**
     * Gets the content between given lines
     * @param string $filename
     * @param int $offset
     * @param int|null $length
     * @param int $flags
     * @return array
     */
    public function getLines(string $filename, int $offset, ?int $length, int $flags = 0): array
    {
        return array_slice(file($filename, $flags), $offset, $length, true);
    }

    /**
     * Gets the file name
     * @param string $path
     * @return string
     */
    public function fileName(string $path): string
    {
        return (string)pathinfo($path, PATHINFO_FILENAME);
    }

    /**
     * Gets the file extension
     * @param string $path
     * @return string
     */
    public function extension(string $path): string
    {
        return (string)pathinfo($path, PATHINFO_EXTENSION);
    }

    /**
     * Find path names matching a pattern
     * @param string $pattern
     * @param int $flags
     * @return array|false
     */
    public function glob(string $pattern, int $flags = 0)
    {
        return glob($pattern, $flags);
    }

}