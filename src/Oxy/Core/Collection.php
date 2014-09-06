<?php
/**
 * @category Oxy
 * @package  Oxy\Core
 * @author   Tomas Bartkus <to.bartkus@gmail.com>
 */

namespace Oxy\Core;

use \Oxy\Core\Collection\CollectionInterface;

/**
 * Class Collection
 *
 * @category Oxy
 * @package  Oxy\Core
 * @author   Tomas Bartkus <to.bartkus@gmail.com>
 */
class Collection implements CollectionInterface
{
    /**
     * Value type
     *
     * @var Mixed
     */
    protected $_valueType;

    /**
     * Whether or not the members or this collection are of a "basic" type
     *
     * Basic types are anything that has a matching "is_*" function listed
     * in {@link http://us2.php.net/manual/en/ref.var.php}. Anything else is
     * assumed to be the name of a class or interface.
     *
     * @var boolean
     */
    protected $_isBasicType = false;

    /**
     * The "is_*" function that validates {@link $_valueType}
     *
     * This property is only used if {@link $_isBasicType} == true
     * @var string
     */
    protected $_validateFunc;

    /**
     * Collection
     *
     * @var Array
     */
    protected $_collection = array();

    /**
     * Construct a new typed collection
     *
     * @param string $valueType collection value type
     * @param array $collectionItems initial items
     */
    public function __construct($valueType, Array $collectionItems = array())
    {
        $this->_valueType = $valueType;
        if (function_exists("is_$valueType")) {
            $this->_isBasicType = true;
            $this->_validateFunc = "is_$valueType";
        }
        $this->setItems($collectionItems);
    }

    /**
     * Set collection items
     *
     * @param Array $collectionItems
     */
    public function setItems(Array $collectionItems)
    {
        if (!empty($collectionItems)) {
            foreach ($collectionItems as $item) {
                $this->add($item);
            }
        }
    }

    /**
     * Add a value into the collection
     *
     * @param mixed $value
     *
     * @throws \InvalidArgumentException when wrong type
     */
    public function add($value)
    {
        if (!$this->isValidType($value)) {
            $currentType = get_class($value);
            throw new \InvalidArgumentException(
                "Trying to add a value of wrong type {$this->_valueType} {$currentType}"
            );
        }

        $this->_collection[] = $value;
    }

    /**
     * Set index's value
     *
     * @param string $index
     * @param mixed $value
     *
     * @throws \OutOfRangeException
     * @throws \InvalidArgumentException
     */
    public function set($index, $value)
    {
        if (!$this->isValidType($value)) {
            throw new \InvalidArgumentException('Trying to add a value of wrong type: "' . $this->_valueType . '" expected, but "' . get_class($value) . '" was given.');
        }
        $this->_collection[$index] = $value;
    }

    /**
     * Remove a value from the collection
     *
     * @param integer $index index to remove
     *
     * @throws \OutOfRangeException if index is out of range
     */
    public function remove($index)
    {
        if (!isset($this->_collection[$index])) {
            throw new \OutOfRangeException('Index out of range');
        }

        unset($this->_collection[$index]);
    }

    /**
     * Return value at index
     *
     * @param integer $index
     *
     * @return mixed
     *
     * @throws \OutOfRangeException
     */
    public function get($index)
    {
        if (!isset($this->_collection[$index])) {
            throw new \OutOfRangeException('Index ' . $index . ' out of range');
        }
        return $this->_collection[$index];
    }

    /**
     * Determine if index exists
     *
     * @param integer $index
     *
     * @return boolean
     */
    public function exists($index)
    {
        if (!isset($this->_collection[$index])) {
            return false;
        }
        return true;
    }

    /**
     * Return last collection item
     *
     * @return mixed
     */
    public function getLast()
    {
        $lookup = $this->_collection;
        return array_pop($lookup);
    }

    /**
     * Pop last collection item
     *
     * @return mixed
     */
    public function popLast()
    {
        return array_pop($this->_collection);
    }

    /**
     * Shift first collection item
     *
     * @return mixed
     */
    public function shiftFirst()
    {
        return array_shift($this->_collection);
    }

    /**
     * Return first collection item
     *
     * @return mixed
     */
    public function getFirst()
    {
        $lookup = $this->_collection;
        return array_shift($lookup);
    }

    /**
     * Return count of items in collection
     * Implements countable
     *
     * @return integer
     */
    public function count()
    {
        return count($this->_collection);
    }

    /**
     * Convert collection to array
     *
     * @return array
     */
    public function toArray()
    {
        if ($this->_isBasicType) {
            return $this->_collection;
        } else {
            $collectionArray = array();
            foreach ($this->_collection as $key => $element) {
                // If this is collection of non-basic elements,
                // check if that element knows how to convert itself into array
                if (method_exists($element, 'toArray')) {
                    $collectionArray[$key] = $element->toArray();
                } else {
                    $collectionArray[$key] = $element;
                }
            }

            return $collectionArray;
        }
    }

    /**
     * Determine if this value can be added to this collection
     *
     * @param mixed $value
     *
     * @return boolean
     */
    public function isValidType($value)
    {
        if ($this->_isBasicType) {
            $validateFunc = $this->_validateFunc;
            return $validateFunc($value);
        } else {
            // instanceof works on interfaces as well as classes.
            // It also checks the entire inheritance chain
            return ($value instanceof $this->_valueType);
        }
    }

    /**
     * Return an iterator
     * Implements IteratorAggregate
     *
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->_collection);
    }

    /**
     * Clear collection
     *
     * @return void
     */
    public function clear()
    {
        $this->_collection = array();
    }

    /**
     * Change collection key
     */
    public function changeKey($oldKey, $newKey)
    {
        // Save value in temp value, for those cases when oldKey matches newKey
        $value = $this->get($oldKey);
        $this->remove($oldKey);
        $this->set($newKey, $value);
    }

    /**
     * Change multiple collection keys
     */
    public function changeMultipleKeys($keysMap)
    {
        foreach ($keysMap as $oldKey => $newKey) {
            $this->changeKey($oldKey, $newKey);
        }
    }
}