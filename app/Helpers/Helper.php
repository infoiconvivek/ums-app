<?php

namespace App\Helpers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Category;
use App\Models\Booking;
use App\Models\PageSection;
use Illuminate\Support\Facades\Http;
use Auth;


class Helper
{

  public static function checkTest()
  {
    return 'hello';
  }
  
  public static function getBookingId($job_id)
  {
     return $booking = Booking::where('job_id',$job_id)->first()->id;
  }


  public static function getPageSection($id)
  {
    $section = PageSection::where(['id'=>$id,'status'=>1])->first();
    if($section)
    {
        return $section;
    } else
    {
        return '';
    }
   
  }


}