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
 * @see \TYPO3\CMS\Fluid\ViewHelpers\Asset\ScriptViewHelper
 */
class ScriptViewHelper extends AbstractTagBasedViewHelper
{
    /**
     * This VH does not produce direct output, thus does not need to be wrapped in an escaping node
     *
     * @var bool
     */
    protected $escapeOutput = false;

    /**
     * Rendered children string is passed as JavaScript code,
     * there is no point in HTML encoding anything from that.
     *
     * @var bool
     */
    protected $escapeChildren = false;

    private AssetCollector $assetCollector;
    private ExtensionConfiguration $extensionConfiguration;
    private static array $jsonConfig = [];
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
        $this->registerTagAttribute('async', 'bool', 'Define that the script will be fetched in parallel to parsing and evaluation.', false);
        $this->registerTagAttribute('crossorigin', 'string', 'Define how to handle crossorigin requests.', false);
        $this->registerTagAttribute('defer', 'bool', 'Define that the script is meant to be executed after the document has been parsed.', false, true);
        $this->registerTagAttribute('integrity', 'string', 'Define base64-encoded cryptographic hash of the resource that allows browsers to verify what they fetch.', false);
        $this->registerTagAttribute('nomodule', 'bool', 'Define that the script should not be executed in browsers that support ES2015 modules.', false);
        $this->registerTagAttribute('nonce', 'string', 'Define a cryptographic nonce (number used once) used to whitelist inline styles in a style-src Content-Security-Policy.', false);
        $this->registerTagAttribute('referrerpolicy', 'string', 'Define which referrer is sent when fetching the resource.', false);
        $this->registerTagAttribute('onload', 'string', 'Define an onload JavaScript function.', false);
        $this->registerTagAttribute('src', 'string', 'Define the URI of the external resource.', false);
        $this->registerTagAttribute('type', 'string', 'Define the MIME type (usually \'text/javascript\').', false);
        $this->registerArgument(
            'identifier',
            'string',
            'Use this identifier within templates to only inject your JS once, even though it is added multiple times.',
            true
        );
        $this->registerArgument(
            'priority',
            'boolean',
            'Define whether the JavaScript should be put in the <head> tag above-the-fold or somewhere in the body part.',
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
        foreach (['async', 'defer', 'nomodule'] as $_attr) {
            if ($attributes[$_attr] ?? false) {
                $attributes[$_attr] = $_attr;
            }
        }

        $options = [
            'priority' => (bool)($this->arguments['priority'] ?? false)
        ];

        $asset = self::$jsonConfig[$identifier] ?? null;
        $src = $this->tag->getAttribute('src');
        unset($attributes['src'], $attributes['data-asset-id']);

        if ($asset !== null) {
            foreach ((array)($asset['dependencies'] ?? []) as $dependencyId => $dependencyFile) {
                $depOptions = $options;
                if (
                    !$options['priority']
                    && array_key_exists($dependencyId, $this->assetCollector->getJavaScripts(true))
                ) {
                    $depOptions['priority'] = true;
                }
                $attributes['data-asset-id'] = $dependencyId;
                $this->assetCollector->addJavaScript($dependencyId, "{$this->outputResourcePath}{$dependencyFile}", $attributes, $depOptions);
            }
            $attributes['data-asset-id'] = $identifier;
            $this->assetCollector->addJavaScript($identifier, "{$this->outputResourcePath}{$asset['file']}", $attributes, $options);
        } elseif ($src !== null) {
            $this->assetCollector->addJavaScript($identifier, $src, $attributes, $options);
        } else {
            $content = (string)$this->renderChildren();
            if ($content !== '') {
                $this->assetCollector->addInlineJavaScript($identifier, $content, $attributes, $options);
            }
        }

        return '';
    }

    /**
     * @return void
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     * @throws ExtensionConfigurationPathDoesNotExistException
     * @throws \JsonException
     */
    private function handleJsonConfig(): void
    {
        $this->outputResourcePath = $this->extensionConfiguration->get(Constants::EXT_KEY, 'assets/scripts/webPath');

        if (empty(self::$jsonConfig)) {
            $jsonFilePath = GeneralUtility::getFileAbsFileName($this->extensionConfiguration->get(Constants::EXT_KEY, 'assets/scripts/json'));
            $file = file_get_contents($jsonFilePath);
            if ($file === false) {
                throw new \RuntimeException("No JSON for scripts found under: '{$jsonFilePath}'");
            }
            self::$jsonConfig = json_decode($file, true, 512, JSON_THROW_ON_ERROR);
        }
    }
}
