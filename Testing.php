<?php

namespace Upcast\Test;

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