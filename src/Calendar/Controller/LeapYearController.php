<?php
namespace Calendar\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Calendar\Model\LeapYear;
use Symfony\Component\Debug\Exception\FlattenException;

class LeapYearController {
    public function indexAction(Request $request, $year) {
        $leapYear = new LeapYear();
        if ($leapYear->isLeapYear($year)) {
            return "Yes, this is a leap year" . rand();
        } else {
            return "No, this is not a leap year" . rand();
        }
    }
}
