<?php

namespace Drupal\profile_ext\Plugin\Linkit\Matcher;

use Drupal\Core\Entity\EntityFieldManager;
use Drupal\Core\Entity\Query\QueryInterface;
use Drupal\linkit\Plugin\Linkit\Matcher\EntityMatcher;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides profile linkit matcher.
 *
 * @Matcher(
 *   id = "profile",
 *   label = @Translation("Profile with Filtering"),
 *   target_entity = "profile",
 *   provider = "profile"
 * )
 */
class ProfileMatcher extends EntityMatcher {

  /**
   * Entity field manager service.
   *
   * @var \Drupal\Core\Entity\EntityFieldManager
   */
  protected EntityFieldManager $entityFieldManager;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): static {
    $instance = parent::create($container, $configuration, $plugin_id, $plugin_definition);
    $instance->entityFieldManager = $container->get('entity_field.manager');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  protected function buildEntityQuery($search_string): QueryInterface {
    $query = parent::buildEntityQuery($search_string);

    $field_names = ['field_first_name', 'field_last_name', 'field_department', 'field_job_title'];
    $or_group = $query->orConditionGroup();

    // Verify that the bundle has the fields to add as conditions.
    $bundles = $this->configuration['bundles'] ?? [];

    // If no bundles specified, use all bundles.
    if (empty($bundles)) {
      $bundle_info = $this->entityTypeBundleInfo->getBundleInfo('profile');
      $bundles = array_keys($bundle_info);
    }

    foreach ($field_names as $field_name) {
      foreach ($bundles as $bundle) {
        $field_definitions = $this->entityFieldManager->getFieldDefinitions('profile', $bundle);
        if (isset($field_definitions[$field_name])) {
          $or_group->condition($field_name, '%' . $this->database->escapeLike($search_string) . '%', 'LIKE');
          // Leave inner loop once it has matched one bundle.
          break;
        }
      }
    }
    $query->condition($or_group);

    return $query;
  }

}
