<?php

namespace CH\CHCore\Conditions;

use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

class ConditionFunctionsProvider implements ExpressionFunctionProviderInterface
{
    public function getFunctions(): array
    {
        return [
            new ExpressionFunction(
                'chCheckPidInRootLine',
                static function (): void {
                    /* Not implemented, we only use the evaluator */
                },
                function ($arguments, $pids) {
                    $rootLineIds = $this->getRootline()
                        ?: $arguments['tree']->rootLineIds;
                    $pageIdsToCheck = $this->prepareGivenIds($pids);
                    return $this->checkIfIdsIntersect($rootLineIds, $pageIdsToCheck);
                }
            ),
            new ExpressionFunction(
                'chCheckParentPidInRootLine',
                static function (): void {
                    /* Not implemented, we only use the evaluator */
                },
                function ($arguments, $pids) {
                    $rootLineParentIds = $this->getRootline(true)
                        ?: $arguments['tree']->rootLineParentIds;
                    $pageIdsToCheck = $this->prepareGivenIds($pids);
                    return $this->checkIfIdsIntersect($rootLineParentIds, $pageIdsToCheck);
                }
            ),
            new ExpressionFunction(
                'chCheckChildrenInRootLine',
                static function (): void {
                    /* Not implemented, we only use the evaluator */
                },
                function ($arguments, $pids) {
                    $rootLineIds = $this->getRootline()
                        ?: $arguments['tree']->rootLineIds;
                    $pageIdsToCheck = $this->prepareGivenIds($pids);

                    return $this->checkIfCurrentPageIsWanted($pageIdsToCheck)
                        && $this->checkIfIdsIntersect($rootLineIds, $pageIdsToCheck);
                }
            ),
        ];
    }

    private function getRootline(bool $returnParents = false): array
    {
        return array_column(
            $this->getTyposcriptFrontendController()?->rootLine ?? [],
            $returnParents ? 'pid' : 'uid',
        );
    }

    private function prepareGivenIds(array|string $givenIds): array
    {
        if (is_string($givenIds)) {
            return GeneralUtility::intExplode(',', $givenIds, true);
        }

        return array_map(
            static fn ($id) => (int)$id,
            array_filter($givenIds, static fn ($id) => $id !== '')
        );
    }

    private function checkIfIdsIntersect(array $rootlineIds, array $givenIds): bool
    {
        return !empty(array_intersect($rootlineIds, $givenIds));
    }

    private function checkIfCurrentPageIsWanted(array $givenIds): bool
    {
        return !in_array($this->getTyposcriptFrontendController()?->id, $givenIds, false);
    }

    private function getTyposcriptFrontendController(): ?TypoScriptFrontendController
    {
        return $GLOBALS['TSFE'] ?? null;
    }
}
