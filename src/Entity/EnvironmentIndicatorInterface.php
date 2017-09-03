<?php

namespace Drupal\ikto_environment_indicator\Entity;

interface EnvironmentIndicatorInterface {

  /**
   * Gets the machine name of the environment indicator.
   *
   * @return string
   */
  public function id();

  /**
   * Gets the human-readable name of the environment indicator.
   *
   * @return string
   */
  public function label();

  /**
   * Gets the description of the environment indicator.
   *
   * @return string
   */
  public function getDescription();

  /**
   * Gets the URL of the environment indicator.
   *
   * @return string
   */
  public function getUrl();

  /**
   * Gets the foreground color.
   *
   * @return string
   */
  public function getFgColor();

  /**
   * Gets the background color.
   *
   * @return string
   */
  public function getBgColor();

  /**
   * Gets the weight.
   *
   * @return integer
   */
  public function getWeight();
}
