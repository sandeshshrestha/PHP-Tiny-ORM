<?php

/**
 * Model.php
 */

namespace TinyORM;

use TinyORM\Query;
use TinyORM\Insert;
use TinyORM\Update;

use \com_create_guid;

/**
 * Model class
 *
 * This class is the main Model class
 *
 * **Example**
 * ```
 * class User extends Model {
 *  protected $table = 'user';
 *  protected $primaryKey = 'id';
 *  protected $fillable = ["fullName", "email"];
 * }
 * $user = new User();
 * $user->fullName = "John Doe";
 * $user->email = "johndoe@example.com";
 * $user->save();
 * $foundUser = User::find($user->getPrimaryValue());
 * $user2 = User::where("fullName", "John Doe")->limit(1)->orderBy('id')->exec();
 * ```
 */
class Model
{
  /** @var string $table Database table */
  protected $table = 'table';
  /** @var string $primaryKey Primary column name*/
  protected $primaryKey = 'id';
  /** @var Query $query Query class*/
  protected $query;
  /** @var array $fillable Table columns*/
  protected $fillable = [];

  function __construct() {
    $this->{$this->getPrimaryKey()} = '';
    foreach ($this->fillable as $value) {
      $this->{$value} = '';
    }
  }

  /**
   * getTable
   *
   * Returns the table name for this Model
   *
   * @return string
   */
  protected function getTable(): string
  {
    return  $this->table;
  }

  /**
   * getPrimaryKey
   *
   * Returns the primary key
   *
   * @return string
   */
  protected function getPrimaryKey(): string
  {
    return $this->primaryKey;
  }

  /**
   * getPrimaryValue
   *
   * Returns the value of primary key
   *
   * @return string
   */
  public function getPrimaryValue(): string
  {
    $key = $this->getPrimaryKey();

    return $this->{$key} ? $this->{$key} : '';
  }

  /**
   * find
   *
   * Find a Modal by its primary Key
   *
   * **Example**
   * ```
   * // Find user with id = '1234'
   * $user = User::find('1234');
   * ```
   *
   * @param  string $id
   *
   * @return Model
   */
  public static function find(string $id)
  {
    $obj = new static();
    $table = $obj->getTable();
    $primaryKey = $obj->getPrimaryKey();

    $query = new Query($table);
    $query->where($primaryKey, '=', $id);
    $query->limit(1);
    $query->exec();

    $rows = $query->exec();

    if (sizeof($rows) > 0) {
      return self::array2obj($rows[0], $obj);
    }

    return null;
  }


  /**
   * where
   *
   * Add where condition in this SELECT operation
   *
   * **Example**
   * ```
   * User::where('name', '=', 'John Doe')->exec();
   * ```
   *
   * @param  string $column Name of the database table column
   * @param  string $operator SQL operator =, <, >, <> etc
   * @param  string $value Value to the compared with the value in $column and $operator
   *
   * @return Model
   */
  public static function where(string $column, string $operator, string $value = ''): Model
  {
    $obj = new static();
    $table = $obj->getTable();
    $obj->query = new Query($table);
    $obj->query->where($column, $operator, $value);

    return $obj;
  }

  /**
   * limit
   *
   * Add LIMIT in this SELECT operation
   *
   * **Example**
   * ```
   * User::where('name', '=', 'John Doe')->limit(1)->exec();
   * ```
   *
   * @param  int $limit
   *
   * @return Model
   */
  public function limit(int $limit): Model
  {
    $this->query->limit($limit);

    return $this;
  }

  /**
   * orderBy
   *
   * Add ORDER BY in this SELECT operation
   *
   * **Example**
   * ```
   * User::where('name', '=', 'John Doe')->orderBy('name', 'DESC')->exec();
   * ```
   *
   * @param  string $column
   * @param  string $dir
   *
   * @return Model
   */
  public function orderBy(string $column, string $dir = 'ASC'): Model
  {
    $this->query->orderBy($column, $dir);

    return $this;
  }

