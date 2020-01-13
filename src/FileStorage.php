<?php

/**
 * FileStorage.php
 */

namespace TinyORM;

use TinyORM\File;
use \fopen;

/**
 * FileStorage class
 *
 * This class holds utils to save and delete file.
 *
 * **Example**
 * ```
 * $file = new File('docs', 'cv.pdf', 'base64 content');
 * FileStorage::saveFile($file);
 * FileStorage::deleteFile($file);
 * FileStorage::deleteFolder('./folder_to_be_deleted');
 * ```
 */

class FileStorage
{
  /** @var File $file File class */
  protected $file;

  /**
   * __construct
   *
   * @param  File $file
   *
   * @return void
   */
  public function __construct(File $file)
  {
    $this->file = $file;
  }

  /**
   * save
   *
   * Save the file to a persistent storage.
   *
   * **Example**
   * ```
   * $file = new File('docs', 'cv.pdf', 'base64 content');
   * $fileStorage = new FileStorage($file);
   * $fileStorage->save();
   * ```
   *
   * @return void
   */
  public function save(): void
  {
    $absoluteFolder = __FILE_STORAGE_PATH__ . $this->file->folder;
    $absoluteFile = $absoluteFolder . '/' . $this->file->fileName;

    if (!file_exists($absoluteFolder)) {
      mkdir($absoluteFolder, 0777, true);
    }

    $file = fopen($absoluteFile, "w") or die("Unable to open file!");
    fwrite($file, $this->file->getContent());
    fclose($file);
  }

  /**
   * delete
   *
   * Delete the file from persistent storage.
   *
   * **Example**
   * ```
   * $file = new File('docs', 'cv.pdf', '');
   * $fileStorage = new FileStorage($file);
   * $fileStorage->delete();
   * ```
   *
   * @return void
   */
  public function delete(): void
  {
    $absoluteFolder = __FILE_STORAGE_PATH__ . $this->file->folder;
    $absoluteFile = $absoluteFolder . '/' . $this->file->fileName;
    unlink($absoluteFile);
  }

  /**
   * saveFile
   *
   * Short hand static method to save a file.
   *
   * **Example**
   * ```
   * $file = new File('docs', 'cv.pdf', 'base64 content');
   * FileStorage::saveFile($file);
   * ```
   * @param  File $file
   *
   * @return FileStorage
   */

  public static function saveFile(File $file): FileStorage
  {
    $obj = new static($file);
    $obj->save();

    return $obj;
  }

  /**
   * deleteFile
   *
   * Short hand static method to delete a file.
   *
   * **Example**
   * ```
   * $file = new File('docs', 'cv.pdf', '');
   * FileStorage::deleteFile($file);
   * ```
   *
   * @param  File $file
   *
   * @return FileStorage
   */
  public static function deleteFile(File $file): FileStorage
  {
    $obj = new static($file);
    $obj->delete();

    return $obj;
  }

  /**
   * _deleteFolder
   *
   * Recursive private method to delete a folder
   *
   * @param  string $dir
   *
   * @return void
   */
  private static function _deleteFolder(string $dir): void
  {
    if (is_dir($dir)) {
      $objects = scandir($dir);
      foreach ($objects as $object) {
        if ($object != "." && $object != "..") {
          if (filetype($dir . "/" . $object) == "dir") {
            self::_deleteFolder($dir . "/" . $object);
          } else {
            unlink($dir . "/" . $object);
          }
        }
      }
      reset($objects);
      rmdir($dir);
    }
  }

  /**
   * deleteFolder
   *
   * Short hand static method to delete a filder.
   *
   * **Example**
   * ```
   * FileStorage::deleteFolder('./folder_to_be_deleted');
   * ```
   *
   * @param  string $path
   *
   * @return void
   */
  public static function deleteFolder(string $path): void
  {
    $dir = __FILE_STORAGE_PATH__ . $path;

    self::_deleteFolder($dir);
  }
}
