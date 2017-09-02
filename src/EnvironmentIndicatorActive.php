<?php

namespace Drupal\ikto_environment_indicator;

use Drupal\Core\Cache\Cache;
use Drupal\Core\Cache\CacheBackendInterface;

class EnvironmentIndicatorActive {

  const CACHE_KEY = 'ikto_environment_indicator:active_environment';

  /**
   * The machine name of the active environment.
   *
   * @var string
   */
  protected $id;

  /**
   * The human-readable name of the active environment.
   *
   * @var string
   */
  protected $name;

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
  protected $fg_color;

  /**
   * The background color of the active environment.
   *
   * @var string
   */
  protected $bg_color;

  /**
   * The flag which indicates whether active indicator was loaded or not.
   *
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
      $this->setId($active_environment->data['id']);
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
      'id' => $this->getId(),
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
   * Gets the machine name of active environment.
   *
   * @return string
   */
  public function getId() {
    return $this->id;
  }

  /**
   * Gets the machine name of active environment.
   *
   * @param string $id
   */
  public function setId($id) {
    $this->id = $id;
  }

  /**
   * Gets the human-readable name of active environment.
   *
   * @return string
   */
  public function getName() {
    return $this->name;
  }

  /**
   * Sets the human-readable name of active environment.
   *
   * @param string $name
   */
  public function setName($name) {
    $this->name = $name;
  }

  /**
   * Gets the description of active environment.
   *
   * @return string
   */
  public function getDescription() {
    return $this->description;
  }

  /**
   * Gets the description of active environment.
   *
   * @param string $description
   */
  public function setDescription($description) {
    $this->description = $description;
  }

  /**
   * Gets the foreground color of active environment.
   *
   * @return string
   */
  public function getFgColor() {
    return $this->fg_color;
  }

  /**
   * Sets the foreground color of active environment.
   *
   * @param string $fg_color
   */
  public function setFgColor($fg_color) {
    $this->fg_color = $fg_color;
  }

  /**
   * Gets the background color of active environment.
   *
   * @return string
   */
  public function getBgColor() {
    return $this->bg_color;
  }

  /**
   * Sets the background color of active environment.
   *
   * @param string $bg_color
   */
  public function setBgColor($bg_color) {
    $this->bg_color = $bg_color;
  }

  /**
   * Indicates whether active environment was loaded or not.
   *
   * @return boolean
   */
  public function getIsLoaded() {
    return $this->is_loaded;
  }
}
