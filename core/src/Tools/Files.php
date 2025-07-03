<?php

declare(strict_types=1);

namespace MXRVX\ORM\Tools;

use MXRVX\ORM\Exceptions\FileNotFoundException;
use MXRVX\ORM\Exceptions\FilesException;
use MXRVX\ORM\Exceptions\WriteErrorException;

class Files
{
    // For files: the owner and group can read and write
    public const FILE_RUNTIME = 0666;

    // For files: only the owner can write
    public const FILE_READONLY = 0644;

    // For directories: the owner and group can read, write, and enter
    public const DIR_RUNTIME = 0775;

    // For directories: only the owner can read, write, and enter
    public const DIR_READONLY = 0755;

    /**
     * Few size constants for better size manipulations.
     */
    public const KB = 1024;

    public const MB = 1048576;
    public const GB = 1073741824;

    /**
     * Default location (directory) separator.
     */
    public const SEPARATOR = '/';

    /**
     * @throws \RuntimeException
     */
    public static function createDirectory(string $directory, ?int   $mode = null, bool   $recursivePermissions = true): bool
    {
        if (empty($mode)) {
            $mode = self::DIR_READONLY;
        }

        //Directories always executable
        $mode |= 0o111;
        if (\is_dir($directory)) {
            //Exists :(
            return self::setPermissions($directory, $mode);
        }

        if (!$recursivePermissions) {
            return \mkdir($directory, $mode, true);
        }

        $directoryChain = [\basename($directory)];

        $baseDirectory = $directory;
        while (!\is_dir($baseDirectory = \dirname($baseDirectory))) {
            $directoryChain[] = \basename($baseDirectory);
        }

        foreach (\array_reverse($directoryChain) as $dir) {
            if (!\mkdir($baseDirectory = \sprintf('%s/%s', $baseDirectory, $dir))) {
                return false;
            }

            \chmod($baseDirectory, $mode);
        }

        return true;
    }

    public static function read(string $filename): string
    {
        if (!self::exists($filename)) {
            throw new FileNotFoundException($filename);
        }

        $result = \file_get_contents($filename);
        return $result === false
            ? throw new FilesException(\sprintf('Unable to read file `%s`.', $filename))
            : $result;
    }

    public static function include(string $filename): mixed
    {
        if (!self::exists($filename)) {
            throw new FileNotFoundException($filename);
        }

        \ob_start();
        /**
         * @psalm-suppress UnresolvableInclude
         * @var mixed $result
         */
        $result = include $filename;
        $output = \ob_get_clean();

        return $result === false
            ? throw new FilesException(\sprintf('Unable to include or parse file `%s`. Output: %s', $filename, \var_export($output, true)))
            : $result;
    }

    /**
     * @param bool $append To append data at the end of existed file.
     */
    public static function write(string $filename, string $data, ?int $mode = null, bool $ensureDirectory = false, bool $append = false): bool
    {
        $mode ??= self::FILE_READONLY;

        try {
            if ($ensureDirectory) {
                self::createDirectory(\dirname($filename), $mode);
            }

            if (self::exists($filename)) {
                //Forcing mode for existed file
                self::setPermissions($filename, $mode);
            }

            $result = \file_put_contents(
                $filename,
                $data,
                $append ? FILE_APPEND | LOCK_EX : LOCK_EX,
            );

            if ($result !== false) {
                //Forcing mode after file creation
                self::setPermissions($filename, $mode);
            }
        } catch (\Exception $e) {
            throw new WriteErrorException($e->getMessage(), (int) $e->getCode(), $e);
        }

        return $result !== false;
    }

    public static function append(string $filename, string $data, ?int $mode = null, bool $ensureDirectory = false): bool
    {
        return self::write($filename, $data, $mode, $ensureDirectory, true);
    }

    public static function delete(string $filename): bool
    {
        if (self::exists($filename)) {
            $result = \unlink($filename);

            //Wiping out changes in local file cache
            \clearstatcache(false, $filename);

            return $result;
        }

        return false;
    }

    /**
     * @see http://stackoverflow.com/questions/3349753/delete-directory-with-files-in-it
     *
     * @throws FilesException
     */
    public static function deleteDirectory(string $directory, bool $contentOnly = false): bool
    {
        if (!self::isDirectory($directory)) {
            throw new FilesException(\sprintf('Undefined or invalid directory `%s`', $directory));
        }

        /** @var \SplFileInfo[] $files */
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($directory, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST,
        );

        foreach ($files as $file) {
            if ($file->isDir()) {
                \rmdir($file->getRealPath());
            } else {
                self::delete($file->getRealPath());
            }
        }

        if (!$contentOnly) {
            return \rmdir($directory);
        }

        return true;
    }

    public static function move(string $filename, string $destination): bool
    {
        if (!self::exists($filename)) {
            throw new FileNotFoundException($filename);
        }

        return \rename($filename, $destination);
    }

    public static function copy(string $filename, string $destination): bool
    {
        if (!self::exists($filename)) {
            throw new FileNotFoundException($filename);
        }

        return \copy($filename, $destination);
    }

    public static function touch(string $filename, ?int $mode = null): bool
    {
        if (!\touch($filename)) {
            return false;
        }

        return self::setPermissions($filename, $mode ?? self::FILE_READONLY);
    }

    public static function getPermissions(string $filename): int
    {
        self::exists($filename) or throw new FileNotFoundException($filename);
        $permission = \fileperms($filename);
        $permission === false and throw new FilesException(
            \sprintf('Unable to read permissions for `%s`.', $filename),
        );
        return $permission & 0777;
    }

    public static function setPermissions(string $filename, int $mode): bool
    {
        if (\is_dir($filename)) {
            //Directories must always be executable (i.e. 664 for dir => 775)
            $mode |= 0111;
        }

        return self::getPermissions($filename) === $mode || \chmod($filename, $mode);
    }

    public static function exists(string $filename): bool
    {
        return \file_exists($filename);
    }

    public static function size(string $filename): int
    {
        self::exists($filename) or throw new FileNotFoundException($filename);

        return (int) \filesize($filename);
    }

    public static function extension(string $filename): string
    {
        return \strtolower(\pathinfo($filename, PATHINFO_EXTENSION));
    }

    public static function md5(string $filename): string
    {
        if (!self::exists($filename)) {
            throw new FileNotFoundException($filename);
        }

        $result = \md5_file($filename);
        $result === false and throw new FilesException(
            \sprintf('Unable to read md5 hash for `%s`.', $filename),
        );

        return $result;
    }

    public static function time(string $filename): int
    {
        if (!self::exists($filename)) {
            throw new FileNotFoundException($filename);
        }

        return \filemtime($filename) ?: throw new FilesException(
            \sprintf('Unable to read modification time for `%s`.', $filename),
        );
    }

    public static function isDirectory(string $filename): bool
    {
        return \is_dir($filename);
    }

    public static function isFile(string $filename): bool
    {
        return \is_file($filename);
    }
}
