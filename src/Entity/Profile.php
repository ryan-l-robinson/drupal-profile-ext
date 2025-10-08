<?php

namespace Drupal\profile_ext\Entity;

use Drupal\profile\Entity\Profile as BaseProfile;

/**
 * Overrides the Profile entity to provide a custom label.
 */
class Profile extends BaseProfile {

  /**
   * {@inheritdoc}
   */
  public function label(): string {
    // Change the label to the staff member's name.
    return \Drupal::service('profile_ext.utils')->getName($this) . " " . $this->t("(Profile)");
  }

}
