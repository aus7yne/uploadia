<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 12-Jul-18
 * Time: 7:38 AM
 */

namespace Coderatio\Uploadia;

use Exception;

class Uploadia
{
    public $destinationPath;
    public $errorMessage;
    public $extensions;
    public $allowAll;
    public $maxSize = 134217728;
    public $uploadName;
    public $sequence = '';
    public $name = 'Uploader';
    public $sameName = false;
    public $useTable = false;

    function setDir($path)
    {
        $this->destinationPath = $path;
        $this->allowAll = false;

        return $this;
    }

    function allowAllFormats()
    {
        $this->allowAll = true;

        return $this;
    }

    function setMaxSize($sizeMB)
    {
        $this->maxSize = $sizeMB * (1024 * 1024);

        return $this;
    }

    function setExtensions($extensions)
    {
        $this->extensions = $extensions;

        return $this;
    }

    function setSameFileName()
    {
        $this->sameFileName = true;
        $this->sameName = true;

        return $this;
    }

    function getExtension($string)
    {
        try {
            $parts = explode(".", $string);
            $ext = strtolower($parts[count($parts) - 1]);
        } catch (Exception $c) {
            $ext = "";
        }

        return $ext;
    }

    function setMessage($message)
    {
        $this->errorMessage = $message;

        return $this;
    }

    function getMessage()
    {
        return $this->errorMessage;
    }

    function getUploadName()
    {
        return $this->uploadName;
    }

    function setSequence($seq)
    {
        $this->sequence = $seq;

        return $this;
    }

    function getRandom()
    {
        return strtotime(date('Y-m-d H:i:s')) . rand(1111, 9999) . rand(11, 99) . rand(111, 999);
    }

    function sameName($true)
    {
        $this->sameName = $true;

        return $this;
    }

    function uploadFile($fileBrowse)
    {
        $result = false;
        if ($this->selected($fileBrowse)) {
            $size = $_FILES[$fileBrowse]["size"];
            $name = $_FILES[$fileBrowse]["name"];
            $ext = $this->getExtension($name);

            if (!is_dir($this->destinationPath)) {
                $this->setMessage("Destination folder is not a directory ");
            } else if (!is_writable($this->destinationPath)) {
                $this->setMessage("Destination is not writable !");
            } else if (empty($name)) {
                $this->setMessage("File not selected ");
            } else if ($size > $this->maxSize) {
                $this->setMessage("Too large file !");
            } else if ($this->allowAll || (!$this->allowAll && in_array($ext, $this->extensions))) {

                if ($this->sameName == false) {
                    $this->uploadName = $this->sequence . "_" . substr(md5(rand(1111, 9999)), 0, 8) . $this->getRandom() . rand(1111, 1000) . rand(99, 9999) . "." . $ext;
                } else {
                    $this->uploadName = $name;
                }

                if (move_uploaded_file($_FILES[$fileBrowse]["tmp_name"], $this->destinationPath . '/' . $this->uploadName)) {
                    $result = true;
                    $this->setMessage("File uploaded");
                } else {
                    $this->setMessage("Upload failed , try later !");
                }

            } else {
                $this->setMessage("Invalid file format !");
            }

        }

        return $result;
    }

    function selected($file)
    {
        if ($_FILES[$file]['size'] > 0) {
            return true;
        } else {
            return false;
        }
    }

    function deleteUploaded()
    {
        return @unlink($this->destinationPath . $this->uploadName);
    }

}