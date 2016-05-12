<?php

namespace Drupal\ikto_environment_indicator;

use Drupal\Core\Cache\Cache;
use Drupal\Core\Cache\CacheBackendInterface;

class EnvironmentIndicatorActive {

  const CACHE_KEY = 'ikto_environment_indicator:active_environment';

  /**
   * @var string
   */
  protected $name;

  /**
   * @var string
   */
  protected $description;

  /**
   * @var string
   */
  protected $fg_color;

  /**
   * @var string
   */
  protected $bg_color;

  /**
   * @var boolean
   */
  protected $is_loaded = FALSE;

  /**
   * @var CacheBackendInterface
   */
  protected $cache;

  public function __construct(CacheBackendInterface $cache) {
    $this->cache = $cache;
  }

  /**
   * Initializes current environment parameters.
   */
  public function init() {
    $active_environment = $this->cache->get(self::CACHE_KEY);

    if ($active_environment && !empty($active_environment->data)) {
      $this->setName($active_environment->data['name']);
      $this->setDescription($active_environment->data['description']);
      $this->setFgColor($active_environment->data['fg_color']);
      $this->setBgColor($active_environment->data['bg_color']);
      $this->is_loaded = TRUE;
    }
  }

  /**
   * Saves current environment parameters.
   */
  public function save() {
    $data = [
      'name' => $this->getName(),
      'description' => $this->getDescription(),
      'fg_color' => $this->getFgColor(),
      'bg_color' => $this->getBgColor(),
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
   * Gets the name of active environment.
   * @return string
   */
  public function getName() {
    return $this->name;
  }

  /**
   * Sets the name of active environment.
   * @param string $name
   */
  public function setName($name) {
    $this->name = $name;
  }

  /**
   * @return string
   */
  public function getDescription() {
    return $this->description;
  }

  /**
   * @param string $description
   */
  public function setDescription($description) {
    $this->description = $description;
  }

  /**
   * Gets the foreground color of active environment.
   * @return string
   */
  public function getFgColor() {
    return $this->fg_color;
  }

  /**
   * Sets the foreground color of active environment.
   * @param string $fg_color
   */
  public function setFgColor($fg_color) {
    $this->fg_color = $fg_color;
  }

  /**
   * Gets the background color of active environment.
   * @return string
   */
  public function getBgColor() {
    return $this->bg_color;
  }

  /**
   * Sets the background color of active environment.
   * @param string $bg_color
   */
  public function setBgColor($bg_color) {
    $this->bg_color = $bg_color;
  }

  /**
   * @return boolean
   */
  public function getIsLoaded() {
    return $this->is_loaded;
  }
}
