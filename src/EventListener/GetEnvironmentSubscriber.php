<?php

namespace Drupal\ikto_environment_indicator\EventListener;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\ikto_environment_indicator\EnvironmentInfoServiceInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Defines request subscriber for determining an active environment.
 */
class GetEnvironmentSubscriber implements EventSubscriberInterface {

  /**
   * The config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $config;

  /**
   * The environment indicator storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $storage;

  /**
   * The environment info service.
   *
   * @var \Drupal\ikto_environment_indicator\EnvironmentInfoServiceInterface
   */
  protected $ev;

  /**
   * GetEnvironmentSubscriber constructor.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config
   *   The config factory.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $em
   *   The entity type manager.
   * @param \Drupal\ikto_environment_indicator\EnvironmentInfoServiceInterface $ev
   *   The environment info service.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   *   If ikto_environment_indicator entity is missing.
   */
  public function __construct(
    ConfigFactoryInterface $config,
    EntityTypeManagerInterface $em,
    EnvironmentInfoServiceInterface $ev
  ) {
    $this->config = $config;
    $this->storage = $em->getStorage('ikto_environment_indicator');
    $this->ev = $ev;
  }

  /**
   * Gets the current environment info from current request.
   *
   * @param \Symfony\Component\HttpKernel\Event\GetResponseEvent $event
   *   The kernel request event instance.
   */
  public function onKernelRequestEnvironment(GetResponseEvent $event) {

    if (!$this->ev->getIsLoaded()) {
      $env = $this->getEnvironmentIndicatorForHost($event->getRequest()->getHttpHost());

      /** @var \Drupal\ikto_environment_indicator\Entity\EnvironmentIndicatorInterface $env */
      if ($env) {
        $this->ev->setEnvironment($env);
        $this->ev->save();
      }
    }
  }

  /**
   * Gets the current environment info from the hostname.
   *
   * @param string $host
   *   The hostname.
   *
   * @return \Drupal\ikto_environment_indicator\Entity\EnvironmentIndicatorInterface
   *   The active environment entity.
   */
  protected function getEnvironmentIndicatorForHost($host) {
    $environments = $this->storage->loadMultiple();
    uasort($environments, array('Drupal\ikto_environment_indicator\Entity\EnvironmentIndicator', 'sort'));

    $env = NULL;
    foreach ($environments as $env) {
      /** @var \Drupal\ikto_environment_indicator\Entity\EnvironmentIndicatorInterface $env */
      $url = $env->getUrl();
      if (empty($url)) {
        break;
      }

      $url_info = parse_url($url);
      if (!empty($url_info['host']) && ($url_info['host'] == $host)) {
        break;
      }
    }

    return $env;
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events[KernelEvents::REQUEST][] = ['onKernelRequestEnvironment', 0];
    return $events;
  }

}
