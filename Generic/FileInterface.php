<?php namespace codesaur\Generic;

interface FileInterface
{
    public function exists(string $pathname) : bool;
    public function open(string $filename, string $mode, bool $useincludepath = false, $context = null);
    public function close();

    public function getExt(string $path) : string;
    public function getSize(string $path) : int;
    public function getMimeType(string $filename) : string;
    public function getNaked(string $path);
    public function getName(string $path) : string;
    public function getAllowed(int $type = 0) : array;
    
    public function generateName(string $uploadpath, string $filename);
    
    public function formatBytes($bytes, $precision = 2, array $suffixes = ['', 'KB', 'MB', 'GB', 'TB']);
    
    public function isUpload($input) : bool;
    public function isUploadImage($input) : bool;
    public function isUploaded(string $pathname) : bool;
    public function isImage(string $filename) : bool;
    public function isDir(string $filename) : bool;
    
    public function upload(
            $input, string $uploadpath,
            array $allowed = [], $overwrite = false, $sizelimit = false);

    public function read(int $length = 4096) : string;
    public function readFull(int $length = 4096) : array;

    public function duplicate($source);
    public function rename(string $oldname, string $newname, $context = null) : bool;

    public function makeDir(string $pathname, $mode = 0755, $recursive = true, $context = null) : bool;

    public function write(string $content);
    
    public function copyImage(string $src, $dest, int $w, int $h, int $quality = null);
    
    public function getLastError() : int;
}
