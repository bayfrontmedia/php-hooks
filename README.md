## PHP hooks

An easy to use hooks library for managing events and filters.

- [License](#license)
- [Author](#author)
- [Requirements](#requirements)
- [Installation](#installation)
- [Usage](#usage)

## License

This project is open source and available under the [MIT License](https://github.com/bayfrontmedia/php-array-helpers/blob/master/LICENSE).

## Author

John Robinson, [Bayfront Media](https://www.bayfrontmedia.com)

## Requirements

* PHP >= 7.1.0

## Installation

```
composer require bayfrontmedia/php-hooks
```

## Usage

### Start using hooks

```
use Bayfront\Hooks\Hooks;

$hooks = new Hooks();
```

### Public methods

**Events**

- [addEvent](#addevent)
- [hasEvent](#hasevent)
- [getEvents](#getevents)
- [removeEvent](#removeevent)
- [removeEvents](#removeevents)
- [doEvent](#doevent)

**Filters**

- [addFilter](#addfilter)
- [hasFilter](#hasfilter)
- [getFilters](#getfilters)
- [removeFilter](#removefilter)
- [removeFilters](#removefilters)
- [doFilter](#dofilter)

<hr />

### addEvent

**Description:**

Adds a hook for a given event name. 

**NOTE:** Anonymous functions are unable to be removed with `removeEvent()`, so use them carefully.

**Parameters:**

- `$name` (string): Name of event
- `$function` (callable)
- `$priority = 5` (int): Hooks will be executed by order of priority in descending order

Reserved names:

- `always`: These hooks will always be executed whenever `doEvent()` is called, regardless of the name.
- `destruct`: These hooks will be executed when the script terminates.

**Returns:**

- (void)

**Examples:**

Anonymous function

```
$hooks->addEvent('name', function($name) {

    echo 'My name is ' . $name;

});
```

Named function

```
function my_name($name) {

    echo 'My name is ' . $name;

}

$hooks->addEvent('name', 'my_name');
```

Inside class scope

```
use Bayfront\Hooks\Hooks;

class MyClass {

    protected $hooks;

    public function __construct(Hooks $hooks) {

        $this->hooks = $hooks;

        $this->hooks->addEvent('name', [$this, 'my_name']);

    }

    public function my_name($name) {

        echo 'My name is ' . $name;

    }
}

$my_class = new MyClass($hooks);
```

Use variables from outside scope

```
$prefix = 'My name is ';

$hooks->addEvent('name', function($name) use ($prefix) {

    echo $prefix . $name;

});
```

<hr />

### hasEvent

**Description:**

Checks if any events exist for a given name. 

**Parameters:**

- `$name` (string): Name of event

**Returns:**

- (bool)

**Example:**

```
if ($hooks->hasEvent('name')) {
    // Do something
}
```

<hr />

### getEvents

**Description:**

Return array of all hooks for all events, or of a given event name. 

**Parameters:**

- `$name = NULL` (string|null): Name of event

**Returns:**

- (array)

**Example:**

```
print_r($hooks->getEvents()); // Returns all hooks for all events

print_r($hooks->getEvents('name')); // Returns all hooks for "name" event
```

<hr />

### removeEvent

**Description:**

Removes hook from a given event, if existing. 

**NOTE:** Hooks using anonymous functions cannot be removed using this method.

**Parameters:**

- `$name` (string): Name of event
- `$function` (callable): Hook to remove

**Returns:**

- (bool): Returns `true` if the hook existed

**Example:**

```
$hooks->removeEvent('name', 'my_name');
```

To remove a hook for a function from within a class scope, the `$function` parameter must be an array whose first value is an instance of the class, and second value is the name of the function within the class:

```
$hooks->removeEvent('name', [$my_class, 'my_name']);
```
<hr />

### removeEvents

**Description:**

Removes all hooks from a given event, if existing. 

**Parameters:**

- `$name` (string): Name of event

**Returns:**

- (bool): Returns `true` if the hook existed

**Example:**

```
$hooks->removeEvents('name');
```

<hr />

### doEvent

**Description:**

Execute queued hooks for a given event in order of priority. 

**Parameters:**

- `$name` (string): Name of event
- `...$arg` (mixed): Optional additional argument(s) to be passed to the functions hooked to the event

**Returns:**

- (void)

**Throws:**

- `Bayfront\Hooks\EventException`

**Example:**

```
use Bayfront\Hooks\EventException;

try {

    $hooks->doEvent('name', 'John');

} catch (EventException $e) {
    die($e->getMessage());
}
```

<hr />

### addFilter

**Description:**

Adds a hook for a given filter name. 

**Parameters:**

- `$name` (string): Name of filter
- `$function` (callable)
- `$priority = 5` (int): Filters will be executed in order of priority in descending order

**Returns:**

- (void)

**Examples:**

Anonymous function

```
$hooks->addFilter('name', function($name) {

    return strtoupper($name);

});
```

Named function

```
function uppercase($name) {

    return strtoupper($name);

}

$hooks->addFilter('name', 'uppercase');
```

Inside class scope

```
use Bayfront\Hooks\Hooks;

class MyClass {

    protected $hooks;

    public function __construct(Hooks $hooks) {

        $this->hooks = $hooks;

        $this->hooks->addFilter('name', [$this, 'uppercase']);

    }

    public function uppercase($name) {

        return strtoupper($name);

    }
}

$my_class = new MyClass($hooks);
```

Use variables from outside scope

```
$prefix = 'My name is ';

$hooks->addFilter('name', function($name) use ($prefix) {

    return strtoupper($prefix . $name);

});
```

<hr />

### hasFilter

**Description:**

Checks if any filters exist for a given name. 

**Parameters:**

- `$name` (string): Name of filter

**Returns:**

- (bool)

**Example:**

```
if ($hooks->hasFilter('name')) {
    // Do something
}
```

<hr />

### getFilters

**Description:**

Return array of all hooks for all filters, or of a given filter name.

**Parameters:**

- `$name = NULL` (string|null): Name of filter

**Returns:**

- (array)

**Example:**

```
print_r($hooks->getFilters()); // Returns all hooks for all filters

print_r($hooks->getFilters('name')); // Returns all hooks for "name" filter
```

<hr />

### removeFilter

**Description:**

Removes hook from a given filter, if existing. 

**NOTE:** Hooks using anonymous functions cannot be removed using this method

**Parameters:**

- `$name` (string): Name of filter
- `$function` (callable): Hook to remove

**Returns:**

- (bool): Returns `true` if the hook existed

**Example:**

```
$hooks->removeFilter('name', 'uppercase');
```

To remove a hook for a function from within a class scope, the `$function` parameter must be an array whose first value is an instance of the class, and second value is the name of the function within the class:

```
$hooks->removeFilter('name', [$my_class, 'uppercase']);
```

<hr />

### removeFilters

**Description:**

Removes all hooks from a given filter, if existing.

**Parameters:**

- `$name` (string): Name of filter

**Returns:**

- (bool): Returns `true` if the hook existed

**Example:**

```
$hooks->removeFilters('name');
```

<hr />

### doFilter

**Description:**

Filters value through queued filters in order of priority.

**Parameters:**

- `$name` (string): Name of filter
- `$value` (mixed): Original value to be filtered

**Returns:**

- (mixed): Filtered value

**Throws:**

- `Bayfront\Hooks\FilterException`

**Example:**

```
use Bayfront\Hooks\FilterException;

try {

    echo $hooks->doFilter('name', 'John');

} catch (FilterException $e) {
    die($e->getMessage());
}
```