A PHP package to determine progress.

<p align="center">
<img src="https://i.imgur.com/uCjFWRD.jpg">
</p>

## Purpose

Walking your users through a process is important for your web app. You want your users to know what you expect of them or a list of requirements that they need to complete.

This progress package is a simple way of taking multiple steps in a process and using them to create a progression system. You can define your steps through an expressive and simple API and let this package handle the heavy lifting. The result is a delightfully easy to use steps-and-progression system.

## Simple overview

To keep things easy, this package only introduces 2 classes: `Progress` and `Step`. The `Progress` class can accept one or more `Step`s. Each `Step` class is self contained with the data it is evaluating and chainable methods that evaluate the data in a truth test format.

This package utilizes the [beberlei/assert](https://github.com/beberlei/assert) library under the hood to handle all of the truthy statements that the `Step` class uses to evaluate against the data it contains. Every assertion method from [beberlei/assert](https://github.com/beberlei/assert) is chainable on the `Step` class. You can find a list of methods below in the documentation.

## Installation

Install via composer:

```
composer require mtownsend/progress
```

*This package is designed to work with any **PHP 7.4+** application.*

## Quick start

### Using the class

Here is an example of the most basic usage:

```php
use Mtownsend\Progress\Progress;
use Mtownsend\Progress\Step;

$progress = (new Progress(
    (new Step(31, 'Age'))
        ->notEmpty()
        ->integer()
        ->between(18, 65)
))->get();

// Output:
[
  "total_steps" => 1
  "percentage_complete" => 100.0
  "percentage_incomplete" => 0.0
  "steps_complete" => 1
  "steps_incomplete" => 0
  "complete_step_names" => [
    0 => "Age"
  ]
  "incomplete_step_names" => []
]
```

The `Progress` class can accept a single `Step` class or many.

```php
use Mtownsend\Progress\Progress;
use Mtownsend\Progress\Step;

$step1 = (new Step('https://marktownsend.rocks', 'Portfolio Site'))->url();
$step2 = (new Step(4, 'Bronze Level Developer'))->integer()->between(1, 5);
$progress = (new Progress($step1, $step2));

$progress->get();

// or an array of Steps
$steps = [
    (new Step('https://marktownsend.rocks', 'Web Site'))->url(),
    (new Step('Laravel Developer', 'Profession'))->notEmpty()->contains('Laravel'),
    (new Step(true, 'Premier Member'))->boolean()->true()
];
$progress = new Progress($steps);

$progress->get();
```

Additionally, if you prefer global helpers, this package auto loads two global helper functions: `progress()` and `step()`. They instantiate each class and accept the exact same arguments. Using the global helpers can reduce your lines of code and eliminate the need to use the `Progress` and `Step` class at the top of your php script - but not all developers prefer global helpers, so it is entirely optional.

```php
$step = step('https://marktownsend.rocks', 'Web Site')->url();
$progress = progress($step)->get();

// or an array of Steps
$steps = [
    step('https://marktownsend.rocks', 'Web Site')->url(),
    step('Laravel Developer', 'Profession')->contains('Laravel'),
    step(true, 'Premier Member')->boolean()->true()
];
$progress = progress($steps)->get();
```

## The `Progress` class arguments and methods

The `Progress` class contains 1 optional constructor argument:

```php
new Progress(mixed Mtownsend\Progress\Step|array $steps);
```

The `$steps` argument can be a single instance of `\Mtownsend\Progress\Step`, multiple instances as their own argument, or an array of `Step` classes. 

However, you can instantiate the `Progress` class without supplying any arguments.

**Methods**

`add(mixed $steps)` Functions exactly the same as the constructor arguments. You can pass a single `Step`, array or multiple instances.

`get()` retrieves the overall progress of your steps. Once it has been called it simply returns the evaluated data. If you want to reuse the class for more progress calculation you will need to instantiate a new `Progress` class to do so.

`toJson()` returns the result of the `Progress` class in valid json.

`toObject()` returns the result of the `Progress` class as a standard PHP object.

**Magic Properties**

If you want to access the results of the `Progress` class without accessing the array of results directly, you can use magic properties that correspond to the key name of the result you want to retrieve.

For example consider the following:

```php
$steps = [
    (new Step(42, 'Answer To Everything'))->integer(),
    (new Step('https://github.com/mtownsend5512', 'Github Profile'))->notEmpty()->url(),
    (new Step(10, 'Open Source Packages'))->notEmpty()->integer(),
];
$progress = new Progress($steps);
$progress->get();

// Outputs:
[
  "total_steps" => 3
  "percentage_complete" => 100.0
  "percentage_incomplete" => 0.0
  "steps_completed" => 3
  "steps_incomplete" => 0
  "complete_step_names" => [
    0 => "Answer To Everything"
    1 => "Github Profile"
    2 => "Open Source Packages"
  ]
  "incomplete_step_names" => []
]

// To retrieve percentage_complete you can simply do:
$progress->percentage_complete; // 100.0
```


## The `Step` class arguments and methods

**Arguments**

The `Step` class contains 2 constructor arguments:

```php
new Step(mixed $data, string $name);
```

The `$data` argument can be anything you wish to evaluate - data from your database, form submission, or other.

`$name` is completely optional, but it can be used to keep track of the step or a label for the step. If you do not assign a name to your `Step`, it will not appear in the `complete_step_names` or `incomplete_step_names` keys, but the failure or success will still be calculated.

### Step arguments and methods

**Methods**

Since the `Step` class wraps around the [beberlei/assert](https://github.com/beberlei/assert) library, you can chain any of the `Assertion` class methods directly on the `Step` class. To see a full list you can view them [directly on the library's readme](https://github.com/beberlei/assert#list-of-assertions), but almost every method you would use has been listed below:

```php
$step->alnum()
->base64()
->between(mixed $lowerLimit, mixed $upperLimit)
->betweenExclusive(mixed $lowerLimit, mixed $upperLimit)
->betweenLength(int $minLength, int $maxLength)
->boolean()
->choice(array $choices)
->choicesNotEmpty(array $choices)
->classExists()
->contains(string $needle)
->count(array|Countable|ResourceBundle|SimpleXMLElement $countable, int $count)
->date(string $value, string $format)
->defined(mixed $constant)
->digit()
->directory()
->e164()
->email()
->endsWith(string $needle)
->eq(mixed $value)
->eqArraySubset(mixed $value)
->extensionLoaded()
->extensionVersion(string $extension, string $operator, mixed $version)
->false()
->file()
->float()
->greaterOrEqualThan(mixed $limit)
->greaterThan(mixed $limit)
->implementsInterface(mixed $class, string $interfaceName)
->inArray(array $choices)
->integer()
->integerish()
->interfaceExists()
->ip(int $flag = null)
->ipv4(int $flag = null)
->ipv6(int $flag = null)
->isArray()
->isArrayAccessible()
->isCallable()
->isCountable()
->isInstanceOf(string $className)
->isJsonString()
->isObject()
->isResource()
->isTraversable()
->keyExists(string|int $key)
->keyIsset(string|int $key)
->keyNotExists(string|int $key)
->length(int $length)
->lessOrEqualThan(mixed $limit)
->lessThan(mixed $limit)
->max(mixed $maxValue)
->maxCount(array|Countable|ResourceBundle|SimpleXMLElement $countable, int $count)
->maxLength(int $maxLength)
->methodExists(mixed $object)
->min(mixed $minValue)
->minCount(array|Countable|ResourceBundle|SimpleXMLElement $countable, int $count)
->minLength(int $minLength)
->noContent()
->notBlank()
->notContains(string $needle)
->notEmpty()
->notEmptyKey(string|int $key)
->notEq(mixed $value)
->notInArray(array $choices)
->notIsInstanceOf(string $className)
->notNull()
->notRegex(string $pattern)
->notSame(mixed $value)
->null()
->numeric()
->objectOrClass()
->phpVersion(string $operator, mixed $version)
->propertiesExist(array $properties)
->propertyExists(string $property)
->range(mixed $minValue, mixed $maxValue)
->readable()
->regex(string $pattern)
->same(mixed $value)
->satisfy(callable $callback)
->scalar(mixed $value)
->startsWith(string $needle)
->string()
->subclassOf(string $className)
->true()
->uniqueValues()
->url()
->uuid()
->version(string $version1, string $operator, string $version2)
->writeable();
```

The `Progress` class handles checking if a `Step` has passed or failed, but in the event you wish to manually check if a `Step` has failed you can call `$step->passed()` which will return a boolean.

## Error handling

In the event you do not supply any `Steps`s to a `Progress` instance and attempt to run the `->get()` method, a `\Mtownsend\Progress\Exceptions\NoStepsException` will be thrown.

## Credits

- Mark Townsend
- [Benjamin Eberlei](https://github.com/beberlei) for his assert library
- [All Contributors](../../contributors)

## Testing

You can run the tests with:

```bash
./vendor/bin/phpunit
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
