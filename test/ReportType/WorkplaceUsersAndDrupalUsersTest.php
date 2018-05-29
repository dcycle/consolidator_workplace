<?php

namespace consolidator_workplace\Test;

use consolidator_workplace\ReportType\WorkplaceUsersAndDrupalUsers;
use PHPUnit\Framework\TestCase;

/**
 * Test WorkplaceUsersAndDrupalUsers.
 *
 * @group myproject
 */
class WorkplaceUsersAndDrupalUsersTest extends TestCase {

  /**
   * Smoke test.
   */
  public function testSmokeTest() {
    $this->assertTrue(is_object(new WorkplaceUsersAndDrupalUsers()));
  }

}
