<?php

namespace Drupal\ikto_environment_indicator;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Defines dynamic permissions provider for environment indicator.
 *
 * @package Drupal\ikto_environment_indicator
 */
class EnvironmentIndicatorPermissions implements ContainerInjectionInterface {

  /**
   * Environment indicator storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $storage;

  /**
   * EnvironmentIndicatorPermissions constructor.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   *   If ikto_environment_indicator entity is missing.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager) {
    $this->storage = $entity_type_manager->getStorage('ikto_environment_indicator');
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager')
    );
  }

  /**
   * Returns the dynamic permissions array.
   *
   * @return array
   *   The permissions configuration array.
   */
  public function permissions() {
    $permissions = [];
    $environments = $this->storage->loadMultiple();
    foreach ($environments as $machine => $environment) {
      $permissions['access ikto environment indicator ' . $environment->id()] = [
        'title' => t('See environment indicator for %name', ['%name' => $environment->label()]),
        'description' => t('See the environment indicator if the user is in the %name environment.', ['%name' => $environment->label()]),
      ];
    }

    return $permissions;
  }

}
