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
      // Delete the graph first...
      $this->execute("SPARQL CLEAR GRAPH <$graph_name>;");
      exec("curl --digest --user $this->user:$this->pass --verbose --url 'http://$this->dsn:8890/sparql-graph-crud-auth?graph-uri=$graph_name' -T $rdf_file_path");
    }
  }

}
