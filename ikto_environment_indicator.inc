<?php

use Drupal\ikto_environment_indicator\Entity\EnvironmentIndicator;
use Drupal\Core\Cache\Cache;

/**
 * Get the cache tags for the environment indicator switcher.
 *
 * @return string[]
 *   The cache tags.
 */
function _ikto_environment_indicator_switcher_cache_tags() {
  /* @var EnvironmentIndicator[] $environment_entities */
  if (!$environment_entities = EnvironmentIndicator::loadMultiple()) {
    return [];
  }

  $cache_tags = [];
  foreach ($environment_entities as $entity) {
    $cache_tags = Cache::mergeTags($cache_tags, $entity->getCacheTags());
  }

  return $cache_tags;
}

/**
 * Helper function that checks if there is toolbar integration.
 *
 * @return bool
 *   TRUE if integration is enabled. FALSE otherwise.
 */
function _ikto_environment_indicator_toolbar_intergration_is_enabled() {
  $access = \Drupal::moduleHandler()->moduleExists('toolbar');
  $access = $access && in_array('toolbar', \Drupal::config('ikto_environment_indicator.settings')
      ->get('toolbar_integration'));
  $access = $access && \Drupal::currentUser()
      ->hasPermission('access toolbar');

  return $access;
}
