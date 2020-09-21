<?php

namespace Mtownsend\Progress;

use Mtownsend\Progress\Exceptions\NoStepsException;
use Mtownsend\Progress\Step;

/**
 * @author Mark Townsend
 *
 */

class Progress
{
    /**
     * The overall percentage of completion for the Progress class.
     *
     * @var integer
     */
    protected $percentageComplete = 0;

    /**
     * The number of steps in the Progress class that passed.
     *
     * @var integer
     */
    protected $stepsPassed = 0;

    /**
     * The progress result array.
     *
     * @var array
     */
    public $result = [];

    /**
     * The steps used to determine overall progress
     * An array of Mtownsend\Progress\Step objects
     *
     * @var array
     */
    protected $steps;

    /**
     * Instantiate the Progress class.
     *
     * @param mixed $steps array or single instance of \Mtownsend\Progress\Step
     */
    public function __construct(...$steps)
    {
        if (!empty($steps)) {
            $this->add(...$steps);
        }
    }

    /**
     * Add a step to the process.
     *
     * @param mixed $steps array or single instance of \Mtownsend\Progress\Step
     *
     * @return \Mtownsend\Progress\Progress
     */
    public function add(...$steps)
    {
        $steps = array_flatten($steps);

        // Only accepts class instance of Step
        foreach ($steps as $step) {
            if (!($step instanceof Step)) {
                continue;
            }
            $this->steps[] = $step;
        }

        return $this;
    }

    /**
     * Retrieve the $result array property.
     *
     * @return array
     */
    public function get(): array
    {
        if (empty($this->result)) {
            return $this->evaluate();
        }

        return $this->result;
    }

    /**
     * Evaluate all of the steps to determine progress.
     *
     * @return array
     */
    protected function evaluate(): array
    {
        if (empty($this->steps)) {
            throw new NoStepsException('No steps have been supplied to the Progress class');
        }

        foreach ($this->steps as $key => $step) {
            $this->steps[$key] = [
                'name' => $step->getName() ?? null,
                'passed' => $step->passed()
            ];
        }

        $this->result = [
            'total_steps' => $this->countSteps(),
            'percentage_complete' => $this->percentageComplete(),
            'percentage_incomplete' => max(100 - $this->percentageComplete(), 0),
            'steps_complete' => $this->countStepsPassed(),
            'steps_incomplete' => $this->countSteps() - $this->countStepsPassed(),
            'complete_step_names' => $this->passedStepNames(),
            'incomplete_step_names' => $this->failedStepNames()
        ];

        return $this->result;
    }

    /**
     * Retrieve a key from the $result property.
     *
     * @param  string $name Name of the key inside the $result property
     * @return mixed
     */
    public function __get(string $name)
    {
        if (empty($this->result) || !isset($this->result[$name])) {
            return null;
        }

        return $this->result[$name];
    }

    /**
     * The progress percentage complete.
     *
     * @return double
     */
    public function percentageComplete()
    {
        if ($this->percentageComplete) {
            return $this->percentageComplete;
        }

        $totalSteps = $this->countSteps();
        if ($totalSteps <= 0) {
            return 0;
        }

        $this->percentageComplete = round($this->countStepsPassed() / ($totalSteps / 100), 2);

        return $this->percentageComplete;
    }

    /**
     * The number of steps successfully completed.
     *
     * @return int
     */
    public function countStepsPassed(): int
    {
        if ($this->stepsPassed) {
            return $this->stepsPassed;
        }

        $this->stepsPassed = array_sum(array_map(function ($item) {
            return $item['passed'] ? 1 : 0;
        }, $this->steps));

        return $this->stepsPassed;
    }

    /**
     * Count the number of steps
     *
     * @return int
     */
    private function countSteps(): int
    {
        return count($this->steps);
    }

    /**
     * Return an array of step names that have passed.
     *
     * @return array
     */
    private function passedStepNames(): array
    {
        return array_filter(array_map(function ($item) {
            return $item['passed'] ? $item['name'] : null;
        }, $this->steps));
    }

    /**
     * Return an array of step names that have failed.
     *
     * @return array
     */
    private function failedStepNames(): array
    {
        return array_filter(array_map(function ($item) {
            return $item['passed'] ? null : $item['name'];
        }, $this->steps));
    }

    /**
     * Return an array of the steps data.
     *
     * @return array
     */
    public function toArray(): array
    {
        return $this->get();
    }

    /**
     * Return an object of the steps data.
     *
     * @return stdClass
     */
    public function toObject(): object
    {
        return json_decode($this->toJson());
    }

    /**
     * Return a json string of the steps data.
     *
     * @return string
     */
    public function toJson(): string
    {
        return json_encode($this->get());
    }

    /**
     * Return a json string of the steps data.
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->get()['percentage_complete'];
    }

    /**
     * Return an array of the steps data.
     *
     * @return array
     */
    public function __invoke(): array
    {
        return $this->get();
    }
}
