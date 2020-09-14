<?php

namespace Mtownsend\Progress;

use Assert\Assert;
use Assert\LazyAssertionException;

/**
 * @author Mark Townsend
 *
 */

class Step
{
    /**
     * The name of the step being evaluated.
     *
     * @var string
     */
    public $name;

    /**
     * The assertion class.
     *
     * @var \Assert\LazyAssertion
     */
    protected $assertion;

    /**
     * The passed status of the step class.
     *
     * @var bool
     */
    public $passed;

    public function __construct($data, $name = '')
    {
        $this->assertion = Assert::lazy()->that($data);
        $this->name = $name;
    }

    /**
     * Evaluate the Step and determine if it passed or failed.
     * True for passed, false for failed.
     *
     * @return bool
     */
    public function passed(): bool
    {
        if (!$this->passed) {
            $this->evaluate();
        }

        return (bool) $this->passed;
    }

    /**
     * Evaluate the step and store the result in the $passed property.
     *
     * @return \Mtownsend\Progress\Step
     */
    protected function evaluate()
    {
        try {
            $this->passed = $this->assertion->verifyNow();
        } catch (LazyAssertionException $exception) {
            $this->passed = false;
        }

        return $this;
    }

    /**
     * Collect unset methods and arguments that will be stored and passed on to the Assert class.
     *
     * @return \Mtownsend\Progress\Step
     */
    public function __call($name, $arguments)
    {
        if (empty($arguments)) {
            $this->assertion->$name();
        } else {
            $this->assertion->$name(...$arguments);
        }

        return $this;
    }

    /**
     * Return the name of the step.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
}
