<?php

namespace CH\CHCore\ViewHelpers\Content;

trait UnwantedArgumentsTrait
{
    protected array $backupArguments = [];

    protected function hideUnwantedArguments(array $unwantedArguments = []): void
    {
        $this->backupArguments = $this->arguments;
        foreach ($unwantedArguments as $unwantedArgument) {
            if ($this->hasArgument($unwantedArgument)) {
                $this->arguments[$unwantedArgument] = null;
            }
        }
    }

    protected function restoreUnwantedArguments(): void
    {
        $this->arguments = $this->backupArguments;
    }
}
