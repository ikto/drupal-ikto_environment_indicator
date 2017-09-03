<?php

namespace Drupal\ikto_environment_indicator;

use Drupal\Core\Access\AccessibleInterface;
use Drupal\Core\Cache\CacheableDependencyInterface;
use Drupal\ikto_environment_indicator\Entity\EnvironmentIndicatorInterface;

interface EnvironmentInfoServiceInterface extends CacheableDependencyInterface, AccessibleInterface {

  /**
   * Gets the machine name of active environment.
   *
   * @return string
   */
  public function getMachineName();

  /**
   * Gets the human-readable name of active environment.
   *
   * @return string
   */
  public function getDisplayName();

  /**
   * Gets full representation of human-readable name of active environment.
   *
   * @return string
   */
  public function getDisplayNameFull();

  /**
   * Gets the description of active environment.
   *
   * @return string
   */
  public function getDescription();

  /**
   * Gets the foreground color of active environment.
   *
   * @return string
   */
  public function getFgColor();

  /**
   * Gets the background color of active environment.
   *
   * @return string
   */
  public function getBgColor();

  /**
   * Indicates whether active environment was loaded or not.
   *
   * @return boolean
   */
  public function getIsLoaded();

  /**
   * Sets environment info from EnvironmentIndicator entity.
   *
   * @param EnvironmentIndicatorInterface $env
   */
  public function setEnvironment(EnvironmentIndicatorInterface $env);
}
