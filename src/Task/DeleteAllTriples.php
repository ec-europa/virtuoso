<?php

namespace Virtuoso\Task;

/**
 * Class PurgeVirtuosoBackend.
 */
class DeleteAllTriples extends VirtuosoTaskBase {

  /**
   * Deletes all triples from the backend.
   */
  public function main() {
    $this->query('DROP ALL;');
  }

}
