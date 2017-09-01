<?php
namespace Calendar\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

function is_leap_year($year = null) {
    if (null === $year) {
        $year = date('Y');
    }

    return 0 === $year % 400 || (0 === $year % 4 && 0 !== $year % 100);
}

class LeapYearController {
    public function indexAction(Request $request, $year) {
        if (is_leap_year($year)) {
            return new Response("Yes, this is a leap year");
        }
        return new Response("No, this is not a leap year");
    }
}
