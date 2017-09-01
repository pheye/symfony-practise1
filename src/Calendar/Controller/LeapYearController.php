<?php
namespace Calendar\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Calendar\Model\LeapYear;


class LeapYearController {
    public function indexAction(Request $request, $year) {
        $leapYear = new LeapYear();
        if ($leapYear->isLeapYear($year)) {
            return new Response("Yes, this is a leap year");
        }
        return new Response("No, this is not a leap year");
    }
}
