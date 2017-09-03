<?php

namespace Drupal\ikto_environment_indicator;

interface GitInfoServiceInterface {

  /**
   * Gets git information.
   *
   * @return string
   *   The git branch or tag.
   */
  public function getGitInfo();
}
