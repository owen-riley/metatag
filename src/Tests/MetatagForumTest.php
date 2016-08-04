<?php

namespace Drupal\metatag\Tests;

use Drupal\simpletest\WebTestBase;

/**
 * Ensures that meta tags are rendering correctly on forum pages.
 *
 * @group Metatag
 */
class MetatagForumTest extends WebTestBase {

  /**
   * {@inheritdoc}
   */
  public static $modules = [
    'token',
    'metatag',
    'node',
    'system',
    'forum',
  ];

  /**
   * Administrator user for tests.
   *
   * @var \Drupal\user\UserInterface
   */
  protected $adminUser;

  /**
   * Setup basic environment.
   */
  protected function setUp() {
    parent::setUp();

    $admin_permissions = [
      'administer nodes',
      'bypass node access',
      'administer meta tags',
      'administer site configuration',
      'access content',
    ];

    // Create and login user.
    $this->adminUser = $this->drupalCreateUser($admin_permissions);
    $this->drupalLogin($this->adminUser);

    // Create content type.
    $this->drupalCreateContentType(['type' => 'page', 'display_submitted' => FALSE]);
    $this->nodeId = $this->drupalCreateNode(
      [
        'title' => $this->randomMachineName(8),
        'promote' => 1,
      ])->id();

    $this->config('system.site')->set('page.front', '/node/' . $this->nodeId)->save();
  }

  /**
   * Verify that a forum post can be loaded when Metatag is enabled.
   */
  function testForumPost() {
    $this->drupalGet('node/add/forum');
    $this->assertResponse(200);
    $edit = [
      'title[0][value]' => 'Testing forums',
      'taxonomy_forums' => 1,
      'body[0][value]' => 'Just testing.',
    ];
    $this->drupalPostForm(NULL, $edit, t('Save and publish'));
    $this->assertResponse(200);
    $this->assertText(t('@type @title has been created.', array('@type' => t('Forum topic'), '@title' => 'Testing forums')));
  }

}
