<?php
/**
 * @author Jakub Stribrny <stribrny@1webit.cz>
 */

namespace Custom\Content;

/**
 * Class ImageBase64Upload
 *
 * @package Custom\Content
 */

class ImageBase64Upload
{
    protected $value;

    protected $filename;

    protected $tmpFilename;

    protected $fullTmpFilePath;

    protected function __construct($value, $filename) {
        $this->filename = $filename;
        $this->value = $value;
    }

    public function move($dest)
    {
        rename($this->fullTmpFilePath, $dest);
    }


    protected function getTmpDir()
    {
        return dirname(__FILE__);
    }

    protected function stripBase64Prefix($value) {
         $prefixPos = strpos($value, 'base64,');
        if($prefixPos !== FALSE)
            $value = substr($value, $prefixPos + 7);

        return $value;
    }

    protected function _saveTmp()
    {
        $tmpFilename = md5( time() ) . '_' . $this->filename;
        $fullTmpFilePath = $this->getTmpDir() . "/" . $tmpFilename;

        $value = $this->stripBase64Prefix( $this->value );
        if(FALSE === file_put_contents( $fullTmpFilePath, base64_decode( $value ) ))
            throw new Base64UploadException('Failed to save to ' . $fullTmpFilePath);

        $this->fullTmpFilePath = $fullTmpFilePath;
        $this->tmpFilename = $tmpFilename;
    }

    public static function saveTmp($value, $filename)
    {
        $instance = new self($value, $filename);
        $instance->_saveTmp();

        return $instance;
    }

    public function getSanitizedName()
    {
        return $this->filename;
    }

    public function getFullTmpFilePath()
    {
        return $this->fullTmpFilePath;
    }

    public function unlink()
    {
        if(!$this->getFullTmpFilePath())
            throw new Base64UploadException('No full tmp path to unlink');

        $unlinkOk = unlink( $this->getFullTmpFilePath());

        if(!$unlinkOk)
            throw new Base64UploadException('Unlink failed: ' . error_get_last());
    }
}

class Base64UploadException extends \Exception {}