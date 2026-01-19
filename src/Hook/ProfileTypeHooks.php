<?php

namespace Drupal\profile_ext\Hook;

use Drupal\Core\Hook\Attribute\Hook;
use Drupal\profile_ext\Entity\Profile;

/**
 * Hook implementations for changing the profile class.
 */
class ProfileTypeHooks {

  /**
   * Adjust user profile links.
   */
  #[Hook('entity_type_alter')]
  public function changeProfileType(array $entity_types): void {
    if (isset($entity_types['profile'])) {
      $definition = $entity_types['profile'];
      $definition->setClass(Profile::class);
    }
  }

}
