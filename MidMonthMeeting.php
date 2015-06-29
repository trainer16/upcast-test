<?php

namespace Upcast\Test;

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