<?php
require_once(__DIR__ . '/../lib/helpers/Sql.class.php');
require_once(__DIR__ . '/../lib/helpers/Environment.class.php');
require_once(__DIR__ . '/../routes/environment/secret.config.php');

Sql::$errorReportsOn = true;
Sql::$errorReportsVisible = true;

$testResults = array();
function register($fn, $name) {
  global $testResults;
  $testResults[$fn] = array($fn, $name, false);
}

function record($fn, $result, $message=false) {
  global $testResults;
  $testResults[$fn][2] = $result ? true : false;
  $testResults[$fn][3] = $message;
}

function textContains($haystack, $needle) {
  return stripos($haystack, $needle) !== -1;
}

function printTestResults($testResults) {
  print "<h1>Test Results</h1>";
  $n = 0;
  foreach ($testResults as $key => $record) {
    $result = $record[2] ? 'Pass' : 'Fail';
    $message = $record[3] ? ' : ' . $record[3] : '';
    $n++;
    print "<p>[$n] [$key] : $result $message</p>";
  }
}

function testSqlConnectionSuccess() {
  register(__FUNCTION__, 'Test SQL Connection Success');
  ob_start();
  $actual = Sql::getInstance();
  ob_clean();
  if($actual) {
    record(__FUNCTION__, true, 'Sql::getInstance() returns a valid SQL connection instance.');
  } else {
    record(__FUNCTION__, false, 'Sql::getInstance() did not return a valid connection instance.');
  }
}

function testSqlConnectionError() {
  register(__FUNCTION__, 'Test SQL Connection Error');
  ob_start();
  $actual = new Sql('badhost', 'baduser', 'badpass', 'baddatabase');
  $result = ob_get_clean();
  if (textContains($result, 'Unable to establish database connection.')) {
    record(__FUNCTION__, true, 'new Sql(...) returned the expected SQL connection error.');
  } else {
    record(__FUNCTION__, false, 'new Sql(...) did not return an SQL connection error. ' . $result);
  }
}

testSqlConnectionSuccess();
testSqlConnectionError();
printTestResults($testResults);
