<?php

namespace CH\CHCore\Converter;

use CH\CHCore\Services\ConstantService;

class TsToJsConverter
{
    protected ?array $outputArray = null;

    public function __construct(
        protected ConstantService $constantService
    ) {
    }

    public function getData(): string
    {
        return self::wrapWithDefaultJSCode($this->getDataInJsonStructure());
    }

    private static function wrapWithDefaultJSCode(string $code): string
    {
        return  /** @lang JavaScript */<<<EOT
(function(window){
    'use strict';
    var CH = window.chiochettibros = window.CH = (window.chiochettibros || window.CH || {});
    CH.constants = CH.constants || {};
    CH.constants = {$code}
})(window);
EOT;
    }

    public function getDataInJsonStructure(): string
    {
        if ($this->outputArray === null) {
            $this->outputArray = $this->constantService->getConstants();
        }

        return self::convertArrayToJson((array)$this->outputArray);
    }

    private static function convertArrayToJson(array $plainArray): string
    {
        try {
            return json_encode(
                $plainArray,
                JSON_THROW_ON_ERROR | JSON_FORCE_OBJECT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
            );
        } catch (\JsonException) {
            return "{}";
        }
    }
}
