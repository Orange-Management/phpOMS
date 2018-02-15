<?php
/**
 * Orange Management
 *
 * PHP Version 7.1
 *
 * @package    TBD
 * @copyright  Dennis Eichhorn
 * @license    OMS License 1.0
 * @version    1.0.0
 * @link       http://website.orange-management.de
 */
declare(strict_types=1);

namespace phpOMS\Stdlib\Base;

use phpOMS\Math\Functions\Functions;

/**
 * SmartDateTime.
 *
 * Providing smarter datetimes
 *
 * @package    Framework
 * @license    OMS License 1.0
 * @link       http://website.orange-management.de
 * @since      1.0.0
 */
class SmartDateTime extends \DateTime
{
    /**
     * Default format
     *
     * @var string
     * @since 1.0.0
     */
    /* public */ const FORMAT = 'Y-m-d hh:mm:ss';

    /**
     * Default timezone
     *
     * @var string
     * @since 1.0.0
     */
    /* public */ const TIMEZONE = 'UTC';

    /**
     * Create object from DateTime
     *
     * @param \DateTime $date DateTime to extend
     *
     * @return SmartDateTime
     *
     * @since  1.0.0
     */
    public static function createFromDateTime(\DateTime $date) : SmartDateTime
    {
        return new self($date->format('Y-m-d H:i:s'), $date->getTimezone());
    }

    /**
     * Modify datetime in a smart way.
     *
     * @param int $y        Year
     * @param int $m        Month
     * @param int $d        Day
     * @param int $calendar Calendar
     *
     * @return SmartDateTime
     *
     * @since  1.0.0
     */
    public function createModify(int $y, int $m = 0, int $d = 0, int $calendar = CAL_GREGORIAN) : SmartDateTime
    {
        $dt = clone $this;
        $dt->smartModify($y, $m, $d, $calendar);

        return $dt;
    }

    /**
     * Modify datetime in a smart way.
     *
     * @param int $y        Year
     * @param int $m        Month
     * @param int $d        Day
     * @param int $calendar Calendar
     *
     * @return SmartDateTime
     *
     * @since  1.0.0
     */
    public function smartModify(int $y, int $m = 0, int $d = 0, int $calendar = CAL_GREGORIAN) : SmartDateTime
    {
        $yearChange  = (int) floor(((int) $this->format('m') - 1 + $m) / 12);
        $yearChange  = ((int) $this->format('m') - 1 + $m) < 0 && ((int) $this->format('m') - 1 + $m) % 12 === 0 ? $yearChange - 1 : $yearChange;
        $yearNew     = (int) $this->format('Y') + $y + $yearChange;
        $monthNew    = ((int) $this->format('m') + $m) % 12;
        $monthNew    = $monthNew === 0 ? 12 : $monthNew < 0 ? 12 + $monthNew : $monthNew;
        $dayMonthOld = cal_days_in_month($calendar, (int) $this->format('m'), (int) $this->format('Y'));
        $dayMonthNew = cal_days_in_month($calendar, $monthNew, $yearNew);
        $dayOld      = (int) $this->format('d');

        if ($dayOld > $dayMonthNew) {
            $dayNew = $dayMonthNew;
        } elseif ($dayOld < $dayMonthNew && $dayOld === $dayMonthOld) {
            $dayNew = $dayMonthNew;
        } else {
            $dayNew = $dayOld;
        }

        $this->setDate($yearNew, $monthNew, $dayNew);

        if ($d !== 0) {
            $this->modify($d . ' day');
        }

        return $this;
    }

    /**
     * Get end of month object
     *
     * @return SmartDateTime
     *
     * @since  1.0.0
     */
    public function getEndOfMonth() : SmartDateTime
    {
        return new SmartDateTime($this->format('Y') . '-' . $this->format('m') . '-' . $this->getDaysOfMonth());
    }

