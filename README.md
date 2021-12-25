# PHP Tiny ORM

### Installation

```bash
composer require sandeshshrestha/tiny-orm
```

### Configuration
Define few constants.
```php
// Define __FILE_STORAGE_PATH__ constant where the FileStorage::save() method saves the file.
define("__FILE_STORAGE_PATH__", __DIR__ . '/uploaded_files/');
// Define __DATABASE_CONFIG__ constant that will be used by Database.php to connect to database
define("__DATABASE_CONFIG__", [
    'type' => 'mysql',
    'host' => 'mysql.example.com',
    'username' => 'db_username',
    'password' => 'db_password',
    'database' => 'db_name',
    'table_prefix' => 'db_table_prefix',
    'port' => 3306
  ]);
```

### Database
Create database tables
```sql
CREATE TABLE `db_table_prefix_user` (
  `id` varchar(255) NOT NULL,
  `fullName` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
```

### Usage
```php

use TinyORM\Model;

class User extends Model {
  protected $table = 'user';
  protected $primaryKey = 'id';
  protected $fillable = ["fullName", "email"];
}

$user = new User();
$user->fullName = "John Doe";
$user->email = "johndoe@example.com";
$user->save();

$user2 = User::create([
  'fullName' => "John Doe 2",
  "email" => "johndoe2@example.com",
]);

$foundUser = User::find($user->getPrimaryValue());

$foundUser->delete();

$users = User::where("fullName", "John Doe")->limit(1)->orderBy('id')->exec();

```

### Documentations
[https://sandeshshrestha.github.io/PHP-Tiny-ORM/](https://sandeshshrestha.github.io/PHP-Tiny-ORM/)


### Code quality status

[![SonarCloud](https://sonarcloud.io/images/project_badges/sonarcloud-white.svg)](https://sonarcloud.io/summary/new_code?id=sandeshshrestha_PHP-Tiny-ORM)


[![Quality Gate Status](https://sonarcloud.io/api/project_badges/measure?project=sandeshshrestha_PHP-Tiny-ORM&metric=alert_status)](https://sonarcloud.io/summary/new_code?id=sandeshshrestha_PHP-Tiny-ORM)
[![Bugs](https://sonarcloud.io/api/project_badges/measure?project=sandeshshrestha_PHP-Tiny-ORM&metric=bugs)](https://sonarcloud.io/summary/new_code?id=sandeshshrestha_PHP-Tiny-ORM)
[![Code Smells](https://sonarcloud.io/api/project_badges/measure?project=sandeshshrestha_PHP-Tiny-ORM&metric=code_smells)](https://sonarcloud.io/summary/new_code?id=sandeshshrestha_PHP-Tiny-ORM)
[![Vulnerabilities](https://sonarcloud.io/api/project_badges/measure?project=sandeshshrestha_PHP-Tiny-ORM&metric=vulnerabilities)](https://sonarcloud.io/summary/new_code?id=sandeshshrestha_PHP-Tiny-ORM)
[![Security Rating](https://sonarcloud.io/api/project_badges/measure?project=sandeshshrestha_PHP-Tiny-ORM&metric=security_rating)](https://sonarcloud.io/summary/new_code?id=sandeshshrestha_PHP-Tiny-ORM)
