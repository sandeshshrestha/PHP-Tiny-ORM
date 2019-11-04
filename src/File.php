<?php
/**
 * File.php
 */

namespace TinyORM;

/**
 * File class
 * 
 * This class hold information about a file. 
 * 
 * **Example**
 * ```
 * $file = new File('docs', 'cv.pdf', 'base64 content');
 * ```
 */
class File {
   /** @var string $folder Folder */
  public $folder;
  /** @var string $fileName File name */
  public $fileName;
  /** @var string $fileContent File content */
  public $fileContent;

  /**
   * __construct
   *
   * @param  string $folder Name of the folder for the file
   * @param  string $fileName Name of the file
   * @param  string $fileContent Base64 encode file content
   *
   * @return void
   */
  public function __construct(string $folder, string $fileName, string $fileContent) {
    $this->folder = $folder;
    $this->fileName = $fileName;
    $this->fileContent = $fileContent;
  }

  /**
   * getContent
   * 
   * Returns the base64 decoded $fileContent 
   *
   * @return string
   */
  public function getContent(): string {
    return base64_decode($this->fileContent);
  }
}