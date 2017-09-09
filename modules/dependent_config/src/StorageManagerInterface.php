<?php

namespace Drupal\ikto_environment_indicator_dependent_config;

use Drupal\Core\Config\StorageInterface;

interface StorageManagerInterface {

  /**
   * Gets a storage for environment dependent configs.
   *
   * @param string $id
   * @return StorageInterface
   */
  public function getDependentStorage($id);
}
