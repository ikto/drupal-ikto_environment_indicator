<?php

namespace Drupal\ikto_environment_indicator\EventSubscriber;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\ikto_environment_indicator\Entity\EnvironmentIndicator;
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
   * @var EntityTypeManagerInterface
   */
  protected $em;

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
    $this->em = $em;
    $this->ev = $ev;
  }

  public function onKernelRequestEnvironment(GetResponseEvent $event) {

    if (!$this->ev->getIsLoaded()) {
      $env = $this->getEnvironmentIndicatorForHost($event->getRequest()->getHttpHost());

      /**
       * @var EnvironmentIndicator $env
       */
      if ($env) {
        $this->ev->setName($env->label());
        $this->ev->setDescription($env->getDescription());
        $this->ev->setFgColor($env->getFgColor());
        $this->ev->setBgColor($env->getBgColor());
      }
    }
  }

  protected function getEnvironmentIndicatorForHost($host) {
    $envs = $this->em->getStorage('ikto_environment_indicator')->loadMultiple();
    uasort($envs, array('Drupal\ikto_environment_indicator\Entity\EnvironmentIndicator', 'sort'));

    $env = NULL;
    foreach ($envs as $env) {
      /**
       * @var EnvironmentIndicator $env
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
