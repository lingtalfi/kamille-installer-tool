<?php


namespace Kamille\Utils\StepTracker;


class ConsoleStepTracker extends StepTracker
{
    public function __construct()
    {
        parent::__construct();

        $this->setOnStepStartCallback(function ($stepId) {
            $label = $this->steps[$stepId];
            $info = $this->getStepNumberInfo($stepId);


            $label = "step $info: $label";
            $this->printToOutput($label, false);
        });

        $this->setOnStepStopCallback(function ($stepId, $state) {


            if ('done' === $state) {
                // todo: green
            } else {
                // todo: red
            }

            $this->printToOutput($state, true);
        });
    }






    //--------------------------------------------
    //
    //--------------------------------------------
    private function printToOutput($msg, $newLine = false)
    {
        if (false === $newLine) {
            $msg = str_replace('"', '\\"', $msg);
            exec('echo -e "' . $msg . '"');
        } else {
            echo $msg;
        }
    }


    private function getStepNumberInfo($stepId)
    {
        $i = 1;
        $found = false;
        foreach ($this->steps as $id => $label) {
            if ($id === $stepId) {
                $found = true;
                break;
            }
            $i++;
        }

        if (true === $found) {
            $n = count($this->steps);
            return $i . "/" . $n;
        } else {
            throw new \RuntimeException("step not found: $stepId");
        }
    }
}


