<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class calendar {

    private $active_year, $active_month, $active_day;
    private $events = [];

    public function __construct($date = null) {
        $this->active_year = $date != null ? date('Y', strtotime($date)) : date('Y');
        $this->active_month = $date != null ? date('m', strtotime($date)) : date('m');
        $this->active_day = $date != null ? date('d', strtotime($date)) : date('d');
    }

    public function add_event($txt, $date, $days = 1, $color = '') {
        $color = $color ? ' ' . $color : $color;
        $this->events[] = [$txt, $date, $days, $color];
    }

    public function __toString() {
        $num_days = date('t', strtotime($this->active_day . '-' . $this->active_month . '-' . $this->active_year));
        $num_days_last_month = date('j', strtotime('last day of previous month', strtotime($this->active_day . '-' . $this->active_month . '-' . $this->active_year)));
        $days = [0 => 'Sun', 1 => 'Mon', 2 => 'Tue', 3 => 'Wed', 4 => 'Thu', 5 => 'Fri', 6 => 'Sat'];
        $first_day_of_week = array_search(date('D', strtotime($this->active_year . '-' . $this->active_month . '-1')), $days);
        $html = '<div class="calendar">';
//        $html .= '<div class="header">';
//        $html .= '<div class="month-year">';
//        $html .= date('F Y', strtotime($this->active_year . '-' . $this->active_month . '-' . $this->active_day));
//        $html .= '</div>';
//        $html .= '</div>';
        $html .= '<div class="row days">';
        foreach ($days as $day) {
            $html .= '
                <div class="col day_name">
                    ' . $day . '
                </div>
            ';
        }
        $html .= '</div>';
        $html .= '<div class="row days">';
        $dayCount=0;
        for ($i = $first_day_of_week; $i > 0; $i--) {
            $html .= '
                <div class="col day_num ignore">
                    ' . ($num_days_last_month-$i+1) . '
                </div>
            ';
            $dayCount++;
        }
        for ($i = 1; $i <= $num_days; $i++) {
            if($dayCount==7){
                $dayCount=0;
                $html .= '</div>';
                $html .= '<div class="row days">';
            }

            $selected = '';
            if ($i == $this->active_day) {
                $selected = ' selected';
            }


            $hasTrip=false;
            foreach ($this->events as $event) {
                for ($d = 0; $d <= ($event[2]-1); $d++) {
                    if (date('Y-m-d', strtotime($this->active_year . '-' . $this->active_month . '-' . $i . ' -' . $d . ' day')) == date('Y-m-d', strtotime($event[1]))) {
                        $html .= '<div class="col day_num has_trip' . $selected . '">';
                        $html .= '<span data-date="'.date('Y-m-d', strtotime($event[1])).'">' . sprintf("%02d", $i) . '</span>';
                        $hasTrip=true;
                        break;
                    }
                }
                if($hasTrip){break;}
            }
            if(!$hasTrip){
                $html .= '<div class="col day_num' . $selected . '">';
                $html .= '<span>' . sprintf("%02d", $i) . '</span>';
            }
            $html .= '</div>';
            $dayCount++;
        }
        for ($i = 1; $i <= (42-$num_days-max($first_day_of_week, 0)); $i++) {
            if($dayCount==7){
                break;
            }
            $html .= '
                <div class="col day_num ignore">
                    ' . $i . '
                </div>
            ';
            $dayCount++;
        }
        $html .= '</div>';
        $html .= '</div>';
        return $html;
    }

}
