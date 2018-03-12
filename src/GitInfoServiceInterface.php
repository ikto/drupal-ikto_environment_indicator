<?php

namespace Drupal\ikto_environment_indicator;

/**
 * Defines an interface for git info service.
 */
interface GitInfoServiceInterface {

  /**
   * Gets git information.
   *
   * @return string
   *   The git branch or tag.
   */
  public function getGitInfo();

}
