<?php

/**
 * @file
 * Contains \Drupal\environment_indicator\EnvironmentIndicatorPermissions.
 */

namespace Drupal\ikto_environment_indicator;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class EnvironmentIndicatorPermissions implements ContainerInjectionInterface {

  /** @var EntityStorageInterface */
  protected $storage;

  public function __construct(EntityStorageInterface $storage) {
    $this->storage = $storage;
  }

  /**
   * {@inheridoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager')->getStorage('ikto_environment_indicator')
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
