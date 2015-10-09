<?php

/**
 * @file
 * Contains \Drupal\search_api\Plugin\search_api\data_type\DataType.
 */

namespace Drupal\search_api\Plugin\search_api\data_type;

use Drupal\Core\Render\Element;
use Drupal\search_api\Backend\BackendPluginManager;
use Drupal\search_api\DataType\DataTypePluginBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Represents a datasource which exposes the content entities.
 *
 * @SearchApiDataType(
 *   id = "entity",
 *   deriver = "Drupal\search_api\Plugin\search_api\data_type\DataTypeDeriver"
 * )
 */
class DataType extends DataTypePluginBase {

  /**
   * The entity manager.
   *
   * @var \Drupal\search_api\Backend\BackendPluginManager|null
   */
  protected $backendManager;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, array $plugin_definition) {
    // Since defaultConfiguration() depends on the plugin definition, we need to
    // override the constructor and set the definition property before calling
    // that method.
    $this->pluginDefinition = $plugin_definition;
    $this->pluginId = $plugin_id;
    $this->configuration = $configuration + $this->defaultConfiguration();
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    /** @var static $data_type */
    $data_type = parent::create($container, $configuration, $plugin_id, $plugin_definition);

    /** @var \Drupal\search_api\Backend\BackendPluginManager $backend_manager */
    $backend_manager = $container->get('plugin.manager.search_api.backend');
    $data_type->setBackendManager($backend_manager);

    return $data_type;
  }

  /**
   * Retrieves the backend manager.
   *
   * @return \Drupal\search_api\Backend\BackendPluginManager
   *   The search_api backend manager.
   */
  public function getBackendManager() {
    return $this->backendManager ?: \Drupal::service('plugin.manager.search_api.backend');
  }

  /**
   * Sets the backend manager.
   *
   * @param \Drupal\search_api\Backend\BackendPluginManager $backend_manager
   *   The search_api backend manager.
   *
   * @return $this
   */
  public function setBackendManager(BackendPluginManager $backend_manager) {
    $this->backendManager = $backend_manager;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    $plugin_definition = $this->getPluginDefinition();
    $conf = array();
    foreach ($this->getBackendManager()->getDefinitions() as $backend_id => $backend) {
      $conf[$backend_id] = array();

      // Facet api support for the data_types. Set the default query_type
      // supported for each.
      if (\Drupal::moduleHandler()->moduleExists('facetapi')) {
        /** @var \Drupal\facetapi\QueryType\QueryTypePluginManager $query_type_plugin_manager */
        $query_type_plugin_manager = \Drupal::service('plugin.manager.facetapi.query_type');

        // The facetapi query type search_api_term supports all search api
        // data_types.
        // set a default but also allow custom configurations to happen.
        // @todo import a mapping here from somewhere.
        // Utility::getSearchApiFacetApiQueryTypeMapping() would be a good
        // candidate.
        $conf[$backend_id]['facetapi']['query_type']['term'] = 'search_api_term';
        if (!empty($plugin_definition['configuration'][$backend_id]['facetapi']['query_type']['term'])){
          $conf[$backend_id]['facetapi']['query_type']['term'] = $plugin_definition['configuration'][$backend_id]['facetapi']['query_type']['term'];
        }
      }
    }

    return $conf;
  }
}
