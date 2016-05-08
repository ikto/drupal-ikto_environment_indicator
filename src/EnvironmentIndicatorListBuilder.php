<?php

/**
 * @file
 * Contains \Drupal\ikto_environment_indicator\EnvironmentIndicatorListBuilder.
 */

namespace Drupal\ikto_environment_indicator;

use Drupal\Core\Config\Entity\ConfigEntityListBuilder;
use Drupal\Core\Entity\EntityInterface;

/**
 * Provides a listing of environments.
 */
class EnvironmentIndicatorListBuilder extends ConfigEntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function getFormID() {
    return 'ikto_environment_indicator_overview_environments';
  }

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $row['name'] = $this->t('Environment name');
    $row['url'] = $this->t('Environment url');
    $row += parent::buildHeader();

    return $row;
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var \Drupal\ikto_environment_indicator\Entity\EnvironmentIndicator $entity */
    $row = [
      'style' => 'color: ' . $entity->getFgColor() . '; background-color: ' . $entity->getBgColor() . ';',
    ];

    $row['data']['name'] = [
      'data' => $entity->label(),
    ];
    $row['data']['url'] = [
      'data' => $entity->getUrl(),
    ];

    $row['data'] += parent::buildRow($entity);

    return $row;
  }

  /**
   * {@inheritdoc}
   */
  public function render() {
    $build['action_header']['#markup'] = '<h3>' . t('Available actions:') . '</h3>';
    $entities = $this->load();
    // If there are not multiple vocabularies, disable dragging by unsetting the
    // weight key.
    if (count($entities) <= 1) {
      unset($this->weightKey);
    }
    $build = parent::render();
    $build['table']['#empty'] = $this->t('No environment switchers available. <a href=":link">Add environment</a>.', [':link' => \Drupal::url('entity.ikto_environment_indicator.add')]);

    return $build;
  }

}
