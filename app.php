<?php

namespace Upcast\Test;

include 'Event.php';
include 'MidMonthMeeting.php';
include 'Testing.php';
include 'MeetingCsvBuilder.php';



$shortopts = 'n::'; // number of months
$shortopts .= 'd::'; // custom date string

// register options passed via the command line
$options = getopt($shortopts);

$numberOfMonths = null;
if(isset($options['n'])){
    $numberOfMonths = $options['n'];
}
$dateTime = new \DateTime();
if(isset($options['d'])){
    $customDateTime = strtotime($options['d']);
    if($customDateTime){
        $dateTime->setTimestamp($customDateTime);
    }
}

// Create the MeetingCSVBuilder and print data
$meetingCsvBuilder = new MeetingCsvBuilder($dateTime,$numberOfMonths);
$meetingCsvBuilder->printCSV();