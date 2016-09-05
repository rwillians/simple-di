# SimpleDI

Simple PHP7 DI container.

## Usage

```php
use Rwillians\SimpleDI\Container;
use Rwillians\SimpleDI\Contracts\ServiceLocatorInterface;

$container = new Container([
    'foo' => 'bar',
    'bar' => 'baz',
    'baz' => 10,
]);

$container->set('awsome.number', function (ServiceLocatorInterface $serviceLocator) {
    return $serviceLocator->get('baz') + 5;
});

echo $container->resolve('awsome.number'); // Outputs 15 (10 + 5)
```