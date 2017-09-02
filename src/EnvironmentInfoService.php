<?php

namespace Drupal\ikto_environment_indicator;

use Drupal\Core\Cache\Cache;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\ikto_environment_indicator\Entity\EnvironmentIndicatorInterface;

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
   * @var boolean
   */
  protected $isLoaded = FALSE;

  /**
   * @var CacheBackendInterface
   */
  protected $cache;

  /**
   * @var GitInfoServiceInterface
   */
  protected $gitInfo;

  public function __construct(CacheBackendInterface $cache, GitInfoServiceInterface $gitInfo) {
    $this->cache = $cache;
    $this->gitInfo = $gitInfo;
  }

  /**
   * Initializes current environment parameters.
   */
  public function init() {
    $cachedEnvironment = $this->cache->get(self::CACHE_KEY);

    if ($cachedEnvironment && !empty($cachedEnvironment->data)) {
      $this->machineName  = $cachedEnvironment->data['machine_name'];
      $this->displayName  = $cachedEnvironment->data['display_name'];
      $this->description  = $cachedEnvironment->data['description'];
      $this->fgColor      = $cachedEnvironment->data['fg_color'];
      $this->bgColor      = $cachedEnvironment->data['bg_color'];
      $this->isLoaded     = TRUE;
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
      Cache::PERMANENT,
      Cache::mergeTags(
        ['config:ikto_environment_indicator.settings'],
        _ikto_environment_indicator_switcher_cache_tags()
      )
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
   * Gets full representation of human-readable name of active environment.
   *
   * @return string
   */
  public function getDisplayNameFull() {
    $displayNameFull = $this->displayName;

    $gitInfo = $this->gitInfo->getGitInfo();
    if ($gitInfo) {
      $displayNameFull .= ' (' . $gitInfo . ')';
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
    $this->machineName  = $env->id();
    $this->displayName  = $env->label();
    $this->description  = $env->getDescription();
    $this->fgColor      = $env->getFgColor();
    $this->bgColor      = $env->getBgColor();
    $this->isLoaded     = TRUE;
  }
}
