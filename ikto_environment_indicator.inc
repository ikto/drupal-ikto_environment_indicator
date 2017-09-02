<?php

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
