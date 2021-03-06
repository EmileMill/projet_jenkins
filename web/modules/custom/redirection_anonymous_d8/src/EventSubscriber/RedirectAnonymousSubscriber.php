<?php

namespace Drupal\redirection_anonymous_d8\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;


/**
 * Event subscriber subscribing to KernelEvents::REQUEST.
 */
class RedirectAnonymousSubscriber implements EventSubscriberInterface {

  public function __construct() {
    $this->account = \Drupal::currentUser();
  }

  public function checkAuthStatus(GetResponseEvent $event) {
    global $base_url;
    if (
      $this->account->isAnonymous() &&
      \Drupal::routeMatch()->getRouteName() != 'user.login' &&
      \Drupal::routeMatch()->getRouteName() != 'user.reset' &&
      \Drupal::routeMatch()->getRouteName() != 'user.reset.form' &&
      \Drupal::routeMatch()->getRouteName() != 'user.reset.login' &&
      \Drupal::routeMatch()->getRouteName() != 'user.pass' ) {
      // add logic to check other routes you want available to anonymous users,
      // otherwise, redirect to login page.
      $route_name = \Drupal::routeMatch()->getRouteName();
      if (strpos($route_name, 'view') === 0 && strpos($route_name, 'rest_') !== FALSE) {
        return;
      }
      $response = new RedirectResponse($base_url . '/user/login', 301);
      $event->setResponse($response);
      $event->stopPropagation();
      return;
    }
  }

  public static function getSubscribedEvents() {
    $events[KernelEvents::REQUEST][] = array('checkAuthStatus');
    return $events;
  }


}
