<?php

namespace Drupal\ikto_environment_indicator_dependent_config\Plugin\ConfigFilter;

use Drupal\config_filter\Plugin\ConfigFilterBase;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\StorageInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\ikto_environment_indicator\EnvironmentInfoServiceInterface;
use Drupal\ikto_environment_indicator_dependent_config\StorageManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class DependentConfigFilter
 * @package Drupal\ikto_environment_indicator_dependent_config\Plugin\ConfigFilter
 *
 * @ConfigFilter(
 *   id = "ikto_environment_indicator_dependent_config_filter",
 *   label = @Translation("IKTO Environment indicator: Dependent config"),
 *   weight = 0,
 *   status = TRUE,
 *   storages = {"config.storage.sync"}
 * )
 */
class DependentConfigFilter extends ConfigFilterBase implements ContainerFactoryPluginInterface {

  /** @var ConfigFactoryInterface */
  protected $configFactory;

  /** @var StorageManagerInterface */
  protected $storageManager;

  /** @var EnvironmentInfoServiceInterface */
  protected $environmentInfoService;

  /** @var array */
  protected $dependentConfigList = NULL;

  /**
   * {@inheritdoc}
   */
  public static function create(
    ContainerInterface $container,
    array $configuration,
    $plugin_id,
    $plugin_definition
  ) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('ikto_environment_indicator_dependent_config.storage_manager'),
      $container->get('ikto_environment_indicator.active_environment_info'),
      $container->get('config.factory')
    );
  }

  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    StorageManagerInterface $storageManager,
    EnvironmentInfoServiceInterface $environmentInfoService,
    ConfigFactoryInterface $configFactory
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->storageManager         = $storageManager;
    $this->environmentInfoService = $environmentInfoService;
    $this->configFactory          = $configFactory;
  }

  /**
   * {@inheritdoc}
   */
  public function filterRead($name, $data) {
    if ($this->isConfigNameDependent($name)) {
      return $this->getDependentStorage()->read($name);
    }
    else {
      return $data;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function filterWrite($name, array $data) {
    if ($this->isConfigNameDependent($name)) {
      $this->getDependentStorage()->write($name, $data);

      return NULL;
    }
    else {
      return $data;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function filterWriteEmptyIsDelete($name) {
    return $this->isConfigNameDependent($name);
  }

  /**
   * {@inheritdoc}
   */
  public function filterExists($name, $exists) {
    if ($this->isConfigNameDependent($name)) {
      return $this->getDependentStorage()->exists($name);
    }
    else {
      return $exists;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function filterDelete($name, $delete) {
    if ($this->isConfigNameDependent($name)) {
      return $this->getDependentStorage()->delete($name);
    }
    else {
      return $delete;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function filterReadMultiple(array $names, array $data) {
    $dependentConfigNames = $this->getDependentConfigNames($names);
    if ($dependentConfigNames) {
      $dependentData = $this->getDependentStorage()->readMultiple($dependentConfigNames);
    }
    else {
      $dependentData = [];
    }

    return $dependentData + $data;
  }

  /**
   * {@inheritdoc}
   */
  public function filterRename($name, $new_name, $rename) {
    if ($this->isConfigNameDependent($name)) {
      if ($this->isConfigNameDependent($new_name)) {
        return $this->getDependentStorage()->rename($name, $new_name);
      }
      else {
        $data = $this->getDependentStorage()->read($name);
        $this->getDependentStorage()->delete($name);
        $this->getSourceStorage()->write($name, $data);
        return $rename;
      }
    }
    else {
      if ($this->isConfigNameDependent($new_name)) {
        $data = $this->getSourceStorage()->read($name);
        $this->getSourceStorage()->delete($name);
        $this->getDependentStorage()->write($new_name, $data);
      }

      return $rename;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function filterListAll($prefix, array $data) {
    if ($this->prefixIncludesDependent($prefix)) {
      $dependentConfigs = $this->getDependentStorage()->listAll($prefix);
      $dependentConfigs = $this->getDependentConfigNames($dependentConfigs);
      $data = array_merge($data, $dependentConfigs);
      $data = array_unique($data);
    }

    return $data;
  }

  /**
   * {@inheritdoc}
   */
  public function filterDeleteAll($prefix, $delete) {
    $names = $this->getDependentStorage()->listAll($prefix);
    foreach ($names as $name) {
      $this->getDependentStorage()->delete($name);
    }

    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function filterCreateCollection($collection) {
    return NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function filterGetAllCollectionNames(array $collections) {
    return $collections;
  }

  /**
   * {@inheritdoc}
   */
  public function filterGetCollectionName($collection) {
    return $collection;
  }

  /**
   * Returns dependent storage.
   *
   * @return StorageInterface
   */
  protected function getDependentStorage() {
    return $this->storageManager->getDependentStorage($this->getEnvironmentId());
  }

  /**
   * Gets current environment id.
   *
   * @return string
   */
  protected function getEnvironmentId() {
    return $this->environmentInfoService->getMachineName();
  }

  /**
   * Ensures that dependent config list is loaded.
   */
  protected function ensureDependentConfigList() {
    if ($this->dependentConfigList === NULL) {
      $config = $this->configFactory->get('ikto_environment_indicator_dependent_config.settings');
      $this->dependentConfigList = $config->get('config_list');
    }
  }

  /**
   * Checks whether config name is environment dependent or not.
   *
   * @param string $name
   * @return bool
   */
  protected function isConfigNameDependent($name) {
    $this->ensureDependentConfigList();

    return in_array($name, $this->dependentConfigList);
  }

  /**
   * Returns a list of environment dependent configs from supplied list of config.
   *
   * @param array $names
   * @return array
   */
  protected function getDependentConfigNames($names) {
    $this->ensureDependentConfigList();

    return array_intersect($names, $this->dependentConfigList);
  }

  /**
   * Checks whether prefix includes environment dependent configs or not.
   *
   * @param string $prefix
   * @return bool
   */
  protected function prefixIncludesDependent($prefix) {
    if (empty($prefix)) {
      return TRUE;
    }

    $this->ensureDependentConfigList();
    foreach ($this->dependentConfigList as $dependentConfigName) {
      if (strpos($dependentConfigName, $prefix) === 0) {
        return TRUE;
      }
    }

    return FALSE;
  }
}
