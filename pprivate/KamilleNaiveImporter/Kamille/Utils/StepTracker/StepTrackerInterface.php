<?php


namespace Kamille\Utils\StepTracker;


interface StepTrackerInterface
{
    /**
     * @return array, array of ordered steps identifier => label
     */
    public function getList();

    public function startStep($stepId);


    /**
     * state: done|aborted
     *
     */
    public function stopStep($stepId, $state="done");
}