    /**
     * Get start of month object
     *
     * @return SmartDateTime
     *
     * @since  1.0.0
     */
    public function getStartOfMonth() : SmartDateTime
    {
        return new SmartDateTime($this->format('Y') . '-' . $this->format('m') . '-01');
    }

    /**
     * Get days of current month
     *
     * @return int
     *
     * @since  1.0.0
     */
    public function getDaysOfMonth() : int
    {
        // todo: maybe ->format('t') is better
        return cal_days_in_month(CAL_GREGORIAN, (int) $this->format('m'), (int) $this->format('Y'));
    }

    /**
     * Get first day of current month
     *
     * @return int
     *
     * @since  1.0.0
     */
    public function getFirstDayOfMonth() : int
    {
        return getdate(mktime(0, 0, 0, (int) $this->format('m'), 1, (int) $this->format('Y')))['wday'];
    }

    /**
     * Is leap year in gregorian calendar
     *
     * @return bool
     *
     * @since  1.0.0
     */
    public function isLeapYear() : bool
    {
        return self::leapYear((int) $this->format('Y'));
    }

    /**
     * Test year if leap year in gregorian calendar
     *
     * @param int $year Year to check
     *
     * @return bool
     *
     * @since  1.0.0
     */
    public static function leapYear(int $year) : bool
    {
        $isLeap = false;

        if ($year % 4 == 0) {
            $isLeap = true;
        }

        if ($year % 100 == 0) {
            $isLeap = false;
        }

        if ($year % 400 == 0) {
            $isLeap = true;
        }

        return $isLeap;
    }

    /**
     * Get day of week
     *
     * @param int $y Year
     * @param int $m Month
     * @param int $d Day
     *
     * @return int
     *
     * @since  1.0.0
     */
    public static function getDayOfWeek(int $y, int $m, int $d) : int
    {
        return (int) date('w', strtotime($d . '-' . $m . '-' . $y));
    }

    /**
     * Get day of week
     *
     * @return int
     *
     * @since  1.0.0
     */
    public function getFirstDayOfWeek() : int
    {
        return self::getDayOfWeek((int) $this->format('Y'), (int) $this->format('m'), (int) $this->format('d'));
    }

    /**
     * Create calendar array
     *
     * @param int $weekStartsWith Day of the week start (0 = Sunday)
     *
     * @return array
     *
     * @since  1.0.0
     */
    public function getMonthCalendar(int $weekStartsWith = 0) : array
    {
        $days = [];

        // get day of first day in month
        $firstDay = $this->getFirstDayOfMonth();

        // calculate difference to $weekStartsWith
        $diffToWeekStart = Functions::mod($firstDay - $weekStartsWith, 7);
        $diffToWeekStart = $diffToWeekStart === 0 ? 7 : $diffToWeekStart;

        // get days of previous month
        $previousMonth     = $this->createModify(0, -1);
        $daysPreviousMonth = $previousMonth->getDaysOfMonth();

        // add difference to $weekStartsWith counting backwards from days of previous month (reorder so that lowest value first)
        for ($i = $daysPreviousMonth - $diffToWeekStart; $i < $daysPreviousMonth; ++$i) {
            $days[] = new \DateTime($previousMonth->format('Y') . '-' . $previousMonth->format('m') . '-' . ($i + 1));
        }

        // add normal count of current days
        $daysMonth = $this->getDaysOfMonth();
        for ($i = 1; $i <= $daysMonth; ++$i) {
            $days[] = new \DateTime($this->format('Y') . '-' . $this->format('m') . '-' . ($i));
        }

        // add remaining days to next month (7*6 - difference+count of current month)
        $remainingDays = 42 - $diffToWeekStart - $daysMonth;
        $nextMonth     = $this->createModify(0, 1);

        for ($i = 1; $i <= $remainingDays; ++$i) {
            $days[] = new \DateTime($nextMonth->format('Y') . '-' . $nextMonth->format('m') . '-' . ($i));
        }

        return $days;
    }
}
