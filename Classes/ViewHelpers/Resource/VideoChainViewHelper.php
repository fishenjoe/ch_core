<?php

namespace CH\CHCore\ViewHelpers\Resource;

use TYPO3\CMS\Core\Resource\FileInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Resource\FileCollector;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Get the video resources from our custom fallback and sources chain.
 * Mainly used in our `T3Video` partial.
 */
class VideoChainViewHelper extends AbstractViewHelper
{
    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument(
            'file',
            'object',
            'The file in question',
            false
        );
    }

    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ): array {
        $file = $arguments['file'] ?? $renderChildrenClosure();

        if (empty($file) || !($file instanceof FileInterface)) {
            return [];
        }

        return self::resolve($file);
    }

    private static function resolve(FileInterface $file): array
    {
        $structure = [
            'file' => $file,
            'fallback' => [],
            'sources' => [],
        ];

        $collectorFallback = self::createFileCollector();
        $collectorFallback->addFilesFromRelation(
            'sys_file_reference',
            'ch_video_fallback',
            $file->toArray()
        );

        /** @var FileInterface $fileFallback */
        foreach ($collectorFallback->getFiles() as $fileFallback) {
            $structure['fallback'][] = self::resolve($fileFallback);
        }

        $collectorSources = self::createFileCollector();
        $collectorSources->addFilesFromRelation(
            'sys_file_reference',
            'ch_video_sources',
            $file->toArray()
        );

        /** @var FileInterface $fileSource */
        foreach ($collectorSources->getFiles() as $fileSource) {
            $structure['sources'][] = self::resolve($fileSource);
        }

        return $structure;
    }

    private static function createFileCollector(): FileCollector
    {
        return GeneralUtility::makeInstance(FileCollector::class);
    }
}
