<?php

namespace Mediadata\Utils;

use ZipArchive;
use RuntimeException;
use InvalidArgumentException;
use function basename;
use function file_exists;
use function is_dir;
use function mkdir;
use function sprintf;
use function sys_get_temp_dir;
use function tempnam;
use function unlink;

class FileUtils
{
    /**
     * @param string|null $basePath
     * @param string      $prefix
     *
     * @return null|string
     */
    public static function createTempDir(string $basePath = null, string $prefix = ''): ?string
    {
        $tempFile = tempnam($basePath ?? sys_get_temp_dir(), $prefix);
        if (file_exists($tempFile)) {
            unlink($tempFile);
        }
        if (!mkdir($tempFile) && !is_dir($tempFile)) {
            throw new RuntimeException(sprintf('Directory "%s" was not created', $tempFile));
        }
        if (is_dir($tempFile)) {
            return $tempFile;
        }
        return null;
    }

    /**
     * @param $dir
     *
     * @return bool
     */
    public static function delTree($dir): bool
    {
        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            (is_dir("$dir/$file")) ? self::delTree("$dir/$file") : unlink("$dir/$file");
        }

        return rmdir($dir);
    }

    /**
     * @param $path
     */
    public static function createDir($path):void
    {
        if (!is_dir($path) && !@mkdir($path, 0775, true)) {
            throw new InvalidArgumentException(sprintf('The directory "%s" does not exist and could not be created.', $path));
        }
    }

    /**
     * Moves files to another folder then returns new fullpath location
     *
     * @param $files
     * @param $folder
     *
     * @return array
     */
    public static function moveFilesToFolder($files, $folder): array
    {
        $files  = (array)$files;
        $output = [];
        foreach ($files as $f) {
            $fileName    = basename($f);
            $destPath = sprintf('%s%s', $folder, $fileName);
            @rename($f, $destPath);
            $output[] = $destPath;
        }
        return $output;
    }

    /**
     * @param array $files
     */
    public static function unlinkFiles(array $files): void
    {
        foreach ($files as $f) {
            @unlink($f);
        }
    }

    /**
     * @param $filesGenerated
     * @param $filename
     * @param $outputFolder
     * @return string|null
     */
    protected function createZipFile($filesGenerated, $filename, $outputFolder): ?string
    {
        if (count($filesGenerated) > 0) {
            $zip          = new ZipArchive();
            $filenamePath = sprintf('%s%s', $outputFolder, $filename);
            if ($zip->open($filenamePath, ZipArchive::CREATE | ZipArchive::OVERWRITE | ZipArchive::FL_NODIR) === true) {
                foreach ($filesGenerated as $f) {
                    $zip->addFile($f, basename($f));
                }
                $zip->close();
                return $filenamePath;
            }
        }
        return null;
    }
}
