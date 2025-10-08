<?php

namespace Drupal\Tests\profile_ext\Unit;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\profile\Entity\Profile;
use Drupal\user\UserInterface;
use Drupal\profile_ext\Services\ProfileExtUtils;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \Drupal\profile_ext\Services\ProfileExtUtils
 * @group laurier_custom
 */
class ProfileExtUtilsTest extends TestCase {

  /**
   * Entity type manager service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Profile extended utilities service.
   *
   * @var \Drupal\profile_ext\Services\ProfileExtUtils
   */
  protected $profileExtUtils;

  /**
   * Setup for tests.
   */
  protected function setUp(): void {
    $this->entityTypeManager = $this->createMock(EntityTypeManagerInterface::class);
    $this->profileExtUtils = new ProfileExtUtils($this->entityTypeManager);
  }

  /**
   * Test preferred option, returning names.
   */
  public function testGetNameWithFirstAndLastName(): void {
    $profile = $this->createMock(Profile::class);
    $profile->method('hasField')->willReturn(TRUE);

    $profile->method('get')
      ->willReturnCallback(function ($field_name) {
        $field = $this->createMock(FieldItemListInterface::class);
        $field->method('getString')->willReturn($field_name === 'field_first_name' ? 'John' : 'Doe');
        return $field;
      });

    $this->assertEquals('John Doe', $this->profileExtUtils->getName($profile));
  }

  /**
   * Test fallback to owner display name.
   */
  public function testGetNameFallbackToOwnerDisplayName(): void {
    $profile = $this->createMock(Profile::class);
    $profile->method('hasField')->willReturn(TRUE);

    $field = $this->createMock(FieldItemListInterface::class);
    $field->method('getString')->willReturn('');
    $profile->method('get')->willReturn($field);

    $owner = $this->createMock(UserInterface::class);
    $owner->method('isAnonymous')->willReturn(FALSE);
    $owner->method('getDisplayName')->willReturn('Jane Smith');
    $profile->method('getOwner')->willReturn($owner);

    $this->assertEquals('Jane Smith', $this->profileExtUtils->getName($profile));
  }

  /**
   * Test fallback to bundle name.
   */
  public function testGetNameFallbackToBundleName(): void {
    $profile = $this->createMock(Profile::class);
    $profile->method('hasField')->willReturn(FALSE);
    $profile->method('bundle')->willReturn('test_bundle');
    $profile->method('id')->willReturn(42);

    $storage = $this->createMock(EntityStorageInterface::class);
    $storage->method('load')->willReturn(NULL);
    $this->entityTypeManager->method('getStorage')->willReturn($storage);

    $this->assertEquals('test_bundle #42', $this->profileExtUtils->getName($profile));
  }

  /**
   * Test bundle name with label.
   */
  public function testGetBundleNameWithLabel(): void {
    $profile = $this->createMock(EntityInterface::class);
    $profile->method('bundle')->willReturn('test_bundle');
    $profile->method('id')->willReturn(99);

    $type = $this->createMock(EntityInterface::class);
    $type->method('label')->willReturn('Test Bundle Label');

    $storage = $this->createMock(EntityStorageInterface::class);
    $storage->method('load')->willReturn($type);
    $this->entityTypeManager->method('getStorage')->willReturn($storage);

    $this->assertEquals('Test Bundle Label #99', $this->profileExtUtils->getBundleName($profile));
  }

  /**
   * Test bundle name fallback.
   */
  public function testGetBundleNameFallback(): void {
    $profile = $this->createMock(EntityInterface::class);
    $profile->method('bundle')->willReturn('fallback_bundle');
    $profile->method('id')->willReturn(NULL);

    $storage = $this->createMock(EntityStorageInterface::class);
    $storage->method('load')->willReturn(NULL);
    $this->entityTypeManager->method('getStorage')->willReturn($storage);

    $this->assertEquals('fallback_bundle #new', $this->profileExtUtils->getBundleName($profile));
  }

}
