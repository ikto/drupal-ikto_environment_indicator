<?php

namespace Drupal\ikto_environment_indicator\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;
use Drupal\Core\Config\Entity\ConfigEntityInterface;
use Drupal\Core\Entity\Annotation\EntityType;
use Drupal\Core\Annotation\Translation;

/**
 * Defines a Environment configuration entity.
 *
 * @ConfigEntityType(
 *   id = "ikto_environment_indicator",
 *   label = @Translation("Environment Switcher"),
 *   handlers = {
 *     "storage" = "Drupal\Core\Config\Entity\ConfigEntityStorage",
 *     "access" = "Drupal\ikto_environment_indicator\EnvironmentIndicatorAccessControlHandler",
 *     "list_builder" = "Drupal\ikto_environment_indicator\EnvironmentIndicatorListBuilder",
 *     "form" = {
 *       "default" = "Drupal\ikto_environment_indicator\Form\EnvironmentIndicatorForm",
 *       "delete" = "Drupal\Core\Entity\EntityDeleteForm"
 *     }
 *   },
 *   admin_permission = "administer ikto environment indicator settings",
 *   config_prefix = "switcher",
 *   static_cache = TRUE,
 *   entity_keys = {
 *     "id" = "machine",
 *     "label" = "human_name",
 *     "weight" = "weight"
 *   },
 *   config_export = {
 *     "machine",
 *     "description",
 *     "name",
 *     "url",
 *     "fg_color",
 *     "bg_color",
 *     "weight",
 *   },
 *   links = {
 *     "edit-form" = "/admin/config/development/ikto-environment-indicator/manage/{ikto_environment_indicator}",
 *     "edit-permissions-form" = "/admin/people/permissions/{user_role}",
 *     "delete-form" = "/admin/config/development/ikto-environment-indicator/manage/{ikto_environment_indicator}/delete",
 *     "collection" = "/admin/config/development/ikto-environment-indicator"
 *   }
 * )
 */
class EnvironmentIndicator extends ConfigEntityBase implements EnvironmentIndicatorInterface {

  /**
   * The machine-readable ID for the configurable.
   *
   * @var string
   */
  public $machine;

  /**
   * The human-readable label for the configurable.
   *
   * @var string
   */
  public $name;

  /**
   * The description for the configurable.
   *
   * @var string
   */
  public $description;

  /**
   * The URL to switch to.
   *
   * @var string
   */
  public $url;

  /**
   * The color code for the indicator.
   *
   * @var string
   */
  public $fg_color;

  /**
   * The color code for the indicator.
   *
   * @var string
   */
  public $bg_color;

  /**
   * The weight of the indicator.
   *
   * @var int
   */
  public $weight;

  /**
   * {@inheritdoc}
   */
  public function id() {
    return $this->get('machine');
  }

  /**
   * {@inheritdoc}
   */
  public function label() {
    return $this->get('name');
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
  public function getUrl() {
    return $this->get('url');
  }

  /**
   * {@inheritdoc}
   */
  public function getFgColor() {
    return $this->get('fg_color');
  }

  /**
   * {@inheritdoc}
   */
  public function getBgColor() {
    return $this->get('bg_color');
  }

  /**
   * {@inheritdoc}
   */
  public function getWeight() {
    return $this->get('weight');
  }

  /**
   * Gets the machine name.
   *
   * @param string $machine
   *   The machine name of the environment.
   */
  public function setMachine($machine) {
    $this->set('machine', $machine);
  }

  /**
   * Sets the name.
   *
   * @param string $name
   *   The name of the environment.
   */
  public function setName($name) {
    $this->set('name', $name);
  }

  /**
   * Sets the description for the configurable.
   *
   * @param string $description
   *   The description of the environment.
   */
  public function setDescription($description) {
    $this->description = $description;
  }

  /**
   * Sets the URL.
   *
   * @param string $url
   *   The URL of the environment.
   */
  public function setUrl($url) {
    $this->set('url', $url);
  }

  /**
   * Sets the foreground color.
   *
   * @param string $fg_color
   *   The foreground color of the environment (hex).
   */
  public function setFgColor($fg_color) {
    $this->set('fg_color', $fg_color);
  }

  /**
   * Sets the background color.
   *
   * @param string $bg_color
   *   The background color of the environment (hex).
   */
  public function setBgColor($bg_color) {
    $this->set('bg_color', $bg_color);
  }

  /**
   * Sets the weight.
   *
   * @param int $weight
   *   The weight of the environment.
   */
  public function setWeight($weight) {
    $this->set('weight', $weight);
  }

}
