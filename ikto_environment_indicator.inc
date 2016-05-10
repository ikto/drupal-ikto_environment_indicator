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
 * Helper function that checks if there is toolabr integration.
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

/**
 * Helper function to get the git information.
 *
 * @return string
 *   The git branch or tag.
 */
function _ikto_environment_indicator_get_git_info() {
  if (!\Drupal::config('ikto_environment_indicator.settings')->get('git')) {
    return NULL;
  }

  $release = NULL;
  // Show the git branch, if it exists.
  if (
    _ikto_environment_indicator_command_exists('git')
    && $git_describe = _ikto_environment_indicator_execute_os_command('git describe --all')
  ) {
    // Execute "git describe --all" and get the last part of heads/7.x-2.x as the
    // tag/branch.
    if (empty($git_describe)) {
      return NULL;
    }
    $tag_branch_parts = explode('/', $git_describe);
    $release = end($tag_branch_parts);
  }

  return trim($release);
}

/**
 * Execute a system command and return the results.
 *
 * @param string $command
 *   The command to execute.
 *
 * @return string
 *   The results of the string execution.
 */
function _ikto_environment_indicator_execute_os_command($command) {
  $process = proc_open($command, [
    // STDIN.
    0 => ['pipe', 'r'],
    // STDOUT.
    1 => ['pipe', 'w'],
    // STDERR.
    2 => ['pipe', 'w'],
  ], $pipes);
  if ($process === FALSE) {
    return FALSE;
  }
  $stdout = stream_get_contents($pipes[1]);
  stream_get_contents($pipes[2]);
  fclose($pipes[1]);
  fclose($pipes[2]);
  proc_close($process);

  return $stdout;
}

/**
 * Determines if a command exists on the current environment.
 *
 * @param string $command
 *   The command to check.
 *
 * @return bool
 *   TRUE if the command has been found; otherwise, FALSE.
 */
function _ikto_environment_indicator_command_exists($command) {
  if ($obj = \Drupal::cache()->get('ikto_environment_indicator_command_exists:' . $command)) {
    return $obj->data;
  }
  $where_is_command = (PHP_OS == 'WINNT') ? 'where' : 'which';

  $command_return = _ikto_environment_indicator_execute_os_command("$where_is_command $command");
  $output = !empty($command_return);
  \Drupal::cache()->set('ikto_environment_indicator_command_exists:' . $command, $output);
  return $output;
}
