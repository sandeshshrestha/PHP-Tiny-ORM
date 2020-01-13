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
  /** @var bool $_isValid Is valid*/
  protected $_isValid;
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
    $errors = [];
    $data = $this->data;

    foreach ($this->config as $key => $value) {
      $required = $value['required'];
      $type = $value['type'];
      $unique = $value['unique'];
      $confirmed = $value['confirmed'];
      $exists = $value['exists'];
      $err = '';

      $val = $data[$key];

      if ($required && !$val) {
        $err = "$key cannot be empty.";
      } else {
        if ($val) {
          if ($type === 'email' && !filter_var($val, FILTER_VALIDATE_EMAIL)) {
            $err = "Invalid email.";
          } elseif ($unique) {
            $query = new Query($unique);
            $rows = $query->where($key, $val)->limit(1)->exec();

            if (sizeof($rows) > 0) {
              $err = "$val already exists.";
            }
          } elseif ($confirmed) {
            $key_confirmed = $key . "_confirmation";
            $val_confirmed = $data[$key_confirmed];

            if (!$val_confirmed) {
              $err = "$key_confirmed cannot be empty.";
            } elseif ($val !== $val_confirmed) {
              $err = "$key and $key_confirmed does not match";
            }
          } elseif ($exists) {
            $abdfd = explode(',', $exists);
            $query = new Query($abdfd[0]);
            $_key = $key;

            if ($abdfd[1]) {
              $_key = $abdfd[1];
            }

            $rows = $query->where($_key, $val)->limit(1)->exec();

            if (sizeof($rows) === 0) {
              $err = "$val doesnot exists.";
            }
          }
        }
      }

      if ($err) {
        $errors[$key] = $err;
      }
    }

    if (sizeof($errors) > 0) {
      $this->_isValid = false;
      $this->errors = $errors;
    } else {
      $this->_isValid = true;
      $this->errors = [];
    }
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
    return $this->_isValid;
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
