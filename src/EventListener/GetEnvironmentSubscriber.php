<?php

namespace Drupal\ikto_environment_indicator\EventListener;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\ikto_environment_indicator\Entity\EnvironmentIndicatorInterface;
use Drupal\ikto_environment_indicator\EnvironmentIndicatorActive;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class GetEnvironmentSubscriber implements EventSubscriberInterface {

  /**
   * @var ConfigFactoryInterface
   */
  protected $config;

  /**
   * @var EntityStorageInterface
   */
  protected $storage;

  /**
   * @var EnvironmentIndicatorActive
   */
  protected $ev;

  public function __construct(
    ConfigFactoryInterface $config,
    EntityTypeManagerInterface $em,
    EnvironmentIndicatorActive $ev
  ) {
    $this->config = $config;
    $this->storage = $em->getStorage('ikto_environment_indicator');
    $this->ev = $ev;
  }

  public function onKernelRequestEnvironment(GetResponseEvent $event) {

    if (!$this->ev->getIsLoaded()) {
      $env = $this->getEnvironmentIndicatorForHost($event->getRequest()->getHttpHost());

      /**
       * @var EnvironmentIndicatorInterface $env
       */
      if ($env) {
        $this->ev->setId($env->id());
        $this->ev->setName($env->label());
        $this->ev->setDescription($env->getDescription());
        $this->ev->setFgColor($env->getFgColor());
        $this->ev->setBgColor($env->getBgColor());
      }
    }
  }

  protected function getEnvironmentIndicatorForHost($host) {
    $environments = $this->storage->loadMultiple();
    uasort($environments, array('Drupal\ikto_environment_indicator\Entity\EnvironmentIndicator', 'sort'));

    $env = NULL;
    foreach ($environments as $env) {
      /**
       * @var EnvironmentIndicatorInterface $env
       */
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
