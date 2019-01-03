<?php

namespace Drupal\rsvp;

use Drupal\node\NodeInterface;
use Drupal\Core\Database\Connection;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * RSVP Event class to handle actions related to submissions
 */
class Event {
  protected $event;
  protected $email;
  protected $name;
  protected $connection;

  public function __construct(Connection $connection) {
    $this->connection = $connection;
  }

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('database')
    );
  }

  public function saveAtendee(NodeInterface $event, String $email, String $name) {
    $this->event = $event;
    $this->email = $email;
    $this->name = $name;

    // make sure the same email will not be saved twice
    if (!$this->validateEmail()) {
      drupal_set_message(t('This e-mail has been registered already.'),'warning');
      return false;
    }

    $result = $this->connection->insert('rsvp')
      ->fields([
        'name' => $this->name,
        'email' => $this->email,
        'nid' => $this->event->id(),
        'timestamp' => REQUEST_TIME,
      ])
      ->execute();

    if (!$result) {
      \Drupal::logger('rsvp')->error('An error occurred while trying to save this submission: name: @name | email: @email | event: @event',
        ['@name' => $this->name, '@email' => $this->email, '@event' => $this->event->id()]
      );
      drupal_set_message(t('An error occurred while trying to save this submission. Please try again later or contact the administrator.'),'error');
    }
  }

  protected function validateEmail() {
    $query = $this->connection->select('rsvp', 'r');
    $result = $query->condition('r.email', $this->email)
      ->condition('r.nid', $this->event->id())
      ->fields('r', ['email'])
      ->execute();
    return empty($result->fetchAllKeyed());
  }

  public function getAll($page = 1) {
    $query = $this->connection->select('rsvp', 'r');
    $query->fields('r', ['nid','name','email', 'timestamp']);
    $limit  = $page * 10;
    $pager = $query->extend('Drupal\Core\Database\Query\PagerSelectExtender')->limit($limit);
    return $pager->execute()->fetchAll();
  }
}