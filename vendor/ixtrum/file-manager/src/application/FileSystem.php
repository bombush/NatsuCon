<?php

/**
 * This file is part of the Ixtrum File Manager package (http://ixtrum.com/file-manager)
 *
 * (c) Bronislav Sedlák <sedlak@ixtrum.com>)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace Ixtrum\FileManager\Application;

use Nette\Utils\Strings,
    Ixtrum\FileManager\Application\FileSystem\Finder;

/**
 * Collection of file system functions.
 *
 * @author Bronislav Sedlák <sedlak@ixtrum.com>
 */
class FileSystem
{

    /**
     * Get unique file/directory path
     *
     * @param string $path Path to file/dir
     *
     * @return string
     */
    public static function getUniquePath($path)
    {
        if (file_exists($path)) {

            $filename = basename($path);
            $dirname = dirname($path);
            $i = 1;
            while (file_exists($path = "$dirname/$i" . "_$filename")) {
                $i++;
            }
        }
        return $path;
    }

    /**
     * Copy file/directory to destination directory
     *
     * @param string  $source      Source file/directory path
     * @param string  $destination Destination directory path
     * @param boolean $overwrite   Overwrite file/directory if exist
     *
     * @throws \Exception
     */
    public function copy($source, $destination, $overwrite = false)
    {
        if (!is_dir($destination)) {
            throw new \Exception("Destination must be existing directory, but '$destination' given!");
        }

        // Get destination file/directory path
        $fileName = basename($source);
        if ($overwrite) {
            $destination .= DIRECTORY_SEPARATOR . $fileName;
        } else {
            $destination = self::getUniquePath($destination . DIRECTORY_SEPARATOR . $fileName);
        }

        if (is_dir($source)) {
            $this->copyDir($source, $destination);
        } else {
            $this->copyFile($source, $destination);
        }
    }

    /**
     * Copy directory
     *
     * @param string $source      Source directory
     * @param string $destination Destination directory
     *
     * @throws \Exception
     */
    public function copyDir($source, $destination)
    {
        if (!is_dir($source)) {
            throw new \Exception("Source must be existing directory, but '$source' given!");
        }

        // Create destination directory if not exists
        if (!is_dir($destination)) {
            $this->mkdir($destination);
        }

        foreach (Finder::find("*")->in($source) as $file) {

            $destPath = $destination . DIRECTORY_SEPARATOR . $file->getFilename();
            if ($file->isDir()) {
                $this->copyDir($file->getRealPath(), $destPath);
            } else {
                $this->copyFile($file->getRealPath(), $destPath);
            }
        }
    }

    /**
     * Copy file (chunked)
     *
     * @param string $src  Source file
     * @param string $dest Destination file
     *
     * @throws \Exception
     */
    public function copyFile($src, $dest)
    {
        if (!is_file($src)) {
            throw new \Exception("Source must be existing file, but '$src' given!");
        }

        $buffer_size = 1048576;
        $ret = 0;
        $fin = fopen($src, "rb");
        $fout = fopen($dest, "w");

        while (!feof($fin)) {
            $ret += fwrite($fout, fread($fin, $buffer_size));
        }

        fclose($fin);
        fclose($fout);
    }

    /**
     * Check if destination directory is located in it's sub-directory
     *
     * @param string $root Original directory path
     * @param string $dir  Tested directory path
     *
     * @return boolean
     */
    public function isSubDir($root, $dir)
    {
        if (!is_dir($root)) {
            throw new \Exception("'$root' is not directory!");
        }

        if ($root === $dir) {
            return false;
        }
        return strpos($dir, $root) === 0;
    }

