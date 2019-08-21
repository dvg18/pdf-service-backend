<?php

namespace Storage;
use BaseClass;

/**
 * Interface for working with the Storage
 * @package Storage
 */
interface CommonInterface
{

    /**
     * Get the Entity by its identifier
     *
     * @param integer $id Entity identifier
     * @return BaseClass
     */
    public function getById($id);

    /**
     * Get multiple Entities by a condition
     *
     * @param string $condition SQL condition
     * @param int $begin LIMIT <b>0</b>,10
     * @param int $count LIMIT 0,<b>10</b>
     * @return BaseClass[]
     */
    public function getAll($condition = '', $begin = 0, $count = 0);

    /**
     * Get the number of Entities by a condition
     *
     * @param string $condition SQL condition
     * @return integer
     */
    public function getCounts($condition = '');

    /**
     * Create a new Entity and save it to the Storage
     *
     * @param BaseClass $entity Entity to save
     * @return BaseClass Saved entity
     */
    public function create(BaseClass $entity);

    /**
     * Save the Entity to the Storage
     *
     * @param BaseClass $entity Entity to update
     * @return boolean Is request success
     */
    public function update(BaseClass $entity);

    /**
     * Remove the Entity from the Storage by the Identifier
     *
     * @param integer $entityId Identifier
     * @return boolean Is request success
     */
    public function remove($entityId);

}
