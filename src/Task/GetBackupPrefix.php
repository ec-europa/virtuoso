<?php

namespace Virtuoso\Task;

/**
 * Sets the prefix used in a Virtuoso backup folder on a Phing property.
 */
class GetBackupPrefix extends \Task {

  /**
   * The directory where the Virtuoso backup files are located.
   *
   * @var string
   */
  protected $dir;

  /**
   * The name of the property in which to store the prefix.
   *
   * @var string
   */
  protected $propertyName;

  /**
   * Discover the prefix used in the backup folder and set it on the property.
   */
  public function main() {
    $prefix = '';
    foreach (glob($this->dir . '/*.bp') as $backup_file) {
      $parts = explode('/', $backup_file);
      $filename = array_pop($parts);
      preg_match('/(.*[^\d])\d+\.bp/', $filename, $matches);
      if (empty($matches[1])) {
        continue;
      }
      if (!empty($prefix) && $prefix !== $matches[1]) {
        throw new \Exception('Cannot determine prefix since files with different prefixes are present in ' . (string) $this->dir);
      }
      $prefix = $matches[1];
    }

    if (empty($prefix)) {
      throw new \Exception('No backup files found in folder ' . (string) $this->dir);
    }

    $this->project->setProperty($this->propertyName, $prefix);
  }

  /**
   * Sets the directory in which the Virtuoso backup files are located.
   *
   * A backup of a Virtuoso database consists of a number of files that share a
   * common prefix, followed by a sequential number starting with the number 1,
   * and the extension '.bp'.
   *
   * @param string $dir
   *   The directory.
   */
  public function setDir($dir) {
    $this->dir = $dir;
  }

  /**
   * Sets the name of the property in which to return the prefix.
   *
   * @param string $propertyName
   *   The name of the property.
   */
  public function setPropertyName($propertyName) {
    $this->propertyName = $propertyName;
  }

}
