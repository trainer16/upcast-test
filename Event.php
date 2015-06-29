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