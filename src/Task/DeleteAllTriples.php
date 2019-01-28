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
    $graphs = $this->query('SELECT DISTINCT(?g) WHERE { GRAPH ?g { ?s ?p ?o } } ORDER BY ?g');
    foreach ($graphs as $graph) {
      if ($graph_uri = $graph->g->getUri()) {
        $this->query("CLEAR GRAPH <{$graph_uri}>;");
      }
    }
  }

}