  /**
   * offset
   *
   * Add OFFSET in this SELECT operation
   *
   * **Example**
   * ```
   * User::where('name', '=', 'John Doe')->offset(10)->exec();
   * ```
   *
   * @param  int $offset
   *
   * @return Model
   */
  public function offset(int $offset): Model
  {
    $this->query->offset($offset);

    return $this;
  }

  /**
   * exec
   *
   * Execute the SELECT operation on $this->query
   *
   * **Example**
   * ```
   * User::where('name', '=', 'John Doe')->exec();
   * ```
   *
   * @return array
   */
  public function exec(): array
  {
    $rows = $this->query->exec();
    $newRows = [];

    foreach ($rows as $row) {
      array_push($newRows, Model::array2obj($row, new static()));
    }

    return $newRows;
  }

  /**
   * getSQLQuery
   *
   * Return the SQL query
   *
   * **Example**
   * ```
   * echo User::where('name', '=', 'John Doe')->getSQLQuery();
   * // Returns "SELECT FROM User WHERE name = 'John Doe'"
   * ```
   *
   * @return string
   */
  public function getSQLQuery(): string
  {
    return $this->query->toString();
  }

  /**
   * _create
   *
   * Create new Model, Also does INSERT into $table
   *
   * @return void
   */
  private function _create(): void
  {
    $this->{$this->getPrimaryKey()} = uniqid('', true);
    $data = [];
    $data[$this->getPrimaryKey()] = $this->getPrimaryValue();

    foreach ($this->fillable as $value) {
      $data[$value] = $this->{$value};
    }

    $insert = new Insert($this->getTable(), $data);
    $insert->exec();
  }

  /**
   * _update
   *
   * Update Model, Also does UPDATE into $table
   *
   * @return void
   */
  private function _update(): void
  {
    $data = [];
    $id = $this->getPrimaryValue();

    foreach ($this->fillable as $value) {
      $data[$value] = $this->{$value};
    }

    $update = new Update($this->getTable(), $data);
    $update->where($this->getPrimaryKey(), $id);
    $update->exec();
  }

  /**
   * save
   *
   * This methods does create() or update() depending upon if the primaryValue
   *
   * **Example**
   * ```
   * $user = new User();
   * $user->name = 'John Doe';
   * $user->save();
   * ```
   *
   * @return void
   */
  public function save(): void
  {
    $id = $this->getPrimaryValue();
    if ($id) {
      $this->_update();
    } else {
      $this->_create();
    }
  }

  /**
   * delete
   *
   * Delete this model from database
   *
   * **Example**
   * ```
   * $user = User::find('2344');
   * $user->delete();
   * ```
   *
   * @return void
   */
  public function delete(): void
  {
    if (!$this->isDeleted) {
      $this->isDeleted = true;
      $delete = new Delete($this->getTable());
      $delete->where($this->getPrimaryKey(), '=', $this->getPrimaryValue());
      $delete->exec();
    }
  }

  /**
   * all
   *
   * Return all Models from database
   *
   * **Example**
   * ```
   * User::all();
   * ```
   *
   * @return Model
   */
  public static function all(): Model
  {
    $obj = new static();
    $table = $obj->getTable();
    $obj->query = new Query($table);

    return $obj;
  }

  /**
   * array2obj
   *
   * @param  array $arr
   * @param  mixed $obj
   *
   * @return Model
   */
  private static function array2obj(array $arr, $obj): Model
  {
    foreach ($arr as $key => $value) {
      $obj->{$key} = $value;
    }

    return $obj;
  }

  /**
   * create
   *
   * Create new Model
   *
   * **Example**
   * ```
   * User::Create([
   *  'name' => 'John Doe'
   * ]);
   * ```
   *
   * @param  array $data
   *
   * @return Model
   */
  public static function create(array $data): Model
  {
    $obj = self::array2obj($data, new static());
    $obj->save();

    return $obj;
  }
}
