<?php

namespace CH\CHCore\ViewHelpers\Image;

use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Calculates ratios for images for multiple media queries
 * Foremost used in our T3Image partial.
 *
 * @deprecated This viewhelper is deprecated in favor of the new handling from `ch_viewhelpers:v1.0.11`.
 *             With Kickstarter v13, this VH will be gone.
 */
class CalculationViewHelper extends AbstractViewHelper
{
    private const DEFAULTS = [
        'width' => [
            'xs' => 480,      // mini mobile
            's' => 767,       // mobile
            'm' => 1024,      // tablet
            'l' => 1920,      // desktop
            'xl' => 2560,     // wide desktop
            'lbox' => 2560,   // lightbox
            'single' => 1920,
        ],
        'mediaQuery' => [
            'xs' => 480,    // mini mobile
            's' => 767,     // mobile
            'm' => 1024,    // tablet
            'xl' => 1921,   // wide desktop
        ],
        'ratio' => [
            'calc' => [
                '21x9' => 21 / 9,  // 2.3333333333333
                '16x9' => 16 / 9,  // 1.7777777777778
                '7x5' => 7 / 5,    // 1,4
                '5x7' => 5 / 7,    // 0,7142857142857
                '5x4' => 5 / 4,    // 1.25
                '4x5' => 4 / 5,    // 0.8
                '4x3' => 4 / 3,    // 1.3333333333333
                '3x4' => 3 / 4,    // 0.75
                '3x2' => 3 / 2,    // 1.5
                '2x3' => 2 / 3,    // 0.6666666666666
                '1x1' => 1,        // 1
            ],
            'default' => '16x9'
        ]
    ];

    public function initializeArguments(): void
    {
        $this->registerArgument(
            'config',
            'array',
            "for each variant {'all', 'xs', 's', 'm', 'l', 'xl', 'single'} you can define a ratio and a width"
        );
        $this->registerArgument(
            'lboxConfig',
            'array',
            "define a ratio and a width, the defaults are:  {w: '2560', r: '16x9'}"
        );
        $this->registerArgument(
            'as',
            'array',
            "define a alternative names for the vars,
             the defaults are: {defaults: 'defaults', variants: 'finalVariants'}"
        );
    }

    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ): mixed {
        $config = $arguments['config'] ?? [];
        $lboxConfig = $arguments['lboxConfig'] ?? [];
        $nameOfDefaultsVariable = $arguments['as']['defaults'] ?? 'defaults';
        $nameOfVariantsVariable = $arguments['as']['variants'] ?? 'finalVariants';

        $variants = [
            'xs' => [
                'w' => $config['xs']['w'] ?? $config['all']['w'] ?? self::DEFAULTS['width']['xs'],
                'r' => $config['xs']['r'] ?? $config['all']['r'] ?? self::DEFAULTS['ratio']['default']
            ],
            's' => [
                'w' => $config['s']['w'] ?? $config['all']['w'] ?? self::DEFAULTS['width']['s'],
                'r' => $config['s']['r'] ?? $config['all']['r'] ?? self::DEFAULTS['ratio']['default']
            ],
            'm' => [
                'w' => $config['m']['w'] ?? $config['all']['w'] ?? self::DEFAULTS['width']['m'],
                'r' => $config['m']['r'] ?? $config['all']['r'] ?? self::DEFAULTS['ratio']['default']
            ],
            'l' => [
                'w' => $config['l']['w'] ?? $config['all']['w'] ?? self::DEFAULTS['width']['l'],
                'r' => $config['l']['r'] ?? $config['all']['r'] ?? self::DEFAULTS['ratio']['default']
            ],
            'xl' => [
                'w' => $config['xl']['w'] ?? $config['all']['w'] ?? self::DEFAULTS['width']['xl'],
                'r' => $config['xl']['r'] ?? $config['all']['r'] ?? self::DEFAULTS['ratio']['default']
            ],
            'lbox' => [
                'w' => $lboxConfig['w'] ?? self::DEFAULTS['width']['lbox'],
                'r' => $lboxConfig['r'] ?? self::DEFAULTS['ratio']['default']
            ],
            'single' => [
                'w' => $config['single']['w'] ?? $config['all']['w'] ?? self::DEFAULTS['width']['single'],
                'r' => $config['single']['r'] ?? $config['all']['r'] ?? self::DEFAULTS['ratio']['default']
            ],
        ];

        $selectedRatioValue = [
            'xs' => [
                'rv' => self::DEFAULTS['ratio']['calc'][$variants['xs']['r']] ?? null
            ],
            's' => [
                'rv' => self::DEFAULTS['ratio']['calc'][$variants['s']['r']] ?? null
            ],
            'm' => [
                'rv' => self::DEFAULTS['ratio']['calc'][$variants['m']['r']] ?? null
            ],
            'l' => [
                'rv' => self::DEFAULTS['ratio']['calc'][$variants['l']['r']] ?? null
            ],
            'xl' => [
                'rv' => self::DEFAULTS['ratio']['calc'][$variants['xl']['r']] ?? null
            ],
            'lbox' => [
                'rv' => self::DEFAULTS['ratio']['calc'][$variants['lbox']['r']] ?? null
            ],
            'single' => [
                'rv' => self::DEFAULTS['ratio']['calc'][$variants['single']['r']] ?? null
            ],
        ];

        $calculatedHeight = [
            'xs' => [
                'h' => ($selectedRatioValue['xs']['rv'] !== null)
                    ? self::calculate((int)$variants['xs']['w'], (float)$selectedRatioValue['xs']['rv'])
                    : 0
            ],
            's' => [
                'h' => ($selectedRatioValue['s']['rv'] !== null)
                    ? self::calculate((int)$variants['s']['w'], (float)$selectedRatioValue['s']['rv'])
                    : 0
            ],
            'm' => [
                'h' => ($selectedRatioValue['m']['rv'] !== null)
                    ? self::calculate((int)$variants['m']['w'], (float)$selectedRatioValue['m']['rv'])
                    : 0
            ],
            'l' => [
                'h' => ($selectedRatioValue['l']['rv'] !== null)
                    ? self::calculate((int)$variants['l']['w'], (float)$selectedRatioValue['l']['rv'])
                    : 0
            ],
            'xl' => [
                'h' => ($selectedRatioValue['xl']['rv'] !== null)
                    ? self::calculate((int)$variants['xl']['w'], (float)$selectedRatioValue['xl']['rv'])
                    : 0
            ],
            'lbox' => [
                'h' => ($selectedRatioValue['lbox']['rv'] !== null)
                    ? self::calculate((int)$variants['lbox']['w'], (float)$selectedRatioValue['lbox']['rv'])
                    : 0
            ],
            'single' => [
                'h' => ($selectedRatioValue['single']['rv'] !== null)
                    ? self::calculate((int)$variants['single']['w'], (float)$selectedRatioValue['single']['rv'])
                    : 0
            ],
        ];

        // merge all arrays to one
        $variants = array_merge_recursive($variants, $selectedRatioValue, $calculatedHeight);

        // add variables to template
        $renderingContext->getVariableProvider()->add((string)$nameOfDefaultsVariable, self::DEFAULTS);
        $renderingContext->getVariableProvider()->add((string)$nameOfVariantsVariable, $variants);

        // render potential content of vh tag or leave
        return $renderChildrenClosure();
    }

    protected static function calculate(int $width, float $ratioValue): int
    {
        return (int)(
            $width / (($ratioValue !== 0.0) ? $ratioValue : 1)
        );
    }
}
