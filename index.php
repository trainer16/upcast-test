<?php
namespace Upcast\Test;

abstract class Event {
    const NICE_DATE_FORMAT = 'D, d M Y';

    /**
     * @var \DateTime
     */
    protected $targetDate;

    /**
     * @var \DateTime
     */
    protected $actualDate;

    /**
     * @var Array
     */
    protected $unavailableDays;

    /**
     * @var String
     */
    protected $alternativeDay;
    
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

    /**
     * This function checks whether the target date happens on an unavailable day and uses instruction given in
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

    protected $unavailableDays = array(
        'Saturday',
        'Sunday',
    );

    protected $alternativeDay = 'next monday';

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
    protected $unavailableDays = array(
        'Friday',
        'Saturday',
        'Sunday',
        'Tuesday',
    );

    protected $alternativeDay = 'last thursday';

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

$now = new \DateTime();

$midMonthMeeting = new MidMonthMeeting($now);
$testing = new Testing($now);
pr($testing, $midMonthMeeting);



//Print output on screen
function pr(){
    $res = func_get_args();
    if(count($res) == 1){
        $res = array_shift($res);
    }
    die( '<div style="border-bottom: 2px solid #333333">&gt;&gt; START</div>'
        . '<div><pre>'
        . print_r($res,1)
        . '</pre></div>'
        . '<div style="border-top: 2px solid #333333">&lt;&lt; END</div>'
    );
}