<?php
/**
 * Delete.php
 */

namespace TinyORM;

use TinyORM\Database;

/**
 * Delete class
 * 
 * This class holds all the information regarding DELETE operation,
 * that will be used by Database class.
 * 
 * **Example**
 * ```
 * $deleteOperation = new Delete('User');
 * $deleteOperation->where('id', '=', '1');
 * $deleteOperation->exec();
 * ```
 */
class Delete {
  /** @var string $table Database table */
  public $table;
  /** @var array $where Where*/
  public $where = [];

  /**
   * __construct
   *
   * @param  string $table Name of the database table where the DELETE operation will happen
   *
   * @return void
   */
  public function __construct(string $table) {
    $this->table = $table;
  }

  /**
   * where
   * 
   * Add where condition in this DELETE operation
   *
   * @param  string $column Name of the database table column
   * @param  string $operator SQL operator =, <, >, <> etc
   * @param  string $value Value to the compared with the value in $column and $operator
   *
   * @return Delete
   */
  public function where(string $column, string $operator, string $value = ''): Delete {
    if (!$value) {
      $value = $operator;
      $operator = '=';
    }
    
    array_push($this->where, [
      'column' => $column,
      'operator' => $operator,
      'value' => $value,
    ]);

    return $this;
  }

  /**
   * exec
   * 
   * Execute the constructed DELETE operation
   *
   * @return bool
   */
  public function exec(): bool {
    return Database::delete($this);
  }

  /**
   * toString
   * 
   * Return the SQL query
   * 
   * **Example**
   * ```
   * $deleteOperation = new Delete('User');
   * $deleteOperation->where('id', '=', '1');
   * echo $deleteOperation->toString();
   * // Returns "DELETE FROM User WHERE id = '1'"
   * ```
   *
   * @return string
   */
  public function toString(): string {
    return Database::deleteToString($this);
  }
}