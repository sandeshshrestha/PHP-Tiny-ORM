<?php
/**
 * Query.php
 */

namespace TinyORM;

use TinyORM\Database;

/**
 * Query class
 * 
 * This class holds all the information regarding SELECT operation,
 * that will be used by Database class.
 */
class Query {
  /**
   * @var string $table Database table */
  public $table;
  /** @var int $limit Query LIMIT */
  public $limit;
  /** @var int $offset Query OFFSET */
  public $offset = 0;
  /** @var array $where Query WHERE conditions*/
  public $where = [];
  /** @var array $orderBys Query ORDER BY*/
  public $orderBys = [];

  /**
   * __construct
   *
   * @param  string $table Name of the database table where the SELECT operation will happen
   *
   * @return void
   */
  public function __construct(string $table) {
    $this->table = $table;
  }

  /**
   * where
   * 
   * Add where condition in this SELECT operation
   *
   * @param  string $column Name of the database table column
   * @param  string $operator SQL operator =, <, >, <> etc
   * @param  string $value Value to the compared with the value in $column and $operator
   *
   * @return Query
   */
  public function where(string $column, string $operator, string $value = ''): Query {
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
   * orderBy
   * 
   * Add ORDER BY in this SELECT operation
   *
   * @param  string $column
   * @param  string $dir
   *
   * @return Query
   */
  public function orderBy(string $column, string $dir = 'ASC'): Query {
    array_push($this->orderBys, [
      'column' => $column,
      'dir' => $dir,
    ]);

    return $this;
  }

  /**
   * limit
   * 
   * Add LIMIT in this SELECT operation
   *
   * @param  int $limit
   *
   * @return Query
   */
  public function limit(int $limit): Query {
    $this->limit = $limit;

    return $this;
  }

  /**
   * offset
   * 
   * Add OFFSET in this SELECT operation
   *
   * @param  int $offset
   *
   * @return Query
   */
  public function offset(int $offset): Query {
    $this->offset = $offset;

    return $this;
  }

  /**
   * exec
   * 
   * Execute the constructed SELECT operation
   *
   * @return array
   */
  public function exec(): array {
    return Database::query($this);
  }

  /**
   * toString
   * 
   * Return the SQL query
   *
   * @return string
   */
  public function toString(): string {
    return Database::selectToString($this);
  }
}