    /**
     * Get permissions
     *
     * @link http://php.net/manual/en/function.fileperms.php
     *
     * @param string $path Path to file
     *
     * @return string
     */
    public function getFileMod($path)
    {
        $perms = fileperms($path);

        if (($perms & 0xC000) == 0xC000) {
            // Socket
            $info = 's';
        } elseif (($perms & 0xA000) == 0xA000) {
            // Symbolic Link
            $info = 'l';
        } elseif (($perms & 0x8000) == 0x8000) {
            // Regular
            $info = '-';
        } elseif (($perms & 0x6000) == 0x6000) {
            // Block special
            $info = 'b';
        } elseif (($perms & 0x4000) == 0x4000) {
            // Directory
            $info = 'd';
        } elseif (($perms & 0x2000) == 0x2000) {
            // Character special
            $info = 'c';
        } elseif (($perms & 0x1000) == 0x1000) {
            // FIFO pipe
            $info = 'p';
        } else {
            // Unknown
            $info = 'u';
        }

        // Owner
        $info .= (($perms & 0x0100) ? 'r' : '-');
        $info .= (($perms & 0x0080) ? 'w' : '-');
        $info .= (($perms & 0x0040) ?
                        (($perms & 0x0800) ? 's' : 'x' ) :
                        (($perms & 0x0800) ? 'S' : '-'));

        // Group
        $info .= (($perms & 0x0020) ? 'r' : '-');
        $info .= (($perms & 0x0010) ? 'w' : '-');
        $info .= (($perms & 0x0008) ?
                        (($perms & 0x0400) ? 's' : 'x' ) :
                        (($perms & 0x0400) ? 'S' : '-'));

        // World
        $info .= (($perms & 0x0004) ? 'r' : '-');
        $info .= (($perms & 0x0002) ? 'w' : '-');
        $info .= (($perms & 0x0001) ?
                        (($perms & 0x0200) ? 't' : 'x' ) :
                        (($perms & 0x0200) ? 'T' : '-'));

        return $info;
    }

    /**
     * Get safe directory name
     *
     * @param string $name Directory name
     *
     * @return string
     */
    public function safeDirname($name)
    {
        $except = array('\\', '/', ':', '*', '?', '"', '<', '>', '|');
        $name = str_replace($except, '', $name);

        if (substr($name, 0) == ".") {
            $name = "";
        } elseif (str_replace(array('.', ' '), '', $name) == "") {  # because of this: .. .
            $name = "";
        }

        return Strings::toAscii($name);
    }

    /**
     * Get safe file name
     *
     * @param string $name File name
     *
     * @return string
     */
    public function safeFilename($name)
    {
        $except = array("\\", "/", ":", "*", "?", '"', "<", ">", "|");
        $name = str_replace($except, "", $name);

        return Strings::toAscii($name);
    }

    /**
     * Delete file/directory
     *
     * @param string $path Path to file/directory
     *
     * @return boolean
     */
    public function delete($path)
    {
        if (is_dir($path)) {

            foreach (Finder::find("*")->in($path) as $item) {

                if ($item->isDir()) {
                    $this->delete($item->getRealPath());
                } else {
                    unlink($item->getPathName());
                }
            }

            if (!@rmdir($path)) {
                return false;
            }
        } else {
            unlink($path);
        }

        return true;
    }

    /**
     * Create directory
     *
     * @param string  $path Path to directory
     * @param integer $mask Directory mask
     *
     * @return boolean
     */
    public function mkdir($path, $mask = 0777)
    {
        $oldumask = umask(0);
        if (mkdir($path, $mask)) {
            umask($oldumask);
            return true;
        }
        umask($oldumask);
        return false;
    }

    /**
     * Get root directory name
     *
     * @return string
     */
    public static function getRootname()
    {
        return "/";
    }

    /**
     * Get file/directory size
     *
     * @param string $path Path to file/dir
     *
     * @return string
     */
    public function getSize($path)
    {
        if (is_dir($path)) {

            $size = 0;
            $files = Finder::findFiles("*")->from($path);
            foreach ($files as $file) {
                $size += $this->getSize($file->getPathName());
            }
            return $size;
        }

        $fileSize = new FileSystem\FileSize($path);
        return $fileSize->getSize();
    }

}