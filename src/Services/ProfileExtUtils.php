<?php

namespace Drupal\profile_ext\Services;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\profile\Entity\Profile;

/**
 * Service for utilities supporting the profile_ext module.
 */
class ProfileExtUtils {

  /**
   * Entity Type Manager service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  public function __construct(EntityTypeManagerInterface $entityTypeManager) {
    $this->entityTypeManager = $entityTypeManager;
  }

  /**
   * Gets the name from the profile.
   *
   * Used to replace default labels in various contexts.
   */
  public function getName(EntityInterface $profile): string {

    if ($profile instanceof Profile && $profile->hasField('field_first_name') && $profile->hasField('field_last_name')) {
      // Preferred: set title to name.
      $first_name = $profile->get('field_first_name')->getString();
      $last_name = $profile->get('field_last_name')->getString();
      if (!empty($first_name) && !empty($last_name)) {
        return "$first_name $last_name";
      }

      // Fallback: owner’s display name if available.
      try {
        $owner = $profile->getOwner();
        if (!$owner->isAnonymous()) {
          return $owner->getDisplayName();
        }
      }
      catch (\Throwable $e) {
        // Ignore and fall through.
      }
    }

    // Final fallback: mirror profile's default pattern.
    return $this->getBundleName($profile);

  }

  /**
   * Returns bundle name.
   */
  public function getBundleName(EntityInterface $profile): string {
    $bundle_label = $profile->bundle();
    try {
      $type = $this->entityTypeManager
        ->getStorage('profile_type')
        ->load($profile->bundle());
      if ($type) {
        $bundle_label = $type->label();
      }
    }
    catch (\Throwable $e) {
      // Keep machine name if loading fails.
    }

    $id = $profile->id();
    return sprintf('%s #%s', $bundle_label, $id ?? 'new');
  }

}
