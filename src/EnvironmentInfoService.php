<?php

namespace Drupal\ikto_environment_indicator;

use Drupal\Core\Access\AccessResultAllowed;
use Drupal\Core\Access\AccessResultForbidden;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Site\Settings;
use Drupal\ikto_environment_indicator\Entity\EnvironmentIndicatorInterface;

/**
 * Defines environment indicator service.
 */
class EnvironmentInfoService implements EnvironmentInfoServiceInterface {

  const CACHE_KEY = 'ikto_environment_indicator:active_environment';

  /**
   * The machine name of the active environment.
   *
   * @var string
   */
  protected $machineName;

  /**
   * The human-readable name of the active environment.
   *
   * @var string
   */
  protected $displayName;

  /**
   * The description of the active environment.
   *
   * @var string
   */
  protected $description;

  /**
   * The foreground color of the active environment.
   *
   * @var string
   */
  protected $fgColor;

  /**
   * The background color of the active environment.
   *
   * @var string
   */
  protected $bgColor;

  /**
   * The flag which indicates whether active indicator was loaded or not.
   *
   * @var bool
   */
  protected $isLoaded = FALSE;

  /**
   * The cache backend for environment indicator.
   *
   * @var \Drupal\Core\Cache\CacheBackendInterface
   */
  protected $cache;

  /**
   * Git info provider service.
   *
   * @var \Drupal\ikto_environment_indicator\GitInfoServiceInterface
   */
  protected $gitInfo;

  /**
   * Environment indicator storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $storage;

  /**
   * EnvironmentInfoService constructor.
   *
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache
   *   The cache backend for environment indicator.
   * @param \Drupal\ikto_environment_indicator\GitInfoServiceInterface $gitInfo
   *   The git info provider service.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   *   If ikto_environment_indicator entity is missing.
   */
  public function __construct(
    CacheBackendInterface $cache,
    GitInfoServiceInterface $gitInfo,
    EntityTypeManagerInterface $entityTypeManager
  ) {
    $this->cache = $cache;
    $this->gitInfo = $gitInfo;
    $this->storage = $entityTypeManager->getStorage('ikto_environment_indicator');
  }

  /**
   * Initializes current environment parameters.
   */
  public function init() {
    $cachedEnvironment = $this->cache->get(self::CACHE_KEY);

    if ($cachedEnvironment && !empty($cachedEnvironment->data)) {
      $this->machineName = $cachedEnvironment->data['machine_name'];
      $this->displayName = $cachedEnvironment->data['display_name'];
      $this->description = $cachedEnvironment->data['description'];
      $this->fgColor = $cachedEnvironment->data['fg_color'];
      $this->bgColor = $cachedEnvironment->data['bg_color'];
      $this->isLoaded = TRUE;
    }
    elseif ($forcedEnvironmentId = Settings::get('ikto_environment_indicator_force_environment')) {
      /** @var \Drupal\ikto_environment_indicator\Entity\EnvironmentIndicatorInterface $forcedEnvironment */
      $forcedEnvironment = $this->storage->load($forcedEnvironmentId);
      if ($forcedEnvironment) {
        $this->setEnvironment($forcedEnvironment);
      }
    }
  }

  /**
   * Saves current environment parameters.
   */
  public function save() {
    $data = [
      'machine_name'  => $this->machineName,
      'display_name'  => $this->displayName,
      'description'   => $this->description,
      'fg_color'      => $this->fgColor,
      'bg_color'      => $this->bgColor,
    ];

    $this->cache->set(
      self::CACHE_KEY,
      $data,
      $this->getCacheMaxAge(),
      $this->getCacheTags()
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getMachineName() {
    return $this->machineName;
  }

  /**
   * {@inheritdoc}
   */
  public function getDisplayName() {
    return $this->displayName;
  }

  /**
   * {@inheritdoc}
   */
  public function getDisplayNameFull() {
    $displayNameFull = $this->displayName;

    $buildInfo = Settings::get('ikto_environment_indicator_force_build_info');
    if (!$buildInfo) {
      $buildInfo = $this->gitInfo->getGitInfo();
    }
    if ($buildInfo) {
      $displayNameFull .= ' (' . $buildInfo . ')';
    }

    return $displayNameFull;
  }

  /**
   * {@inheritdoc}
   */
  public function getDescription() {
    return $this->description;
  }

  /**
   * {@inheritdoc}
   */
  public function getFgColor() {
    return $this->fgColor;
  }

  /**
   * {@inheritdoc}
   */
  public function getBgColor() {
    return $this->bgColor;
  }

  /**
   * {@inheritdoc}
   */
  public function getIsLoaded() {
    return $this->isLoaded;
  }

  /**
   * {@inheritdoc}
   */
  public function setEnvironment(EnvironmentIndicatorInterface $env) {
    $this->machineName = $env->id();
    $this->displayName = $env->label();
    $this->description = $env->getDescription();
    $this->fgColor = $env->getFgColor();
    $this->bgColor = $env->getBgColor();
    $this->isLoaded = TRUE;
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheContexts() {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheTags() {
    $cacheTags = ['config:ikto_environment_indicator.settings'];

    $environments = $this->storage->loadMultiple();
    foreach ($environments as $entity) {
      $cacheTags = Cache::mergeTags($cacheTags, $entity->getCacheTags());
    }

    return $cacheTags;
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheMaxAge() {
    return Cache::PERMANENT;
  }

  /**
   * {@inheritdoc}
   */
  public function access($operation, AccountInterface $account = NULL, $return_as_object = FALSE) {
    if ($operation != 'view') {
      if ($return_as_object) {
        return new AccessResultForbidden();
      }
      else {
        return FALSE;
      }
    }

    $access = $account->hasPermission('access ikto environment indicator');

    if (!$access) {
      $access = $account->hasPermission('access ikto environment indicator ' . $this->getMachineName());
    }

    if ($return_as_object) {
      if ($access) {
        return new AccessResultAllowed();
      }
      else {
        return new AccessResultForbidden();
      }
    }

    return $access;
  }

}
