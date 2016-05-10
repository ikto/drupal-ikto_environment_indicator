<?php

namespace Drupal\ikto_environment_indicator\EventSubscriber;

use Drupal\Core\Cache\Cache;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\ikto_environment_indicator\Entity\EnvironmentIndicator;
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
   * @var CacheBackendInterface
   */
  protected $cache;

  public function __construct(ConfigFactoryInterface $config, EntityTypeManagerInterface $em, CacheBackendInterface $cache) {
    $this->config = $config;
    $this->em = $em;
    $this->cache = $cache;
  }

  public function onKernelRequestEnvironment(GetResponseEvent $event) {

    $cachedEnvironment = $this->cache->get('ikto_environment_indicator:active_environment');

    if (!$cachedEnvironment) {
      $env = $this->getEnvironmentIndicatorForHost($event->getRequest()->getHttpHost());

      /**
       * @var EnvironmentIndicator $env
       */
      if ($env) {

        $data = [
          'name' => $env->label(),
          'fg_color' => $env->getFgColor(),
          'bg_color' => $env->getBgColor(),
        ];

        $this->cache->set(
          'ikto_environment_indicator:active_environment',
          $data,
          Cache::PERMANENT,
          Cache::mergeTags(
            ['config:ikto_environment_indicator.settings'],
            _ikto_environment_indicator_switcher_cache_tags()
          )
        );
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
