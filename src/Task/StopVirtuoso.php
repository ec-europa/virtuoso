<?php

namespace Virtuoso\Task;

/**
 * Class StopVirtuoso.
 */
class StopVirtuoso extends VirtuosoTaskBase {

  /**
   * Shutdown Virtuoso.
   */
  public function main() {
    $this->execute('shutdown();');
  }

}
