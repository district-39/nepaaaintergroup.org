<?php

namespace GoDaddy\WordPress\Plugins\SiteDesigner\Dependencies\GoDaddy\Auth;

class ObjectMapper
{
    /**
     * Iterates through JSON properties and maps those properties to the passed object.
     * - If the JSON property does not exist on the object, it is skipped.
     * - If the object property is null, or object property and JSON property have matching types, object property
     *   is set to whatever JSON property is.
     * - If the JSON property is an array and the object property is another object, it is recursively mapped in the
     *   same way. Otherwise, the object property gets the value of that array.
     * - If the object property is another object and it is not present as JSON property (or JSON property is null),
     *   it is set to null.
     *
     * @param string $json
     * @param object $object Object to map to; remember to initialize object properties, since they cannot be guessed by PHP without parsing PHPDoc.
     *
     * @throws \Exception If JSON property and object property have mismatching types.
     */
    public function mapJsonToObject(string $json, $object)
    {
        $props = json_decode($json, true);
        if (!is_array($props)) {
            throw new \Exception('Passed JSON is not an object nor an array');
        }
        $this->mapDataToObject($props, $object, is_object($object) ? get_class($object) : '');
    }

    /**
     * @param array  $props  Props to map to the object.
     * @param object $object Object to map to; remember to initialize object properties, since they cannot be guessed by PHP without parsing PHPDoc.
     * @param string $path   Used internally for error reporting.
     *
     * @throws \Exception If JSON property and object property have mismatching types.
     */
    public function mapDataToObject(array $props, $object, $path = '')
    {
        if (!is_object($object)) {
            throw new \Exception(sprintf('Expected object, got %s', gettype($object)));
        }
        $unVisited = get_object_vars($object);
        foreach ($props as $prop => $val) {
            unset($unVisited[$prop]);
            if (!property_exists($object, $prop)) {
                continue;
            }
            $objVal = $object->$prop;
            if (is_object($objVal) && is_array($val)) {
                $this->mapDataToObject($val, $objVal, $path.'->'.get_class($objVal));
                continue;
            }
            if ($val === null) {
                if (is_object($objVal)) {
                    $object->$prop = null;
                }
                continue;
            }
            if ($objVal !== null && gettype($objVal) !== gettype($val)) {
                $expect = gettype($objVal);
                if ($expect === 'object') {
                    $expect = 'instance of '.get_class($objVal);
                }
                throw new \Exception(sprintf('Mapping of property %s failed; got %s, expected %s', $path.'->'.$prop, gettype($val), $expect));
            }
            $object->$prop = $val;
        }
        foreach ($unVisited as $prop => $val) {
            if (!is_object($val)) {
                continue;
            }
            $object->$prop = null;
        }
    }
}
