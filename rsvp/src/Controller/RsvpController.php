<?php

namespace Drupal\rsvp\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Request;
use Drupal\node\Entity\Node;

class RsvpController extends ControllerBase {
  public function fullReport(Request $request) {
    $eventHandler = \Drupal::service('rsvp.event');
    $page = $request->get('page') ? $request->get('page') + 1 : 1;
    $results = $eventHandler->getAll($page);

    $output = [];
    foreach ($results as $result) {
      $output[] = [
        'event' => ($event = Node::load($result->nid)) ? $event->getTitle() : $result->nid,
        'name' => $result->name,
        'email' => $result->email,
        'date' => date('d/M/Y',$result->timestamp),
      ];
    }

    $build['table'] = [
      '#type' => 'table',
      '#header' => [t('Event'), t('Name'), t('E-mail'), t('Date')],
      '#rows' => $output,
      '#empty' => t('No users found'),
    ];

    $build['pager'] = [
      '#type' => 'pager'
    ];

    return $build;
  }
}