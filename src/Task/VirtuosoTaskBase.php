<?php
/**
 * @file
 * Contains \DrupalProject\build\Phing\SetVirtuosoSparqlPermissions.
 */

namespace Virtuoso\Task;

use \EasyRdf\GraphStore;
use \EasyRdf\Graph;
use \EasyRdf\Sparql\Client;

/**
 * Class SetVirtuosoSparqlPermissions.
 */
class VirtuosoTaskBase extends \Task {

  /**
   * The location of the isql binary.
   *
   * @var string
   */
  protected $isqlPath;

  /**
   * The protocol to use.
   *
   * @var string
   */
  protected $protocol = 'http://';

  /**
   * The data source name.
   *
   * @var string
   */
  protected $dsn = 'localhost';

  /**
   * The port number of the endpoint.
   *
   * @var string
   */
  protected $port = 8890;

  /**
   * The database connection username.
   *
   * @var string
   */
  protected $user = 'dba';

  /**
   * The database connection password.
   *
   * @var string
   */
  protected $pass = 'dba';

  /**
   * A directory, mounted on both the deployment machine and the db server.
   *
   * @var string
   */
  protected $sharedDirectory;

  /**
   * Executes the query using the isql binary.
   *
   * @param string $query
   *   The query string.
   *
   * @return $this
   *   The object itself for chaining.
   *
   * @throws \BuildException
   *   Thrown when there is an error with executing the command.
   */
  protected function execute($query) {
    $parts = [
      'echo ' . escapeshellarg($query),
      '|',
      escapeshellcmd($this->isqlPath),
      escapeshellarg($this->dsn),
      escapeshellarg($this->user),
      escapeshellarg($this->pass),
    ];
    $output = array();
    $return = NULL;
    exec(implode(' ', $parts), $output, $return);
    $this->log('Executing: ' . implode(' ', $parts), \Project::MSG_INFO);
    if ($return != 0) {
      foreach ($output as $line) {
        $this->log($line, \Project::MSG_ERR);
      }
      throw new \BuildException("An error occurred while executing the isql command $return");
    }
    else {
      foreach ($output as $line) {
        $this->log($line, \Project::MSG_INFO);
      }
    }

    return $this;
  }

  /**
   * Returns a GraphStore object of initialized with the object's settings.
   *
   * @return \EasyRdf\GraphStore
   *   The GraphStore object.
   */
  public function graphStore() {
    $connect_string = $this->protocol . $this->dsn . ':' . $this->port . '/sparql-graph-crud';
    // Use a local SPARQL 1.1 Graph Store.
    return new GraphStore($connect_string);
  }

  /**
   * Clears a graph from the endpoint.
   *
   * @param string $uri
   *   A uri representing a graph.
   *
   * @throws \EasyRdf\Exception
   *   Thrown if something went wrong with the delete method.
   */
  public function deleteGraph($uri) {
    $this->graphStore()->delete($uri);
  }

  /**
   * Replace the contents of a graph in the graph store with new data
   *
   * @param string $file_path
   *   The path of the file that should be parsed and uploaded.
   * @param string $graph_uri
   *   The graph uri that the triples should end up to.
   * @param string $format
   *   The format of the contents of the file provided by $file_path.
   */
  public function replaceGraph($file_path, $graph_uri, $format = 'ntriples') {
    $graph = new Graph();
    $graph->parseFile($file_path);
    $this->graphStore()->replace($graph, $graph_uri, $format);
  }

  /**
   * Executes a query through an HTTP request.
   *
   * @param string $query
   *   The string version of the query to execute.
   *
   */
  public function query($query) {
    // @todo: The port should be passed as a variable below.
    $connect_string = $this->protocol . $this->dsn . ':' . $this->port . '/sparql';
    $client = new Client($connect_string);
    $client->query($query);
  }

  /**
   * {@inheritdoc}
   */
  public function main() {
    throw new \Exception('VirtuosoTaskBase should not be directly instantiated.');
  }

  /**
   * Set path to isql binary.
   *
   * @param string $path
   *    Path on the server.
   */
  public function setIsqlPath($path) {
    $this->isqlPath = $path;
  }

  /**
   * Set the protocol for the queries.
   *
   * @param string $protocol
   *    The protocol for the queries.
   */
  public function setProtocol($protocol) {
    $this->protocol = $protocol;
  }

  /**
   * Set data source name.
   *
   * @param string $dsn
   *    Data source name of the Virtuoso database.
   */
  public function setDataSourceName($dsn) {
    $this->dsn = $dsn;
  }

  /**
   * Sets the port of the endpoint.
   *
   * @param string $port
   *   The port number.
   */
  public function setPort($port) {
    $this->port = $port;
  }

  /**
   * Set user name.
   *
   * @param string $user
   *    User name of the Virtuoso dba user.
   */
  public function setUsername($user) {
    $this->user = $user;
  }

  /**
   * Set password.
   *
   * @param string $pass
   *    Password of the Virtuoso dba user.
   */
  public function setPassword($pass) {
    $this->pass = $pass;
  }

  /**
   * Sets the shared directory variable.
   *
   * @param string $dir
   *   The shared directory path.
   */
  public function setSharedDirectory($dir) {
    $this->sharedDirectory = $dir;
  }

}
