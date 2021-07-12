# Event Component

[![Latest Version](https://img.shields.io/badge/release-1.0.0-blue?style=for-the-badge)](https://www.presstify.com/pollen-solutions/event/)
[![MIT Licensed](https://img.shields.io/badge/license-MIT-green?style=for-the-badge)](LICENSE.md)
[![PHP Supported Versions](https://img.shields.io/badge/PHP->=7.4-8892BF?style=for-the-badge&logo=php)](https://www.php.net/supported-versions.php)

Pollen Solutions **Event** Component provide a PSR-14 implementation of Event Dispatching.

## Installation

```bash
composer require pollen-solutions/event
```

## Basic Usage

### Simple way of events dispatching

```php
use Pollen\Event\EventDispatcher;

// Create Dispatcher
$dispatcher = new EventDispatcher();

// Subscribe events
$dispatcher->on('event.demo', function () {
    var_dump('one');
});
$dispatcher->on('event.demo', function () {
    var_dump('two');
});

// Dispatch events
$dispatcher->trigger('event.demo');

// Output
// >> (string) 'one'
// >> (string) 'two' 
```

### Prioritize the event dispatching

```php
use Pollen\Event\EventDispatcher;

// Create Dispatcher
$dispatcher = new EventDispatcher();

// Subscribe events
$dispatcher->on('event.demo', function () {
    var_dump('one');
}, 10);
$dispatcher->on('event.demo', function () {
    var_dump('two');
}, 20);

// Dispatch events
$dispatcher->trigger('event.demo');

// Output
// >> (string) 'two'
// >> (string) 'one' 
```

### Stoppable event propagation

```php
use Pollen\Event\EventDispatcher;
use Pollen\Event\StoppableEvent;

// Create Dispatcher
$dispatcher = new EventDispatcher();

// Subscribe events
$dispatcher->on('event.demo', function (StoppableEvent $e) {
    $e->stopPropagation();
    var_dump('one');
});
$dispatcher->on('event.demo', function () {
    var_dump('two');
});

// Dispatch events
$dispatcher->trigger('event.demo');

// Output
// >> (string) 'one' 
```

### Passed arguments to event listeners

```php
use Pollen\Event\EventDispatcher;
use Pollen\Event\TriggeredEvent;

// Create Dispatcher
$dispatcher = new EventDispatcher();

// Subscribe events
$dispatcher->on('event.demo', function (TriggeredEvent $e, $arg1, $arg2) {
    var_dump('one', $arg1, $arg2);
    $e->setEventArgs(['newValue1', 'newValue2']);
});
$dispatcher->on('event.demo', function (TriggeredEvent $e, $arg1, $arg2) {
    var_dump('two', $arg1, $arg2);
});

// Dispatch events
$dispatcher->trigger('event.demo', ['value1', 'value2']);

// Output
// >> (string) 'one' 
// >> (string) 'value1'
// >> (string) 'value2'
// >> (string) 'two' 
// >> (string) 'newValue1'
// >> (string) 'newValue2'
```

### Once dispatch listener

```php
use Pollen\Event\EventDispatcher;

// Create Dispatcher
$dispatcher = new EventDispatcher();

// Subscribe events
$dispatcher->one('event.demo', function () {
    var_dump('one');
});
$dispatcher->on('event.demo', function () {
    var_dump('two');
});

// Dispatch events
// First
$dispatcher->trigger('event.demo');
// Second
$dispatcher->trigger('event.demo');

// Output
// First dispatch
// >> (string) 'one' 
// >> (string) 'two'
// Second dispatch
// >> (string) 'two'
```

### Dependencies injection container lazy loading and invokable class

```php
namespace {
use Pollen\Container\Container;
use Pollen\Event\EventDispatcher;

// Container Declaration 
$container = new Container();

class ServiceDemoNamedClass
{
    public function __invoke($e, $v)
    {
        var_dump('one >> '. $v);
    }
}
$container->add(
    'container.service1',
    ServiceDemoNamedClass::class
);

class ServiceDemoClosuredClass
{
    public function __invoke($e, $v)
    {
        var_dump('two >> '. $v);
    }
}
$container->add(
    'container.service2',
    function () {
       return new ServiceDemoClosuredClass();
    }
);

// Class Declaration
class InvokableDemoClass
{
    public function __invoke($e, $v)
    {
        var_dump('three >> '. $v);
    }
}

class InstantiableDemoClass
{
    public function __invoke($e, $v)
    {
        var_dump('four >> '. $v);
    }
}

// Create Dispatcher
$dispatcher = new EventDispatcher([], $container);

// Subscribe events
// Good practice
$dispatcher->on('event.demo', 'container.service1');
$dispatcher->on('event.demo', 'container.service2');
$dispatcher->on('event.demo', InvokableDemoClass::class);
// Increased memory usage practice
$dispatcher->on('event.demo', new InstantiableDemoClass());

// Dispatch events
$dispatcher->trigger('event.demo', ['value']);
}
```

## Advanced usage

@todo

### Event subscriber

