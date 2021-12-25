<?php

/**
 * Validator.php
 */

namespace TinyORM;

use TinyORM\Query;

/**
 * Validator class
 *
 * This class holds utils to validate data.
 *
 * **Example**
 * ```
 * $data = [
 *  "name" => "John Doe"
 * ];
 *
 * $config = [
 *  "name" => [
 *    "require" => true
 *  ]
 * ];
 * $validator = new Validator($data, $config);
 * echo $validator->isValid(); // Returns 'true'
 * ```
 *
 */
class Validator
{
  /** @var array $data Data*/
  protected $data;
  /** @var array $config Config*/
  protected $config;
  /** @var array $errors Errors*/
  protected $errors = [];

  /**
   * __construct
   *
   * @param  array $data
   * @param  array $config
   *
   * @return void
   */
  public function __construct(array $data, array $config)
  {
    $this->data = $data;
    $this->config = $config;

    $this->validate();
  }

  /**
   * validate
   *
   * Method that calculate if the 'data' is valid according to 'config'
   *
   * @return void
   */
  protected function validate(): void
  {
    $this->errors = [];

    foreach ($this->config as $key => $value) {
      $err = $this->getError($key, $value);

      if ($err) {
        $this->errors[$key] = $err;
      }
    }
  }

  /**
   * validate
   *
   * Method that calculate if the 'data.item' is valid according to 'config'
   *
   * @return void
   */
  protected function getError($key, $keyConfig): string
  {
    $required = $keyConfig['required'] ?? false;
    $type = $keyConfig['type'] ?? '';
    $unique = $keyConfig['unique'] ?? '';
    $confirmed = $keyConfig['confirmed'] ?? '';
    $exists = $keyConfig['exists'] ?? '';

    $val = $this->data[$key] ?? '';

    if ($val) {

      if ($type === 'email' && !filter_var($val, FILTER_VALIDATE_EMAIL)) {
        return 'Invalid email.';
      }

      if ($unique && $this->rowExists($unique, $key, $val)) {
        return "$val already exists.";
      }

      if ($confirmed && $val !== $this->data[$key . "_confirmation"] ?? '') {
        return "$key does not match with confirmation";
      }

      if ($exists) {
        $abdfd = explode('.', $exists);
        $_key = $abdfd[1] ?? $key;

        if (!$this->rowExists($abdfd[0], $_key, $val)) {
          return "$val doesnot exists.";
        }
      }

      return '';
    }

    if ($required) {
      return "$key cannot be empty.";
    }

    return '';
  }

  /**
   * rowExists
   *
   * Method to check if a row exists in a table
   *
   * @return void
   */
  protected function rowExists($tableName, $columnName, $value): bool
  {
    $query = new Query($tableName);
    $rows = $query->where($columnName, $value)->limit(1)->exec();

    return !empty($rows);
  }

  /**
   * isValid
   *
   * Returns the validity of 'data'
   *
   * **Example**
   * ```
   * $data = [
   *  "name" => "John Doe"
   * ];
   *
   * $config = [
   *  "name" => [
   *    "require" => true
   *  ]
   * ];
   * $validator = new Validator($data, $config);
   * echo $validator->isValid(); // Returns 'true'
   * ```
   *
   * @return bool
   */
  public function isValid(): bool
  {
    return empty($this->errors);
  }

  /**
   * getErrors
   *
   * Returns array of errors if isValid() == false
   *
   * **Example**
   * ```
   * $data = [];
   *
   * $config = [
   *  "name" => [
   *    "require" => true
   *  ]
   * ];
   * $validator = new Validator($data, $config);
   * echo $validator->isValid(); // Returns 'false'
   * $validator->getErrors(); // Returns the error stating that 'name' is required
   * ```
   *
   * @return array
   */
  public function getErrors(): array
  {
    return $this->errors;
  }
}
