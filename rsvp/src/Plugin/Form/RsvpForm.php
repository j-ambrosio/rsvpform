<?php

namespace Drupal\rsvp\Plugin\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * RSVP Form
 * - requires user's name and e-mail
 */
class RsvpForm extends FormBase {
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'rsvp_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Name'),
      '#description' => $this->t('Please insert the atendee\'s full name.'),
      '#required' => true
    ];
    $form['email'] = [
      '#type' => 'email',
      '#title' => $this->t('E-mail'),
      '#description' => $this->t('Please insert the atendee\'s e-mail address.'),
      '#required' => true
    ];
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $eventHandler = \Drupal::service('rsvp.event');
    if ($event = \Drupal::routeMatch()->getParameter('node')) {
      if ($eventHandler->saveAtendee($event, $form_state->getValue('email'), $form_state->getValue('name'))) {
        drupal_set_message($this->t('User registered successfully.'));
      }
    }
    else {
      drupal_set_message($this->t('This form must be inserted in some node\'s page.'));
    }
  }
}