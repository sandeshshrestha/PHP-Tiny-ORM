<?php

/**
 * MYSQLDatabase.php
 */

namespace TinyORM;

use TinyORM\IDatabase;
use TinyORM\Query;
use TinyORM\Update;
use TinyORM\Insert;
use TinyORM\Delete;
use \mysqli;

/**
 * MYSQLDatabase class
 *
 * This class holds the logics to convert Query/Update/Insert/Delete class to actual MYSQL query string
 * This class will be extended my Database class
 */

class MYSQLDatabase implements IDatabase
{
  /** @var mysqli $conn MySql connection*/
  private static $conn = null;
  private static $table_prefix = __DATABASE_CONFIG__['table_prefix'] ?? '';

  /**
   * connection
   *
   * Return mysql connection
   *
   * @return void
   */
  private static function &connection()
  {
    $host = __DATABASE_CONFIG__['host'] ?? '';
    $username = __DATABASE_CONFIG__['username'] ?? '';
    $password = __DATABASE_CONFIG__['password'] ?? '';
    $database = __DATABASE_CONFIG__['database'] ?? '';
    $port = __DATABASE_CONFIG__['port'] ?? '3306';

    if (!$host) {
      throw new Exception('Database host not set');
    }
    if (!$username) {
      throw new Exception('Database username not set');
    }
    if (!$password) {
      throw new Exception('Database password not set');
    }
    if (!$database) {
      throw new Exception('Database name not set');
    }

    if (self::$conn == NULL) {
      self::$conn = new mysqli($host, $username, $password, $database, $port ? $port : 3306);
      if (self::$conn->connect_errno) {
        die("Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error);
      }
    }

    return self::$conn;
  }

  /**
   * selectToString
   *
   * Method that convert TinyORM\Query object to SQL SELECT string
   *
   * @param  Query $query
   *
   * @return string
   */
  public static function selectToString(Query $query): string
  {
    $table = self::$table_prefix . $query->table;
    $limit = $query->limit;
    $offset = $query->offset;
    $where = $query->where;
    $orderBys = $query->orderBys;
    $whereLength = sizeof($where);
    $orderLength = sizeof($orderBys);

    $sql = "SELECT * FROM $table";

    if ($whereLength > 0) {
      $sql = $sql . " WHERE";

      foreach ($where as $index => $value) {
        $sql = $sql . " " . self::sanitizeStringWithoutSpace($value['column']) . " " . self::sanitizeOperator($value['operator']) . " '" . self::sanitizeString($value['value']) . "'";

        if ($index + 1 < $whereLength) {
          $sql = $sql . " AND";
        }
      }
    }

    if ($orderLength > 0) {
      $sql = $sql . " ORDER BY";

      foreach ($orderBys as $index => $value) {
        $sql = $sql . " " . self::sanitizeStringWithoutSpace($value['column']) . " " . self::sanitizeStringWithoutSpace($value['dir']);

        if ($index + 1 < $orderLength) {
          $sql = $sql . ", ";
        }
      }
    }

    if ($limit) {
      $sql = $sql . " LIMIT " . self::sanitizeStringWithoutSpace($limit);
    }
    if ($offset) {
      $sql = $sql . " OFFSET " . self::sanitizeStringWithoutSpace($offset);
    }

    return $sql;
  }

  /**
   * insertToString
   *
   * Method that convert TinyORM\Insert object to SQL INSERT string
   *
   * @param  Insert $query
   *
   * @return string
   */
  public static function insertToString(Insert $query): string
  {
    $table = self::$table_prefix . $query->table;
    $data = $query->data;

    $sql = "INSERT INTO $table";
    $columnNames = "";
    $values = "";

    foreach ($data as $key => $value) {
      $columnNames = $columnNames . self::sanitizeStringWithoutSpace($key) . ", ";
      $values = $values . "'" . self::sanitizeString($value) . "', ";
    }

    $columnNames = $columnNames . " created_at, updated_at";
    $values = $values . " CURRENT_TIMESTAMP, CURRENT_TIMESTAMP";

    $sql = $sql . " (" . $columnNames . ") VALUES (" . $values . ")";

    return $sql;
  }

  /**
   * updateToString
   *
   * Method that convert TinyORM\Update object to SQL Update string
   *
   * @param  Update $query
   *
   * @return string
   */
  public static function updateToString(Update $query): string
  {
    $table = self::$table_prefix . $query->table;
    $data = $query->data;
    $where = $query->where;
    $whereLength = sizeof($where);

    $sql = "UPDATE $table SET";
    $columnNames = "";

    foreach ($data as $key => $value) {
      $columnNames = $columnNames . self::sanitizeStringWithoutSpace($key) . "='" . self::sanitizeString($value) . "', ";
    }

    $columnNames = $columnNames . " updated_at=CURRENT_TIMESTAMP";

    $sql = $sql . " " . $columnNames;

    if ($whereLength > 0) {
      $sql = $sql . " WHERE";

      foreach ($where as $index => $value) {
        $sql = $sql . " " . self::sanitizeStringWithoutSpace($value['column']) . " " . self::sanitizeOperator($value['operator']) . " '" . self::sanitizeString($value['value']) . "'";

        if ($index + 1 < $whereLength) {
          $sql = $sql . " AND";
        }
      }
    }

    return $sql;
  }

  /**
   * deleteToString
   *
   * Method that convert TinyORM\Delete object to SQL DELETE string
   *
   * @param  Delete $query
   *
   * @return string
   */
  public static function deleteToString(Delete $query): string
  {
    $table = self::$table_prefix . $query->table;
    $where = $query->where;
    $whereLength = sizeof($where);

    $sql = "DELETE FROM $table";

    if ($whereLength > 0) {
      $sql = $sql . " WHERE";

      foreach ($where as $index => $value) {
        $sql = $sql . " " . self::sanitizeStringWithoutSpace($value['column']) . " " . self::sanitizeOperator($value['operator']) . " '" . self::sanitizeString($value['value']) . "'";

        if ($index + 1 < $whereLength) {
          $sql = $sql . " AND";
        }
      }
    }

    return $sql;
  }

  /**
   * query
   *
   * Method that execute TinyORM\Query object
   *
   * @param  Query $query
   *
   * @return array
   */
  public static function query(Query $query): array
  {
    $conn = self::connection();
    $sql = self::selectToString($query);

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
      return $result->fetch_all(MYSQLI_ASSOC);
    }

    return [];
  }

