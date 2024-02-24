<?php

namespace CH\CHCore\Services;

use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

class ConstantService
{
    protected static array $constants;
    protected static array $excludeConstants = [];

    public function __construct()
    {
        // only frontend requests allowed
        if ($this->getTyposcriptFrontendController()?->tmpl === null) {
            return;
        }

        // ensure that the typoscript constants are processed
        if (
            $this->getTyposcriptFrontendController()->tmpl->flatSetup === null
            || !is_array($this->getTyposcriptFrontendController()->tmpl->flatSetup)
            || count($this->getTyposcriptFrontendController()->tmpl->flatSetup) === 0
        ) {
            $this->getTyposcriptFrontendController()->tmpl->generateConfig();
        }

        // if constants are not set yet, set them and replace nested constants
        if (empty(self::$constants)) {
            self::$constants = $this->getTyposcriptFrontendController()->tmpl->flatSetup;

            // resolve nested constants
            foreach (self::$constants as $key => $constant) {
                self::$constants[$key] = $this->replaceNestedConstants($constant);
            }
        }

        if (empty(self::$excludeConstants)) {
            $excludeConstantsList = $this
                ->getTyposcriptFrontendController()
                ->tmpl->setup['plugin.']['tx_ch_core.']['excludeConstants'];

            if (!empty($excludeConstantsList)) {
                self::$excludeConstants = array_unique(explode(',', $excludeConstantsList));
            }
        }
    }

    private function excludeConstantsFromArray(array $array, array $keys): array
    {
        return array_diff_key($array, array_flip($keys));
    }

    private function replaceNestedConstants(string $constantValue): string
    {
        $isAtLeastOneNested = (bool)preg_match_all('/{\$([^}]+)}/', $constantValue, $matches);
        if ($isAtLeastOneNested) {
            /**
             * go through each match and check if it is a valid constant
             * if not, then ignore it
             * if yes, then proceed and replace the current match with
             * the value of the nested constant.
             * if that has a nested value itself, repeat the whole procedure
             * example:
             *  $matches[0][0] like "{$blabla}"
             *  $matches[1][0] like "blabla" (without the {$} wrap).
             **/
            foreach ($matches[1] as $id => $match) {
                $newConstantValue = self::$constants[$match] ?? null;
                if ($newConstantValue !== null) {
                    $constantValue = (string)str_replace(
                        $matches[0][$id],
                        $this->replaceNestedConstants($newConstantValue),
                        $constantValue
                    );
                }
            }
        }

        return $constantValue;
    }

    public function getConstant(string $name): string
    {
        $name = str_replace(['<', '>'], ['{', '}'], $name);
        return self::$constants[$this->replaceNestedConstants($name)] ?? '';
    }

    /**
     * Return an array of all found constants
     * (converted into a deep array)
     * from:
     *   key.of.constant => "value"
     * to:
     *   'key' => [
     *    'of' => [
     *     'constant' => "value"
     *    ]
     *   ]
     */
    public function getConstants(): array
    {
        $constants = $this->createNestedArrayFromFlatInput(self::$constants);
        // remove unimportant constants nodes
        return $this->excludeConstantsFromArray($constants, self::$excludeConstants);
    }

    protected function createNestedArrayFromFlatInput(array $input): array
    {
        $nestedArray = [];
        foreach ($input as $constKey => $constValue) {
            $temp = &$nestedArray;
            foreach (explode('.', $constKey) as $key) {
                $temp = &$temp[$key];
            }
            $temp = $constValue;
        }

        return $nestedArray;
    }

    protected function getTyposcriptFrontendController(): ?TypoScriptFrontendController
    {
        return $GLOBALS['TSFE'] ?? null;
    }
}
