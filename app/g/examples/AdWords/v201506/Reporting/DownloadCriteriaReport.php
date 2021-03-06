<?php
/**
 * This example downloads a criteria report to a file.
 *
 * Copyright 2014, Google Inc. All Rights Reserved.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * @package    GoogleApiAdsAdWords
 * @subpackage v201506
 * @category   WebServices
 * @copyright  2015, Google Inc. All Rights Reserved.
 * @license    http://www.apache.org/licenses/LICENSE-2.0 Apache License,
 *             Version 2.0
 */

// Include the initialization file
require_once dirname(dirname(__FILE__)) . '/init.php';
require_once ADWORDS_UTIL_VERSION_PATH . '/ReportUtils.php';

/**
 * Runs the example.
 * @param AdWordsUser $user the user to run the example with
 * @param string $filePath the path of the file to download the report to
 */
function DownloadCriteriaReportExample(AdWordsUser $user, $filePath) {
  // Load the service, so that the required classes are available.
  $user->LoadService('ReportDefinitionService', ADWORDS_VERSION);
  // Optional: Set clientCustomerId to get reports of your child accounts
  // $user->SetClientCustomerId('INSERT_CLIENT_CUSTOMER_ID_HERE');

  // Create selector.
  $selector = new Selector();
  $selector->fields = array('CampaignId', 'AdGroupId', 'Id', 'Criteria',
      'CriteriaType', 'Impressions', 'Clicks', 'Cost');

  // Optional: use predicate to filter out paused criteria.
  $selector->predicates[] = new Predicate('Status', 'NOT_IN', array('PAUSED'));

  // Create report definition.
  $reportDefinition = new ReportDefinition();
  $reportDefinition->selector = $selector;
  $reportDefinition->reportName = 'Criteria performance report #' . uniqid();
  $reportDefinition->dateRangeType = 'LAST_7_DAYS';
  $reportDefinition->reportType = 'CRITERIA_PERFORMANCE_REPORT';
  $reportDefinition->downloadFormat = 'CSV';

  // Exclude criteria that haven't recieved any impressions over the date range.
  $reportDefinition->includeZeroImpressions = false;

  // Set additional options.
  $options = array('version' => ADWORDS_VERSION);

  // Optional: Set skipReportHeader, skipColumnHeader, skipReportSummary to
  //     suppress headers or summary rows.
  // $options['skipReportHeader'] = true;
  // $options['skipColumnHeader'] = true;
  // $options['skipReportSummary'] = true;
  // Optional: Set includeZeroImpressions to include zero impression rows in
  //     the report output.
  // $options['includeZeroImpressions'] = true;

  // Download report.
  ReportUtils::DownloadReport($reportDefinition, $filePath, $user, $options);
  printf("Report with name '%s' was downloaded to '%s'.\n",
      $reportDefinition->reportName, $filePath);
}

// Don't run the example if the file is being included.
if (__FILE__ != realpath($_SERVER['PHP_SELF'])) {
  return;
}

try {
  // Get AdWordsUser from credentials in "../auth.ini"
  // relative to the AdWordsUser.php file's directory.
  $user = new AdWordsUser();

  // Log every SOAP XML request and response.
  $user->LogAll();

  // Download the report to a file in the same directory as the example.
  $filePath = dirname(__FILE__) . '/report.csv';

  // Run the example.
  DownloadCriteriaReportExample($user, $filePath);
} catch (Exception $e) {
  printf("An error has occurred: %s\n", $e->getMessage());
}
