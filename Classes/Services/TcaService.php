<?php

namespace CH\CHCore\Services;

class TcaService
{
    public function getTtContentTitle(&$parameters): void
    {
        $newTitle = '';

        if (!empty($parameters['row']['header'])) {
            $newTitle = ' - ' . $parameters['row']['header'];
        } elseif (!empty($parameters['row']['subheader'])) {
            $newTitle = ' - ' . $parameters['row']['subheader'];
        }

        $type = (is_array($parameters['row']['CType']))
            ? $parameters['row']['CType'][0]
            : $parameters['row']['CType'];

        $parameters['title'] = "[{$parameters['row']['uid']}][{$type}]$newTitle";
    }
}
