<?php

/**
 * @file
 * Contains \Drupal\search_api\DataType\DataTypePluginManager.
 */

namespace Drupal\search_api\DataType;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;

/**
 * Manages data type plugins.
 *
 * @see \Drupal\search_api\Annotation\SearchApiDataType
 * @see \Drupal\search_api\DataType\DataTypeInterface
 * @see \Drupal\search_api\DataType\DataTypePluginBase
 * @see plugin_api
 */
class DataTypePluginManager extends DefaultPluginManager {

  /**
   * Constructs a DataTypePluginManager object.
   *
   * @param \Traversable $namespaces
   *   An object that implements \Traversable which contains the root paths
   *   keyed by the corresponding namespace to look for plugin implementations.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache_backend
   *   Cache backend instance to use.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler.
   */
  public function __construct(\Traversable $namespaces, CacheBackendInterface $cache_backend, ModuleHandlerInterface $module_handler) {
    parent::__construct('Plugin/search_api/data_type', $namespaces, $module_handler, 'Drupal\search_api\DataType\DataTypeInterface', 'Drupal\search_api\Annotation\SearchApiDataType');
    $this->setCacheBackend($cache_backend, 'search_api_data_type');
    $this->alterInfo('search_api_data_type_info');
  }

}
