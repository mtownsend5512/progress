<?php

use Mtownsend\Progress\Exceptions\NoStepsException;
use Mtownsend\Progress\Progress;
use Mtownsend\Progress\Step;
use PHPUnit\Framework\TestCase;

class ProgressTest extends TestCase
{
    /** @test */
    public function step_passes()
    {
        $result = (new Step('john@smith.com', 'Email'))->email();
        $this->assertTrue($result->passed());
    }

    /** @test */
    public function step_fails()
    {
        $result = (new Step('john smith', 'Name'))->isArray();
        $this->assertFalse($result->passed());
    }

    /** @test */
    public function progress_can_receive_single_step()
    {
        $result = new Progress((new Step(31, 'Age'))->integer());
        $this->assertEquals($result->get()['steps_complete'], 1);
    }

    /** @test */
    public function progress_can_receive_multiple_steps()
    {
        $result = new Progress(
            (new Step(31, 'Age'))->integer(),
            (new Step('John Smith', 'Name'))->string()->notEmpty()
        );
        $this->assertEquals($result->get()['steps_complete'], 2);
    }

    /** @test */
    public function progress_can_receive_array_of_steps()
    {
        $steps = [
            (new Step('https://marktownsend.rocks', 'Web Site'))->url(),
            (new Step('Laravel Developer', 'Profession'))->contains('Laravel'),
            (new Step(true, 'Premier Member'))->boolean(),
        ];
        $result = new Progress($steps);
        $this->assertEquals($result->get()['steps_complete'], 3);
    }

    /** @test */
    public function step_can_instantiate_from_helper()
    {
        $result = step(31);
        $this->assertTrue($result instanceof Step);
    }

    /** @test */
    public function progress_can_instantiate_from_helper()
    {
        $result = progress();
        $this->assertTrue($result instanceof Progress);
    }

    /** @test */
    public function retrieve_percentage_complete_through_string_casting()
    {
        $result = new Progress((new Step(31, 'Age'))->integer());
        $this->assertEquals((string) $result, 100);
    }

    /** @test */
    public function can_retrieve_result_key_from_magic_property()
    {
        $steps = [
            (new Step(42, 'Answer To Everything'))->integer(),
            (new Step('https://github.com/mtownsend5512', 'Github Profile'))->notEmpty()->url(),
            (new Step(10, 'Open Source Packages'))->notEmpty()->integer(),
        ];
        $progress = new Progress($steps);
        $progress->get();

        $this->assertEquals($progress->steps_complete, 3);
    }

    /** @test */
    public function progress_throws_no_steps_exception()
    {
        $progress = new Progress();
        $this->expectException(NoStepsException::class);
        $progress->get();
    }

    /** @test */
    public function progress_outputs_json()
    {
        $steps = [
            (new Step(31, 'Age'))->notEmpty()->integer()->between(18, 85),
            (new Step('john@smith.com', 'Confirmed Email'))->notEmpty()->string()->email(),
            (new Step(true, 'Connect Your Facebook'))->true(),
            (new Step(false, 'Connect Your PayPal'))->true(),
            (new Step(null, 'List Your First Item For Sale'))->notEmpty()->integer(),
        ];
        $result = (new Progress($steps))->toJson();
        $json = '{"total_steps":5,"percentage_complete":60,"percentage_incomplete":40,"steps_complete":3,"steps_incomplete":2,"complete_step_names":["Age","Confirmed Email","Connect Your Facebook"],"incomplete_step_names":{"3":"Connect Your PayPal","4":"List Your First Item For Sale"}}';

        $this->assertEquals($result, $json);
    }

    /** @test */
    public function progress_outputs_object()
    {
        $steps = [
            (new Step(31, 'Age'))->notEmpty()->integer()->between(18, 85),
            (new Step('john@smith.com', 'Confirmed Email'))->notEmpty()->string()->email(),
            (new Step(true, 'Connect Your Facebook'))->true(),
            (new Step(false, 'Connect Your PayPal'))->true(),
            (new Step(null, 'List Your First Item For Sale'))->notEmpty()->integer(),
        ];
        $result = (new Progress($steps))->toObject();

        $this->assertTrue($result instanceof stdClass);
    }
}
