<?php
/**
 * @file
 * Contains \Drupal\search_api\Controller\ServerListController.
 */

namespace Drupal\search_api\Controller;

/*
 * Include required classes and interfaces.
 */
use Drupal\Core\Config\Entity\ConfigEntityListController;
use Drupal\Component\Utility\String;
use Drupal\Component\Utility\Xss;

/**
 * Defines a list controller for the Server entity.
 */
class ServerListController extends ConfigEntityListController {

  /**
   * {@inheritdoc}
   */
  public function load() {
    // Initialize the entities variable to default array structure.
    $entities = array(
      'enabled' => array(),
      'disabled' => array(),
    );
    // Iterate through the available entities.
    foreach (parent::load() as $entity) {
      // Get the status key based upon the entity status.
      $status_key = $entity->status() ? 'enabled' : 'disabled';
      // Add the entity to the list.
      $entities[$status_key][] = $entity;
    }
    return $entities;
  }

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    return array(
      'title' => $this->t('Name'),
      'service' => array(
        'data' => $this->t('Service'),
        'class' => array(RESPONSIVE_PRIORITY_LOW),
      ),
      'operations' => $this->t('Operations'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(\Drupal\Core\Entity\EntityInterface $entity) {
    // Check if the server contains a valid service.
    if ($entity->hasValidService()) {
      // Get the service plugin definition.
      $service_plugin_definition = $entity->getService()->getPluginDefinition();
      // Get the service label.
      $service_label = $this->t($service_plugin_definition['name']);
    }
    else {
      // Set the service label to broken.
      $service_label = $this->t('Broken');
    }
    // Build the row for the current entity.
    return array(
      'title' => String::checkPlain($entity->label()) . ($entity->getDescription() ? "<div class=\"description\">{$entity->getDescription()}</div>" : ''),
      'service' => String::checkPlain($service_label),
      'operations' => array(
        'data' => $this->buildOperations($entity),
      ),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function render() {
    // Load the entities.
    $entities = $this->load();
    // Initialize the build variable to an empty array.
    $build = array(
      'enabled' => array('#markup' => "<h2>{$this->t('Enabled')}</h2>"),
      'disabled' => array('#markup' => "<h2>{$this->t('Disabled')}</h2>"),
    );
    // Iterate through the entity states.
    foreach (array('enabled', 'disabled') as $status) {
      // Initialize the rows variable to an empty array.
      $rows = array();
      // Iterate through the entities.
      foreach ($entities[$status] as $entity) {
        // Add the entity to the rows.
        $rows[$entity->id()] = $this->buildRow($entity);
      }
      // Build the status container.
      $build[$status]['#type'] = 'container';
      $build[$status]['table'] = array(
        '#theme' => 'table',
        '#header' => $this->buildHeader(),
        '#rows' => $rows,
      );
    }
    // Configure the empty messages.
    $build['enabled']['table']['#empty'] = $this->t('There are no enabled search servers.');
    $build['disabled']['table']['#empty'] = $this->t('There are no disabled search servers.');
    // Return the renderable array.
    return $build;
  }

}
