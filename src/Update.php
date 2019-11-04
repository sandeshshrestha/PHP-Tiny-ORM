<?php
/**
 * Update.php
 */

namespace TinyORM;

use TinyORM\Database;

/**
 * Update class
 * 
 * This class holds all the information regarding UPDATE operation,
 * that will be used by Database class.
 */
class Update {
  /** @var string $table Database table*/
  public $table;
  /** @var array $data Data*/
  public $data;
  /** @var array $where Where*/
  public $where = [];

  /**
   * __construct
   *
   * @param  string $table Name of the database table where the DELETE operation will happen
   * @param  array $data Data that will be inserted into $table
   *
   * @return void
   */
  public function __construct(string $table, array $data) {
    $this->table = $table;
    $this->data = $data;
  }

  /**
   * where
   * 
   * Add where condition in this UPDATE operation
   *
   * @param  string $column Name of the database table column
   * @param  string $operator SQL operator =, <, >, <> etc
   * @param  string $value Value to the compared with the value in $column and $operator
   *
   * @return Update
   */
  public function where(string $column, string $operator, string $value = ''): Update {
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
   * Execute the constructed UPDATE operation
   *
   * @return bool
   */
  public function exec(): bool {
    return Database::update($this);
  }

  /**
   * toString
   * 
   * Return the SQL query
   *
   * @return string
   */
  public function toString(): string {
    return Database::updateToString($this);
  }
}