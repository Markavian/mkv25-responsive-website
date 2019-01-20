<?php
require_once(__DIR__ . '/../lib/helpers/Sql.class.php');
require_once(__DIR__ . '/../lib/helpers/Environment.class.php');
require_once(__DIR__ . '/../routes/environment/secret.config.php');

Sql::$errorReportsOn = true;
Sql::$errorReportsVisible = false;

$testResults = array();
function register($fn, $name) {
  global $testResults;
  $testResults[$fn] = array($fn, $name, false);
}

function record($fn, $result) {
  global $testResults;
  $testResults[$fn][2] = $result ? true : false;
}

function printTestResults($testResults) {
  print "<h1>Test Results</h1>";
  $n = 0;
  foreach ($testResults as $key => $record) {
    $result = $record[2] ? 'Pass' : 'Fail';
    $n++;
    print "<p>[$n] [$key] : $result</p>";
  }
}

function testSqlConnection() {
  register(__FUNCTION__, 'Test SQL Connection');
  $actual = Sql::getInstance();
  assert($actual, 'Sql::getInstance() returns an SQL connection instance.');
  record(__FUNCTION__, true);
}

testSqlConnection();
printTestResults($testResults);
