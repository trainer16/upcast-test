<?php

namespace Upcast\Test;

class MeetingCsvBuilder {

    const MONTH_COLUMN_TITLE = 'Month';
    const MONTH_COLUMN_FORMAT = 'F';
    const NUMBER_OF_MONTHS_DEFAULT = 6;

    private $rows;
    private $numberOfMonths = self::NUMBER_OF_MONTHS_DEFAULT;
    private $sourceDateTime;

    public function __construct(\DateTime $dateTime, $numberOfMonths=null){
        if($numberOfMonths && is_numeric($numberOfMonths)){
            $this->numberOfMonths = (int) $numberOfMonths;
        }
        $dateTime = clone $dateTime;
        $this->sourceDateTime = $dateTime;

        for($i=0; $i < $this->numberOfMonths;$i++){
            $dateTimeInstance = clone $dateTime;
            if($i!=0){
                $interval = "P{$i}M";
                $dateTimeInstance->add(new \DateInterval($interval));
            }

            $midMonthMeeting = new MidMonthMeeting($dateTimeInstance);
            $testing = new Testing($dateTimeInstance);

            $row = array(
                self::MONTH_COLUMN_TITLE => $dateTimeInstance->format(self::MONTH_COLUMN_FORMAT),
                $midMonthMeeting->getDateTitle() => $midMonthMeeting->getActualDateNice(),
                $testing->getDateTitle() => $testing->getActualDateNice(),
            );

            $this->rows[] = $row;
        }
    }

    /**
     * Print $rows into a CSV file in home directory.
     */
    public function printCSV(){
        if(!empty($this->rows)){
            $filename = 'event_dates_'.$this->sourceDateTime->getTimestamp().'.csv';
            // create a file pointer
            $fp = fopen($filename, 'w');

            // print header by retrieving the keys of the first element in $this->rows array:
            $header = array_keys(array_shift(array_values($this->rows)));
            fputcsv($fp, $header);

            foreach ($this->rows as $row) {
                fputcsv($fp, $row);
            }

            fclose($fp);
        }
    }
}