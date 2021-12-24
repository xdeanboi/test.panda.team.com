<?php

namespace PandaTeam\View;

class View
{
    private $templatePatch;
    private $extraVars = [];

    public function __construct(string $templatePatch)
    {
        $this->templatePatch = $templatePatch;
    }

    public function setVars(string $name, $value)
    {
        $this->extraVars[$name] = $value;
    }

    public function renderHtml(string $templateName, array $vars = [], int $code = 200)
    {
        http_response_code($code);

        extract($this->extraVars);
        extract($vars);

        ob_start();
        include $this->templatePatch . '/' . $templateName;
        $buffer = ob_get_contents();
        ob_end_clean();

        echo $buffer;
    }
}