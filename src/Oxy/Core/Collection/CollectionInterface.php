<?php
/**
 * @category Oxy
 * @package  Oxy\Core\Collection
 * @author   Tomas Bartkus <to.bartkus@gmail.com>
 */

namespace Oxy\Core\Collection;

/**
 * Interface CollectionInterface
 *
 * @category Oxy
 * @package  Oxy\Core\Collection
 * @author   Tomas Bartkus <to.bartkus@gmail.com>
 */
interface CollectionInterface extends \Countable, \IteratorAggregate
{
    /**
     * Set collection items
     *
     * @param Array $collectionItems
     */
    public function setItems(Array $collectionItems);

    /**
     * Add a value into the collection
     *
     * @param mixed $value
     *
     * @throws \InvalidArgumentException when wrong type
     */
    public function add($value);

    /**
     * Set index's value
     *
     * @param string $index
     * @param mixed $value
     *
     * @throws \OutOfRangeException
     * @throws \InvalidArgumentException
     */
    public function set($index, $value);

    /**
     * Remove a value from the collection
     *
     * @param integer $index index to remove
     *
     * @throws \OutOfRangeException if index is out of range
     */
    public function remove($index);

    /**
     * Return value at index
     *
     * @param integer $index
     *
     * @return mixed
     *
     * @throws \OutOfRangeException
     */
    public function get($index);

    /**
     * Determine if index exists
     * @param integer $index
     *
     * @return boolean
     */
    public function exists($index);

    /**
     * Return last collection item
     *
     * @return mixed
     */
    public function getLast();

    /**
     * Pop last collection item
     *
     * @return mixed
     */
    public function popLast();

    /**
     * Shift first collection item
     *
     * @return mixed
     */
    public function shiftFirst();

    /**
     * Return first collection item
     *
     * @return mixed
     */
    public function getFirst();

    /**
     * Convert collection to array
     * 
     * @return array
     */
    public function toArray();

    /**
     * Clear collection
     *
     * @return void
     */
    public function clear();
}