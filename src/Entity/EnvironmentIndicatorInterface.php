<?php

namespace Drupal\ikto_environment_indicator\Entity;

/**
 * Defines an interface of the environment indicator entity.
 */
interface EnvironmentIndicatorInterface {

  /**
   * Gets the machine name of the environment indicator.
   *
   * @return string
   *   The machine name of the environment.
   */
  public function id();

  /**
   * Gets the human-readable name of the environment indicator.
   *
   * @return string
   *   The name of the environment.
   */
  public function label();

  /**
   * Gets the description of the environment indicator.
   *
   * @return string
   *   The description of the environment.
   */
  public function getDescription();

  /**
   * Gets the URL of the environment indicator.
   *
   * @return string
   *   The URL of the environment.
   */
  public function getUrl();

  /**
   * Gets the foreground color.
   *
   * @return string
   *   The foreground color of the environment (hex).
   */
  public function getFgColor();

  /**
   * Gets the background color.
   *
   * @return string
   *   The background color of the environment (hex).
   */
  public function getBgColor();

  /**
   * Gets the weight.
   *
   * @return int
   *   The weight of the environment.
   */
  public function getWeight();

}
