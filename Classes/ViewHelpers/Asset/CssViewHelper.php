<?php

namespace CH\CHCore\ViewHelpers\Asset;

use CH\CHCore\Globals\Constants;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Page\AssetCollector;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\TagBuilder;

/**
 * @see \TYPO3\CMS\Fluid\ViewHelpers\Asset\CssViewHelper
 */
class CssViewHelper extends AbstractTagBasedViewHelper
{
    /**
     * This VH does not produce direct output, thus does not need to be wrapped in an escaping node
     *
     * @var bool
     */
    protected $escapeOutput = false;

    /**
     * Rendered children string is passed as CSS code,
     * there is no point in HTML encoding anything from that.
     *
     * @var bool
     */
    protected $escapeChildren = false;

    private AssetCollector $assetCollector;
    private ExtensionConfiguration $extensionConfiguration;
    private static array $jsonConfig = [];
    private static array $fileCache = [];
    private string $outputResourcePath;

    public function injectAssetCollector(AssetCollector $assetCollector): void
    {
        $this->assetCollector = $assetCollector;
    }

    public function injectExtensionConfiguration(ExtensionConfiguration $extensionConfiguration): void
    {
        $this->extensionConfiguration = $extensionConfiguration;
    }

    public function initialize(): void
    {
        // Add a tag builder, that does not html encode values, because rendering with encoding happens in AssetRenderer
        $this->setTagBuilder(
            new class () extends TagBuilder {
                public function addAttribute($attributeName, $attributeValue, $escapeSpecialCharacters = false): void
                {
                    parent::addAttribute($attributeName, $attributeValue, false);
                }
            }
        );
        parent::initialize();
    }

    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerUniversalTagAttributes();
        $this->registerTagAttribute('as', 'string', 'Define the type of content being loaded (For rel="preload" or rel="prefetch" only).', false);
        $this->registerTagAttribute('crossorigin', 'string', 'Define how to handle crossorigin requests.', false);
        $this->registerTagAttribute('disabled', 'bool', 'Define whether or not the described stylesheet should be loaded and applied to the document.', false);
        $this->registerTagAttribute('href', 'string', 'Define the URL of the resource (absolute or relative).', false);
        $this->registerTagAttribute('hreflang', 'string', 'Define the language of the resource (Only to be used if \'href\' is set).', false);
        $this->registerTagAttribute('importance', 'string', 'Define the relative fetch priority of the resource.', false);
        $this->registerTagAttribute('integrity', 'string', 'Define base64-encoded cryptographic hash of the resource that allows browsers to verify what they fetch.', false);
        $this->registerTagAttribute('media', 'string', 'Define which media type the resources applies to.', false, 'screen');
        $this->registerTagAttribute('referrerpolicy', 'string', 'Define which referrer is sent when fetching the resource.', false);
        $this->registerTagAttribute('onload', 'string', 'Define an onload JavaScript function.', false);
        $this->registerTagAttribute('rel', 'string', 'Define the relationship of the target object to the link object.', false);
        $this->registerTagAttribute('sizes', 'string', 'Define the icon size of the resource.', false);
        $this->registerTagAttribute('type', 'string', 'Define the MIME type (usually \'text/css\').', false);
        $this->registerTagAttribute('nonce', 'string', 'Define a cryptographic nonce (number used once) used to whitelist inline styles in a style-src Content-Security-Policy.', false);
        $this->registerArgument(
            'identifier',
            'string',
            'Use this identifier within templates to only inject your CSS once, even though it is added multiple times.',
            true
        );
        $this->registerArgument(
            'priority',
            'boolean',
            'Define whether the css should be inlined (=1) or added via link tag (=0) (in the head).',
            false,
            false
        );
    }

    public function render(): string
    {
        $this->handleJsonConfig();

        $identifier = (string)$this->arguments['identifier'];
        $attributes = $this->tag->getAttributes();

        // boolean attributes shall output attr="attr" if set
        if ($attributes['disabled'] ?? false) {
            $attributes['disabled'] = 'disabled';
        }

        $options = [
            'priority' => (bool)($this->arguments['priority'] ?? false)
        ];

        if (
            !$options['priority']
            && (string)($attributes['onload'] ?? '') === ''
            && (string)($attributes['media'] ?? '') !== 'print'
        ) {
            $attributes['onload'] = "this.onload=null;this.media='{$attributes['media']}';";
            $attributes['media'] = 'print';
        }

        $asset = self::$jsonConfig[$identifier] ?? null;
        $file = $this->tag->getAttribute('href');
        unset($attributes['href'], $attributes['data-asset-id']);

        if ($asset !== null) {
            $attributes['data-asset-id'] = $identifier;
            if ($options['priority'] === true) {
                if ('' !== ($content = $this->getContentOfFile("{$this->outputResourcePath}{$asset}"))) {
                    $this->assetCollector->addInlineStyleSheet($identifier, $content, $attributes, $options);
                }
            } else {
                $this->assetCollector->addStyleSheet($identifier, "{$this->outputResourcePath}{$asset}", $attributes, $options);
            }
        } elseif ($file !== null) {
            if ($options['priority'] === true) {
                if ('' !== ($content = $this->getContentOfFile($file))) {
                    $this->assetCollector->addInlineStyleSheet($identifier, $content, $attributes, $options);
                }
            } else {
                $this->assetCollector->addStyleSheet($identifier, $file, $attributes, $options);
            }
        } elseif (($content = (string)$this->renderChildren()) !== '') {
            $this->assetCollector->addInlineStyleSheet($identifier, $content, $attributes, $options);
        }

        return '';
    }

    /**
     * @throws ExtensionConfigurationPathDoesNotExistException
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     * @throws \JsonException
     */
    private function handleJsonConfig(): void
    {
        $this->outputResourcePath = $this->extensionConfiguration->get(Constants::EXT_KEY, 'assets/styles/webPath');

        if (empty(self::$jsonConfig)) {
            $jsonFilePath = GeneralUtility::getFileAbsFileName($this->extensionConfiguration->get(Constants::EXT_KEY, 'assets/styles/json'));
            $file = file_get_contents($jsonFilePath);
            if ($file === false) {
                throw new \RuntimeException("No JSON for styles found under: '{$jsonFilePath}'");
            }
            self::$jsonConfig = json_decode($file, true, 512, JSON_THROW_ON_ERROR);
        }
    }

    private function getContentOfFile(string $fileName): string
    {
        if (!array_key_exists($fileName, self::$fileCache)) {
            $content = file_get_contents(GeneralUtility::getFileAbsFileName($fileName));
            if ($content === false) {
                throw new \RuntimeException("Error while loading resource: '$fileName'");
            }
            self::$fileCache[$fileName] = $content;
        }

        return self::$fileCache[$fileName];
    }
}
