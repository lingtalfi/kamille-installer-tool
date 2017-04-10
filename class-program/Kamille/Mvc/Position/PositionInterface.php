<?php


namespace Kamille\Mvc\Position;


/**
 * A position wraps widgets.
 */
interface PositionInterface
{

    public function setTemplate($templateName);

    public function render(array $variables=[]);
}