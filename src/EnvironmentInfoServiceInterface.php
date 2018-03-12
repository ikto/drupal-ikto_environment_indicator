<?php

namespace Drupal\ikto_environment_indicator;

use Drupal\Core\Access\AccessibleInterface;
use Drupal\Core\Cache\CacheableDependencyInterface;
use Drupal\ikto_environment_indicator\Entity\EnvironmentIndicatorInterface;

/**
 * Defines an interface for environment indicator service.
 */
interface EnvironmentInfoServiceInterface extends CacheableDependencyInterface, AccessibleInterface {

  /**
   * Gets the machine name of active environment.
   *
   * @return string
   *   The machine name of active environment.
   */
  public function getMachineName();

  /**
   * Gets the human-readable name of active environment.
   *
   * @return string
   *   The human-readable of active environment.
   */
  public function getDisplayName();

  /**
   * Gets full representation of human-readable name of active environment.
   *
   * @return string
   *   Full representation of human-readable name of active environment.
   */
  public function getDisplayNameFull();

  /**
   * Gets the description of active environment.
   *
   * @return string
   *   The description of active environment.
   */
  public function getDescription();

  /**
   * Gets the foreground color of active environment.
   *
   * @return string
   *   The foreground color of active environment (hex).
   */
  public function getFgColor();

  /**
   * Gets the background color of active environment.
   *
   * @return string
   *   The background color of active environment (hex).
   */
  public function getBgColor();

  /**
   * Indicates whether active environment was loaded or not.
   *
   * @return bool
   *   The flag, indicates whether active environment was loaded or not.
   */
  public function getIsLoaded();

  /**
   * Sets environment info from EnvironmentIndicator entity.
   *
   * @param \Drupal\ikto_environment_indicator\Entity\EnvironmentIndicatorInterface $env
   *   The environment indicator entity.
   */
  public function setEnvironment(EnvironmentIndicatorInterface $env);

}
