<?php


namespace Kamille\Mvc\Widget;


/**
 * A widget is an element you can place on a page.
 */
interface WidgetInterface
{
    public function setVariables(array $variables);

    public function setTemplate($templateName);

    public function render();
}