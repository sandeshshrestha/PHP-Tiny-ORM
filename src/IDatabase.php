<?php

/**
 * IDatabase.php
 */

namespace TinyORM;

use TinyORM\Query;
use TinyORM\Update;
use TinyORM\Insert;
use TinyORM\Delete;

/**
 * Database interface
 *
 * To add support for new database, the new class has to implement this interface.
 * Check MYSQLDatabase.php for example
 */

interface IDatabase
{
  /**
   * selectToString
   *
   * Method that convert TinyORM\Query object to SQL SELECT string
   *
   * @param  Query $query
   *
   * @return string
   */
  public static function selectToString(Query $query): string;
  /**
   * insertToString
   *
   * Method that convert TinyORM\Insert object to SQL INSERT string
   *
   * @param  Insert $query
   *
   * @return string
   */
  public static function insertToString(Insert $query): string;
  /**
   * updateToString
   *
   * Method that convert TinyORM\Update object to SQL Update string
   *
   * @param  Update $query
   *
   * @return string
   */
  public static function updateToString(Update $query): string;
  /**
   * deleteToString
   *
   * Method that convert TinyORM\Delete object to SQL DELETE string
   *
   * @param  Delete $query
   *
   * @return string
   */
  public static function deleteToString(Delete $query): string;
  /**
   * query
   *
   * Method that execute TinyORM\Query object
   *
   * @param  Query $query
   *
   * @return array
   */
  public static function query(Query $query): array;
  /**
   * insert
   *
   * Method that execute TinyORM\Insert object
   *
   * @param  Insert $query
   *
   * @return string
   */
  public static function insert(Insert $query): string;
  /**
   * update
   *
   * Method that execute TinyORM\Update object
   *
   * @param  Update $query
   *
   * @return bool
   */
  public static function update(Update $query): bool;
  /**
   * delete
   *
   * Method that execute TinyORM\Delete object
   *
   * @param  Delete $query
   *
   * @return bool
   */
  public static function delete(Delete $query): bool;
}
