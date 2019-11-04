<?php
/**
 * Insert.php
 */

namespace TinyORM;

use TinyORM\Database;

/**
 * Insert class
 * 
 * This class holds all the information regarding INSERT operation,
 * that will be used by Database class.
 * 
 * **Example**
 * ```
 * $data = [
 *  "name": "John Doe"
 * ];
 * $insertOperation = new Insert('User', $data);
 * $insertOperation->exec();
 * ```
 */

class Insert {
  /** @var string $table Database table */
  public $table;
  /** @var array $data Column data*/
  public $data;

  /**
   * __construct
   *
   * @param  string $table Name of the database table where the INSERT operation will happen
   * @param  array $data Data that will be inserted into $table
   *
   * @return void
   */
  public function __construct(string $table, array $data) {
    $this->table = $table;
    $this->data = $data;
  }

  /**
   * exec
   * 
   * Execute the constructed INSERT operation
   *
   * @return string
   */
  public function exec(): string {
    return Database::insert($this);
  }

  /**
   * toString
   * 
   * Return the SQL query
   * 
   * **Example**
   * ```
   * $data = [
   *  "name": "John Doe"
   * ];
   * $insertOperation = new Insert('User', $data);
   * echo $insertOperation->toString();
   * // Returns "INSERT INTO User (name) VALUES ('John Doe')"
   * ```
   *
   * @return string
   */
  public function toString(): string {
    return Database::insertToString($this);
  }
}