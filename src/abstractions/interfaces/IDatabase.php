<?php
/**
 * Created by PhpStorm.
 * User: Gumacs
 * Date: 2016-12-14
 * Time: 04:47 AM
 */

namespace Framework\Abstractions\Interfaces;


interface IDatabase
{

    /**Creates the database instance with the given config
     * @param array $config
     */
    public static function Database($config = array());

    /**Commits the current transaction
     */
    public function commit();

    /**Rolls back the previous commit, and all the queries with it
     */
    public function rollBack();

    /**Switches the autocommit mode
     * @param bool $bool
     */
    public function switchAutoCommit($bool);

    /**Executes the given sql query
     * @param string $query
     */
    public function executeQuery($query);

    /**Executes the given sql statement with the given params
     * @param string $query
     * @param array $params
     */
    public function executeQueryStatement($query, $params);

    /**Executes all the earlier added queries
     */
    public function executeQueries();

    /**Add query
     * @param string $query
     */
    public function addQuery($query);

    /**Add query statement
     * @param $query
     * @param $params
     */
    public function addQueryStatement($query, $params);

    /**Escapes the given string
     * @param $string
     */
    public function escapeString($string);

    /**Gets the last executed querie's result
     * @return IDatabaseResult
     */
    public function getLastResult();

    /**Gets the last executed querie's insert id
     * @return mixed
     */
    public function getLastInsertId();

    /**Gets the last executed querie's affected rows
     * @return int
     */
    public function getLastAffectedRows();

    /**Gets the last executed querie's errors
     * @return array
     */
    public function getLastErrors();

    /**Gets all executed queries result as an array
     * @return array
     */
    public function getAllResult();

    /**Gets all executed queries' insert ids in an array
     * @return array
     */
    public function getAllInsertId();

    /**Gets all executed queries' affected rows in an array
     * @return array
     */
    public function getAllAffectedRows();

    /**Gets all executed queries' errors in an array
     * @return array
     */
    public function getAllErrors();
}