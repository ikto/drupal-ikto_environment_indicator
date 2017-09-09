<?php

namespace Drupal\ikto_environment_indicator_dependent_config;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\FileStorage;
use Drupal\Core\Config\StorageInterface;

class StorageManager implements StorageManagerInterface {

  /** @var ConfigFactoryInterface */
  protected $configFactory;

  /** @var StorageInterface[] */
  protected $storages = [];

  public function __construct(ConfigFactoryInterface $configFactory) {
    $this->configFactory = $configFactory;
  }

  /**
   * {@inheritdoc}
   */
  public function getDependentStorage($id) {
    if (!array_key_exists($id, $this->storages)) {
      $this->storages[$id] = $this->createDependentStorage($id);
    }

    return $this->storages[$id];
  }

  /**
   * Creates environment dependent storage.
   *
   * @param string $id
   * @return StorageInterface
   */
  protected function createDependentStorage($id) {
    $storagePath = $this->getStoragePath($id);

    if ($storagePath === NULL) {
      return NULL;
    }
    else {
      return new FileStorage($storagePath);
    }
  }

  /**
   * Returns a path for environment dependent storage.
   *
   * @param string $id
   * @return string
   */
  protected function getStoragePath($id) {
    $basePath = $this->getStorageBasePath();
    if ($basePath === NULL) {
      return NULL;
    }

    return $basePath . '/' . $id;
  }

  /**
   * Returns a base path for storing environment dependent storages.
   *
   * @return string
   */
  protected function getStorageBasePath() {
    $config = $this->configFactory->get('ikto_environment_indicator_dependent_config.settings');
    $base_dir = ltrim($config->get('base_dir'));
    if ($base_dir[0] != '/') {
      $base_dir = \Drupal::root() . '/' . $base_dir;
    }

    return realpath($base_dir);
  }
}
