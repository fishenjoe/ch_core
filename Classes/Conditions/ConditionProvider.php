<?php

namespace CH\CHCore\Conditions;

use TYPO3\CMS\Core\ExpressionLanguage\AbstractProvider;

class ConditionProvider extends AbstractProvider
{
    public function __construct()
    {
        $this->expressionLanguageProviders = [
            ConditionFunctionsProvider::class
        ];
    }
}
