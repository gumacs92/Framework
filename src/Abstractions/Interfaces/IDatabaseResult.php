<?php
/**
 * Created by PhpStorm.
 * User: Gumacs
 * Date: 2016-12-14
 * Time: 04:48 AM
 */

namespace Framework\Abstractions\Interfaces;


interface IDatabaseResult
{
    /**Whether the query was succesful or not
     * @return bool
     */
    public function isSuccess();

    /**Get the errors
     * @return array
     */
    public function getErrors();

    /**Get the number of affected rows
     * @return int
     */
    public function getAffectedRows();

    /**Get the number of rows
     * @return int
     */
    public function getNumberOfRows();

    /**get the insert Id or -1 if there was no insert
     * @return int
     */
    public function getInsertId();

    /**Yields the results as the object of the named class
     * @param string $class_name
     * @return object|null
     */
    public function yieldObjectArray($class_name);

    /**Gets the results as the object of the named class in one array
     * @param string $class_name
     * @return array
     */
    public function fetchObjectArray($class_name);

    /**Yields the results as an assoc array
     * @return array
     */
    public function yieldAssocArray();

    /**Gets the results as an assoc array in one array
     * @return array
     */
    public function fetchAssocArray();

    /**Yields the results as an simple array
     * @return array
     */
    public function yieldArray();

    /**Gets the results as an simple array in one array
     * @return array
     */
    public function fetchArray();
}