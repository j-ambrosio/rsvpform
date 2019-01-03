<?php

namespace Drupal\rsvp\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * @Block(
 *  id = "rsvp_block",
 *  admin_label = @Translation("RSVP Block"),
 *  category = @Translation("Forms")
 * )
 */
class RsvpBlock extends BlockBase {

  /**
   * {@inheritDoc}
   */
  public function build() {
    return \Drupal::formBuilder()->getForm('Drupal\rsvp\Plugin\Form\RsvpForm');
  }
}