<?php
namespace Upcast\Test;

abstract class Event {
    const NICE_DATE_FORMAT = 'D, d M Y';
    const DATE_TITLE_DEFAULT = 'event date';

    /**
     * @var \DateTime
     */
    protected $targetDate;

    /**
     * @var \DateTime
     */
    protected $actualDate;

    /**
     * @var array Array of day names e.g. [Saturday,Sunday]
     *
     */
    protected $unavailableDays;

    /**
     * @var string String containing "Relative Formats" instructions
     *
     * @link http://php.net/manual/en/datetime.formats.relative.php
     */
    protected $alternativeDay;

    /**
     * @var string
     */
    protected $dateTitle = self::DATE_TITLE_DEFAULT;
    
    abstract public function __construct(\DateTime $dateTime);

    /**
     * @param \DateTime $targetDate
     */
    public function setTargetDate(\DateTime $targetDate){
        $this->targetDate = $targetDate;
    }

    /**
     * @return \DateTime
     */
    public function getTargetDate(){
        return $this->targetDate;
    }

    /**
     * @param \DateTime $actualDate
     */
    public function setActualDate(\DateTime $actualDate){
        $this->actualDate = $actualDate;
    }

    /**
     * @return \DateTime
     */
    public function getActualDate(){
        return $this->actualDate;
    }

    /**
     * @return null|string
     */
    public function getActualDateNice(){
        if(!empty($this->actualDate)){
            return $this->actualDate->format(self::NICE_DATE_FORMAT);
        }
        
        return null;
    }

    public function getDateTitle(){
        return $this->dateTitle;
    }


    /**
     * This function checks whether the target date happens on an unavailable day and uses instructions given in
     * self::$alternativeDay to change the date
     */
    public function checkDateAvailability(){
        if(!empty($this->targetDate) && $this->alternativeDay && is_array($this->unavailableDays)){
            $targetDay = $this->targetDate->format('l');
            if(in_array($targetDay,$this->unavailableDays)){
                $targetDate = clone $this->targetDate;
                $actualTimestamp = strtotime($this->alternativeDay, $targetDate->getTimestamp());
                $actualDate = new \DateTime();
                $actualDate->setTimestamp($actualTimestamp);
                $this->setActualDate($actualDate);
            }
        }
    }

}

class MidMonthMeeting extends Event {
    const TARGET_MID_MONTH_DAY = 14;
    const DATE_TITLE = 'Mid Month Meeting Date';

    /**
     * {@inheritdoc }
     */
    protected $unavailableDays = array(
        'Saturday',
        'Sunday',
    );

    /**
     * {@inheritdoc }
     */
    protected $alternativeDay = 'next monday';
    protected $dateTitle = self::DATE_TITLE;

    public function __construct(\DateTime $dateTime){
        $dateTime = clone $dateTime;
        $targetDate = $dateTime->setDate($dateTime->format('Y'), $dateTime->format('m'), self::TARGET_MID_MONTH_DAY);
        $this->setTargetDate($targetDate);
        // temporarily set the actual date
        $this->setActualDate($targetDate);

        // check availability:
        $this->checkDateAvailability();
    }

}


class Testing extends Event {
    const DATE_TITLE = 'End of Month Testing Date';

    /**
     * {@inheritdoc }
     */
    protected $unavailableDays = array(
        'Friday',
        'Saturday',
        'Sunday',
        'Tuesday',
    );

    /**
     * {@inheritdoc }
     */
    protected $alternativeDay = 'last thursday';
    protected $dateTitle = self::DATE_TITLE;

    public function __construct(\DateTime $dateTime){
        $dateTime = clone $dateTime;
        /**
         * N.B the 't' format returns the number of days in the given month. We use it here to find the last day fo the
         * month.
         */
        $targetDate = $dateTime->setDate($dateTime->format('Y'), $dateTime->format('m'), $dateTime->format('t'));
        $this->setTargetDate($targetDate);
        // temporarily set the actual date
        $this->setActualDate($targetDate);

        // check availability:
        $this->checkDateAvailability();
    }
}

class MeetingCsvBuilder {

    const MONTH_COLUMN_TITLE = 'Month';
    const MONTH_COLUMN_FORMAT = 'F';

    private $rows;
    private $numberOfMonths = 6;
    private $sourceDateTime;

    public function __construct(\DateTime $dateTime){
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



$today = new \DateTime();

$meetingCsvBuilder = new MeetingCsvBuilder($today);
$meetingCsvBuilder->printCSV();