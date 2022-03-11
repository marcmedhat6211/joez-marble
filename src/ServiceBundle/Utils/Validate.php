<?php

namespace App\ServiceBundle\Utils;

class Validate
{
    public static function not_null($value, $length = null): bool
    {

        if ($value == '0') {
            return false;
        }
        if ($length != null and strlen($value) > $length) {
            return false;
        }
        if (is_array($value)) {
            return sizeof($value) > 0;
        } else {
            return ($value != '') && (@strtolower($value) != 'null') && (@strlen(@trim($value)) > 0);
        }
    }

    public static function date($date, $format = 'DD/MM/YYYY'): bool
    {
        if ($format == 'YYYY-MM-DD') {
            list($year, $month, $day) = explode('-', $date);
        }
        if ($format == 'YYYY/MM/DD') {
            list($year, $month, $day) = explode('/', $date);
        }
        if ($format == 'YYYY.MM.DD') {
            list($year, $month, $day) = explode('.', $date);
        }

        if ($format == 'DD-MM-YYYY') {
            list($day, $month, $year) = explode('-', $date);
        }
        if ($format == 'DD/MM/YYYY') {
            list($day, $month, $year) = explode('/', $date);
        }
        if ($format == 'DD.MM.YYYY') {
            list($day, $month, $year) = explode('.', $date);
        }

        if ($format == 'MM-DD-YYYY') {
            list($month, $day, $year) = explode('-', $date);
        }
        if ($format == 'MM/DD/YYYY') {
            list($month, $day, $year) = explode('/', $date);
        }
        if ($format == 'MM.DD.YYYY') {
            list($month, $day, $year) = explode('.', $date);
        }

        if (is_numeric($year) && is_numeric($month) && is_numeric($day)) {
            return checkdate($month, $day, $year);
        }

        return false;
    }

    public static function email($email): bool
    {
        return (bool)filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    public static function isPhoneNumber($number): bool
    {
        return (bool)preg_match("/^[0-9\(\)\/\+ \-]+$/i", $number);
    }

    public static function isJson($string): bool
    {
        return is_string($string) && (is_object(json_decode($string)) || is_array(json_decode($string)));
    }
}