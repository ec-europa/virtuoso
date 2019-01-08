<?php
/**
 * @file
 * Contains \DrupalProject\build\Phing\ImportRdfFixtures.
 */

namespace Virtuoso\Task;

/**
 * Class ImportRdfFixtures.
 */
class ImportRdfFixtures extends VirtuosoTaskBase {

  /**
   * (re-)import the rdf fixtures into the sparql endpoint.
   */
  public function main() {
    $fixtures_path = $this->project->getBasedir() . '/resources/fixtures/';
    foreach (glob($fixtures_path . '*.rdf') as $rdf_file_path) {
      $parts = explode('/', $rdf_file_path);
      $filename = array_pop($parts);
      $file = str_replace('.rdf', '', $filename);
      $graph_name = 'http://' . strtolower($file);
      // Since there might be too many triples to delete from a single graph,
      // we enforce autocommit on the transaction log to split up the memory
      // load.
      // @see: http://vos.openlinksw.com/owiki/wiki/VOS/VirtTipsAndTricksGuideDeleteLargeGraphs
      $this->query("DEFINE sql:log-enable 3 CLEAR GRAPH <$graph_name>;");
      exec("curl --digest --user $this->user:$this->pass --verbose --url '$this->protocol://$this->dsn:$this->port/sparql-graph-crud-auth?graph-uri=$graph_name' -T $rdf_file_path");
    }
  }

}
