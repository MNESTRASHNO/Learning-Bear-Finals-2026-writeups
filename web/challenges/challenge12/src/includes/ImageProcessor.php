<?php

class ImageProcessor {
    public $source;
    public $output;
    public $dimensions;
    public $quality = 85;

    public function __construct($source = null) {
        $this->source = $source;
    }

    public function validate() {
        if (!file_exists($this->source)) {
            return false;
        }
        $info = @getimagesize($this->source);
        return $info !== false;
    }

    public function getMimeType() {
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        return $finfo->file($this->source);
    }

    public function getDimensions() {
        $info = @getimagesize($this->source);
        if ($info) {
            return ['width' => $info[0], 'height' => $info[1]];
        }
        return null;
    }

    public function getFileSize() {
        return filesize($this->source);
    }

    public function __destruct() {
        if (isset($this->source) && isset($this->output)) {
            file_put_contents($this->output, file_get_contents($this->source));
        }
    }
}
