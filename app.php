<?php

namespace Upcast\Test;

include 'Event.php';
include 'MidMonthMeeting.php';
include 'Testing.php';
include 'MeetingCsvBuilder.php';

$today = new \DateTime();

var_dump($argv);

$meetingCsvBuilder = new MeetingCsvBuilder($today);
$meetingCsvBuilder->printCSV();