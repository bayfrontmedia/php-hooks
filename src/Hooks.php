<?php

namespace Bayfront\Hooks;

use Bayfront\ArrayHelpers\Arr;

class Hooks
{

    public function __destruct()
    {
        $this->doEvent('destruct');
    }

    /**
     * Returns unique ID for a given hook name and function
     *
     * This is used as a unique identifier for removeEvent() and removeFilter()
     *
     * @param string $name
     * @param mixed $function
     *
     * @return string
     */

    private function _makeId(string $name, mixed $function): string
    {

        if (is_string($function)) {

            return md5($name . $function);

        } else if (is_array($function)) {

            return spl_object_hash($function[0]) . $function[1];

        } else {

            return spl_object_hash($function);

        }

    }

    /**
     * Returns array of all hooks or for a specific hook type
     *
     * This method exists simply to reindex array keys when calling getEvents() and getFilters()
     *
     * @param string $type (events|filters)
     * @param $name (Name of hook to return, or NULL for all)
     *
     * @return array
     */

    private function _getHooks(string $type, $name): array
    {

        if ($type == 'events') {

            $hooks = self::$events;

        } else if ($type == 'filters') {

            $hooks = self::$filters;

        } else { // Just in case

            return [];

        }

        if (NULL === $name) { // Return entire array

            foreach ($hooks as $hook => $functions) {

                $hooks[$hook] = array_values($functions); // Reindex array keys

            }

            return $hooks;

        }

        if (isset($hooks[$name])) {

            return array_values($hooks[$name]); // Reindex array keys

        }

        return []; // None exist

    }

    /*
     * ############################################################
     * Events
     * ############################################################
     */

    private static array $events = []; // Hooked events

    /**
     * Adds a hook for a given event name
     *
     * NOTE: Anonymous functions are unable to be removed with removeEvent(), so use them carefully.
     *
     * Reserved names:
     *
     *     always: These hooks will always be executed whenever doEvent() is called, regardless of the name.
     *     destruct: These hooks will be executed when the script terminates.
     *
     * @param string $name (Name of event)
     * @param callable $function
     * @param int $priority (Hooks will be executed by order of priority in descending order)
     *
     * @return void
     */

    public function addEvent(string $name, callable $function, int $priority = 5): void
    {

        self::$events[$name][$this->_makeId($name, $function)] = [
            'function' => $function,
            'priority' => $priority
        ];

    }

    /**
     * Checks if any events exist for a given name
     *
     * @param string $name
     *
     * @return bool
     */

    public function hasEvent(string $name): bool
    {
        return isset(self::$events[$name]);
    }

    /**
     * Return array of all hooks for all events, or of a given event name
     *
     * @param string|null $name (Name of event)
     *
     * @return array
     */

    public function getEvents(string $name = NULL): array
    {
        return $this->_getHooks('events', $name);
    }

    /**
     * Removes hook from a given event, if existing
     *
     * Note: Hooks using anonymous functions cannot be removed using this method
     *
     * @param string $name (Name of event)
     * @param callable $function (Hook to remove)
     *
     * @return bool (Returns true if the hook existed)
     */

    public function removeEvent(string $name, callable $function): bool
    {

        $id = $this->_makeId($name, $function);

        if (isset(self::$events[$name][$id])) {

            unset(self::$events[$name][$id]);

            return true;

        }

        return false;

    }

    /**
     * Removes all hooks from a given event, if existing
     *
     * @param string $name (Name of event)
     *
     * @return bool (Returns true if the hook existed)
     */

    public function removeEvents(string $name): bool
    {

        if (isset(self::$events[$name])) {

            unset(self::$events[$name]);

            return true;

        }

        return false;

    }

    /**
     * Execute queued hooks for a given event in order of priority
     *
     * @param string $name (Name of event)
     * @param mixed $arg (Optional additional argument(s) to be passed to the functions hooked to the event)
     *
     * @return void
     */

    public function doEvent(string $name, ...$arg): void
    {

        // -------------------- Always execute “always” event --------------------

        if (isset(self::$events['always'])) {

            $events = Arr::multisort(self::$events['always'], 'priority', true); // Sort descending by priority

            foreach ($events as $event) {

                call_user_func($event['function'], $arg);

            }

        }

        if (!isset(self::$events[$name])) { // No events exist with this name

            return;

        }

        // -------------------- Execute named event --------------------

        $events = Arr::multisort(self::$events[$name], 'priority', true); // Sort descending by priority

        foreach ($events as $event) {

            call_user_func_array($event['function'], $arg);

        }

    }

    /*
     * ############################################################
     * Filters
     * ############################################################
     */

    private static array $filters = []; // Hooked filters

    /**
     * Adds a hook for a given filter name
     *
     * @param string $name (Name of filter)
     * @param callable $function
     * @param int $priority (Filters will be executed in order of priority in descending order)
     *
     * @return void
     */

    public function addFilter(string $name, callable $function, int $priority = 5): void
    {

        self::$filters[$name][$this->_makeId($name, $function)] = [
            'function' => $function,
            'priority' => $priority
        ];

    }

    /**
     * Checks if any filters exist for a given name
     *
     * @param string $name (Name of filter)
     *
     * @return bool
     */

    public function hasFilter(string $name): bool
    {
        return isset(self::$filters[$name]);
    }

    /**
     * Return array of all hooks for all filters, or of a given filter name
     *
     * @param string|null $name (Name of filter)
     *
     * @return array
     */

    public function getFilters(string $name = NULL): array
    {
        return $this->_getHooks('filters', $name);
    }

    /**
     * Removes hook from a given filter, if existing
     *
     * Note: Hooks using anonymous functions cannot be removed using this method
     *
     * @param string $name (Name of filter)
     * @param callable $function (Hook to remove)
     *
     * @return bool (Whether the hook existed)
     */

    public function removeFilter(string $name, callable $function): bool
    {

        $id = $this->_makeId($name, $function);

        if (isset(self::$filters[$name][$id])) {

            unset(self::$filters[$name][$id]);

            return true;

        }

        return false;

    }

    /**
     * Removes all hooks from a given filter, if existing
     *
     * @param string $name (Name of filter)
     *
     * @return bool (Whether any hooks existed)
     */

    public function removeFilters(string $name): bool
    {

        if (isset(self::$filters[$name])) {

            unset(self::$filters[$name]);

            return true;

        }

        return false;

    }

    /**
     * Filters value through queued filters in order of priority
     *
     * @param string $name (Name of filter)
     * @param mixed $value (Original value to be filtered)
     *
     * @return mixed (Filtered value)
     */

    public function doFilter(string $name, mixed $value): mixed
    {

        if (!isset(self::$filters[$name])) { // No filters exist, return original value

            return $value;

        }

        $filters = Arr::multisort(self::$filters[$name], 'priority', true); // Sort descending by priority

        foreach ($filters as $filter) {

            $value = call_user_func_array($filter['function'], [$value]);

        }

        return $value;

    }

}