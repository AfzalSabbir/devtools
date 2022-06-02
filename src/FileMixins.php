<?php

namespace KDA\Backpack;

class FileMixins
{
    private $stubs = [];

    public function kda_stub()
    {
        return function ($path) {

            if (!isset($this->stubs[$path])) {
                if (file_exists($path)) {
                    $this->stubs[$path] = $this->get($path);
                } else {
                    $stubPath = file_exists($customPath = CUSTOM_STUBS_PATH . '/' . $path)
                        ? $customPath
                        : STUBS_PATH . '/' . $path;
                    $this->stubs[$path] = $this->get($stubPath);
                }
            }

            return $this->stubs[$path];
        };
    }
}
