<?php

namespace consolidator_workplace\ReportType;

use consolidator\ReportType\ReportType;

/**
 * A sample report to fetch users from a Facebook Workplace account.
 */
class WorkplaceUsersAndDrupalUsers extends ReportType {

  /**
   * {@inheritdoc}
   */
  public function displayReport(array $report) : string {
    $return = '<ul>';
    foreach ($report as $item) {
      $return .= '<li>' . $item['title'] . ':<strong> ' . $item['value'] . '</strong></li>';
    }
    return $return . '</ul>';
  }

  /**
   * {@inheritdoc}
   */
  public function name() : string {
    return 'Facebook Workplace Users vs. Drupal users';
  }

  /**
   * {@inheritdoc}
   */
  public function steps() : array {
    return [
      'getWorkplaceGroups' => [],
      'getWorkplaceUsers' => [],
      'getDrupalUsers' => [],
    ];
  }

  /**
   * Step 1, get Workplace groups.
   */
  public function getWorkplaceGroups() {
    $existing_results = $this->fromLastCall('existing', []);
    $total_calls = $this->fromLastCall('total_calls', 1);
    $next_page = $this->fromLastCall('next-page', '');

    $path = 'community/groups';
    $call = $next_page ? $next_page : ($this->setting('graph_url') . '/' . $path);
    $data = $this->getJson(
      $call,
      array(
        'headers' => array(
          'Authorization' => 'Bearer ' . $this->setting('api_key'),
        ),
        'method' => 'GET',
        'data' => json_encode(NULL),
      )
    );
    $all_results = array_merge($existing_results, $data['data']);
    if (!empty($data['paging']['next'])) {
      $this->rememberForNext('next-page', $data['paging']['next']);
      $this->rememberForNext('existing', $all_results);
      $this->rememberForNext('total_calls', ++$total_calls);
    }
    else {
      return [
        'result' => $all_results,
        'total-calls' => $total_calls,
      ];
    }
  }

  /**
   * Step 2, get Workplace users.
   */
  public function getWorkplaceUsers() {
    $existing_results = $this->fromLastCall('existing', []);
    $total_calls = $this->fromLastCall('total_calls', 1);

    // Remaining groups which have not even been started yet.
    $current_group = $this->fromLastCall('current-group', '');
    if (!$current_group) {
      $current_group = array_pop($remaining_groups);
    }
    // So we are dealing with $current_group.
    $next_page = $this->fromLastCall('next-page', '');
    $path = '/v1/Users';
    $call = $next_page ? $next_page : ($this->setting('scim_url') . '/' . $path);

    try {
      $data = $this->getJson(
        $call,
        array(
          'headers' => array(
            'Authorization' => 'Bearer ' . $this->setting('api_key'),
          ),
          'method' => 'GET',
          'data' => json_encode(NULL),
        )
      );
    }
    catch (\Exception $e) {
      watchdog('a', $e->getMessage());
    }

    $all_results = array_merge($existing_results, $data['data']);
    if (!empty($data['paging']['next'])) {
      $this->rememberForNext('next-page', $data['paging']['next']);
      $this->rememberForNext('existing', $all_results);
      $this->rememberForNext('total_calls', ++$total_calls);
      $this->rememberForNext('remaining-groups', $remaining_groups);
      $this->rememberForNext('current-group', $current_group);
    }
    elseif (count($remaining_groups)) {
      $this->rememberForNext('existing', $all_results);
      $this->rememberForNext('total_calls', ++$total_calls);
      $this->rememberForNext('remaining-groups', $remaining_groups);
      $this->rememberForNext('current-group', '');
      $this->rememberForNext('next-page', '');
    }
    else {
      return [
        'result' => $all_results,
        'total-calls' => $total_calls,
      ];
    }
  }

  /**
   * Step 3, get Drupal users.
   */
  public function getDrupalUsers() {
    $users = entity_load('user');
    unset($users[0]);
    unset($users[1]);
    return [
      'users' => $users,
    ];
  }

  /**
   * Final step, build a report.
   */
  public function buildReport() : array {
    $workplace_groups = $this->get('getWorkplaceGroups');
    $workplace_users = $this->get('getWorkplaceUsers');
    $drupal_users = $this->get('getDrupalUsers');

    return [
      [
        'title' => 'Number of users in both Drupal and Workplace',
        'value' => 0,
      ],
      [
        'title' => 'Number of users in Drupal',
        'value' => count($drupal_users['users']),
      ],
      [
        'title' => 'Number of groups in Workplace',
        'value' => count($workplace_groups['result']),
      ],
      [
        'title' => 'Total api calls to workplace groups',
        'value' => $workplace_groups['total-calls'],
      ],
      [
        'title' => 'Number of users in Workplace',
        'value' => count($workplace_users['result']),
      ],
      [
        'title' => 'Total api calls to workplace users',
        'value' => $workplace_users['total-calls'],
      ],
    ];
  }

  public function setting(string $name) {
    $var = 'consolidator_workplace_' . $name;
    $return = variable_get($var);
    if (!$return) {
      throw new \Exception('Please set ' . $var . ' as in the README file.');
    }
    return $return;
  }

}
