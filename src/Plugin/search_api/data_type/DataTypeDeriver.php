<?php

/**
 * @file
 * Contains \Drupal\search_api\Plugin\search_api\data_type\DataTypeDeriver.
 */

namespace Drupal\search_api\Plugin\search_api\data_type;

use Drupal\Core\Plugin\Discovery\ContainerDeriverInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Component\Plugin\PluginBase;


/**
 * Derives a data_type plugin definition for every data_type defined in utility.
 *
 * @see \Drupal\search_api\Plugin\search_api\data_type\DataType
 */
class DataTypeDeriver implements ContainerDeriverInterface {

  use StringTranslationTrait;

  /**
   * List of derivative definitions.
   *
   * @var array
   */
  protected $derivatives = array();

  /**
   * The data type plugin manager to use.
   *
   * @var \Drupal\search_api\DataType\DataTypePluginManager
   */
  protected $dataTypePluginManager;

  /**
   * The entity manager.
   *
   * @var \Drupal\Core\Entity\EntityManagerInterface
   */
  protected $entityManager;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, $base_plugin_id) {
    $deriver = new static();

    /** @var \Drupal\search_api\DataType\DataTypePluginManager $data_type_plugin_manager */
    $data_type_plugin_manager = $container->get('plugin.manager.search_api.data_type');
    $deriver->setDataTypePluginManager($data_type_plugin_manager);

    /** @var \Drupal\Core\StringTranslation\TranslationInterface $translation */
    $translation = $container->get('string_translation');
    $deriver->setStringTranslation($translation);

    return $deriver;
  }

  /**
   * Retrieves the data type plugin manager.
   *
   * @return \Drupal\search_api\DataType\DataTypePluginManager
   *   The data type plugin manager.
   */
  public function getDataTypePluginManager() {
    return $this->dataTypePluginManager ?: \Drupal::service('plugin.manager.search_api.data_type');
  }

  /**
   * Sets the data type plugin manager.
   *
   * @param \Drupal\search_api\DataType\DataTypePluginManager $data_type_plugin_manager
   *   The new data type plugin manager.
   *
   * @return $this
   */
  public function setDataTypePluginManager($data_type_plugin_manager) {
    $this->dataTypePluginManager = $data_type_plugin_manager;
    return $this;
  }


  /**
   * {@inheritdoc}
   */
  public function getDerivativeDefinition($derivative_id, $base_plugin_definition) {
    $derivatives = $this->getDerivativeDefinitions($base_plugin_definition);
    return isset($derivatives[$derivative_id]) ? $derivatives[$derivative_id] : NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function getDerivativeDefinitions($base_plugin_definition) {
    $base_plugin_id = $base_plugin_definition['id'];

    if (!isset($this->derivatives[$base_plugin_id])) {
      $plugin_derivatives = array();

      foreach ($this->getDataTypePluginManager()->getDataTypeDefinitions() as $id => $data_type) {
        $plugin_derivatives[$id] = array(
            'id' => $base_plugin_id . PluginBase::DERIVATIVE_SEPARATOR . $id,
            'label' => $data_type['label'],
            'description' => $data_type['description'],
          ) + $base_plugin_definition;
      }

      uasort($plugin_derivatives, array($this, 'compareDerivatives'));

      $this->derivatives[$base_plugin_id] = $plugin_derivatives;
    }
    return $this->derivatives[$base_plugin_id];
  }

  /**
   * Compares two plugin definitions according to their labels.
   *
   * @param array $a
   *   A plugin definition, with at least a "label" key.
   * @param array $b
   *   Another plugin definition.
   *
   * @return int
   *   An integer less than, equal to, or greater than zero if the first
   *   argument is considered to be respectively less than, equal to, or greater
   *   than the second.
   */
  public function compareDerivatives(array $a, array $b) {
    return strnatcasecmp($a['label'], $b['label']);
  }

}