  /**
   * insert
   *
   * Method that execute TinyORM\Insert object
   *
   * @param  Insert $insert
   *
   * @return string
   */
  public static function insert(Insert $insert): string
  {
    $conn = self::connection();
    $sql = self::insertToString($insert);

    $conn->query($sql);

    return $conn->insert_id;
  }

  /**
   * update
   *
   * Method that execute TinyORM\Update object
   *
   * @param  Update $update
   *
   * @return bool
   */
  public static function update(Update $update): bool
  {
    $conn = self::connection();
    $sql = self::updateToString($update);

    $conn->query($sql);

    return true;
  }

  /**
   * delete
   *
   * Method that execute TinyORM\Delete object
   *
   * @param  Delete $delete
   *
   * @return bool
   */
  public static function delete(Delete $delete): bool
  {
    $conn = self::connection();
    $sql = self::deleteToString($delete);

    $conn->query($sql);

    return true;
  }

  private static function sanitizeStringWithoutSpace($string)
  {
    return str_replace(' ', '', $string);
  }

  private static function sanitizeString($string)
  {
    return filter_var(str_replace(';', '\;', $string), FILTER_SANITIZE_SPECIAL_CHARS);
  }

  private static function sanitizeOperator($operator)
  {
    if (
      $operator == '=' ||
      $operator == '>' ||
      $operator == '<' ||
      $operator == '>=' ||
      $operator == '<=' ||
      $operator == '<>'
    ) {
      return $operator;
    }
    throw new \Exception("Invalid operator: $operator");
  }
}
