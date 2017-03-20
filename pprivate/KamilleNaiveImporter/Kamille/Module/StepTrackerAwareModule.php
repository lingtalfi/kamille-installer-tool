<?php


namespace Kamille\Module;


use Kamille\Utils\StepTracker\StepTrackerAwareInterface;
use Kamille\Utils\StepTracker\StepTrackerInterface;

abstract class StepTrackerAwareModule implements ModuleInterface, StepTrackerAwareInterface
{


    /**
     * @var StepTrackerInterface $stepTracker
     */
    protected $stepTracker;

    public function setStepTracker(StepTrackerInterface $stepTracker)
    {
        $this->stepTracker = $stepTracker;
        $stepTracker->setSteps($this->getStepsList());
        return $this;
    }


    /**
     * @return array of steps id => label
     */
    abstract protected function getStepsList();


}