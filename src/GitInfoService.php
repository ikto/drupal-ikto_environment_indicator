<?php

namespace Drupal\ikto_environment_indicator;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Config\ConfigFactoryInterface;

class GitInfoService implements GitInfoServiceInterface {

  const CACHE_KEY_BASE_COMMAND_EXISTS = 'ikto_environment_indicator:command_exists:';
  const CACHE_KEY_GIT_INFO = 'ikto_environment_indicator:git_info';

  /**
   * @var ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * @var CacheBackendInterface
   */
  protected $cache;

  public function __construct(ConfigFactoryInterface $configFactory, CacheBackendInterface $cache) {
    $this->configFactory = $configFactory;
    $this->cache = $cache;
  }

  /**
   * {@inheritdoc}
   */
  public function getGitInfo() {
    if (!$this->configFactory->get('ikto_environment_indicator.settings')->get('git')) {
      return NULL;
    }

    $cachedGitInfo = $this->cache->get(static::CACHE_KEY_GIT_INFO);
    if ($cachedGitInfo && !empty($cachedGitInfo->data)) {
      return $cachedGitInfo->data;
    }

    $release = NULL;
    // Show the git branch, if it exists.
    if (
      $this->isCommandExist('git')
      && $git_describe = $this->executeOsCommand('git describe --all')
    ) {
      // Execute "git describe --all" and get the last part of heads/7.x-2.x as the
      // tag/branch.
      if (empty($git_describe)) {
        return NULL;
      }
      $tag_branch_parts = explode('/', $git_describe);
      $release = end($tag_branch_parts);
    }

    $release = trim($release);

    $this->cache->set(static::CACHE_KEY_GIT_INFO, $release);

    return $release;
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
  protected function isCommandExist($command) {
    if ($obj = $this->cache->get(static::CACHE_KEY_BASE_COMMAND_EXISTS . $command)) {
      return $obj->data;
    }

    $where_is_command = (PHP_OS == 'WINNT') ? 'where' : 'which';

    $command_return = $this->executeOsCommand("$where_is_command $command");
    $output = !empty($command_return);
    $this->cache->set(static::CACHE_KEY_BASE_COMMAND_EXISTS . $command, $output);

    return $output;
  }

  /**
   * Executes a system command and return the results.
   *
   * @param string $command
   *   The command to execute.
   *
   * @return string
   *   The results of the string execution.
   */
  protected function executeOsCommand($command) {
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
}
