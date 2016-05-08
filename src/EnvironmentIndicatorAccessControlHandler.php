<?php

/**
 * @file
 * Contains \Drupal\ikto_environment_indicator\EnvironmentIndicatorAccessControlHandler.
 */

namespace Drupal\ikto_environment_indicator;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Defines an access controller for the environment entity.
 *
 * @see \Drupal\ikto_environment_indicator\Plugin\Core\Entity\EnvironmentIndicator.
 */
class EnvironmentIndicatorAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    return AccessResult::allowedIf($account->hasPermission('administer ikto environment indicator settings'));
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIf($account->hasPermission('administer ikto environment indicator settings'));
  }

}
