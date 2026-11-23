<?php

use App\Models\User;
use Illuminate\Support\Facades\Storage;

/**
 * fullName   return full name
 *
 * @param  mixed $firstName
 * @param  mixed $lastName
 * @return void
 */
function fullName($firstName, $lastName)
{
    return ucwords($firstName . ' ' . $lastName);
}

/**
 * fullName   return social login provide
*/
function getSocialProviderName($provider)
{
    if(!is_null($provider->fbId)){
        return 'Facebook';
    }
    if(!is_null($provider->googleId)){
        return 'Google';
    }
    if(!is_null($provider->googleId) && !is_null($provider->fbId)){
        return 'Google & Facebook';
    }else{


        return 'N/A';
    }

}

/**
 * tinyIntToReadable   cobvert 1,0 into Yes No
 *
 * @param  bool $param
 * @return void
 */
function tinyIntToReadable($param)
{
    if (is_null($param)) {
        return 'No';
    }
    if ($param == 1) {
        return 'Yes';
    }
    if ($param == 0) {
        return 'No';
    }
}
/**
 * getStatusHtml   return html formatted buttons according to status
 *
 * @param  mixed $param
 * @return void
 */
function getStatusHtml($param)
{

    if ($param == 'Active' || $param == \App\Enums\SellerDocumentEnum::APPROVED) {
        return '<button class="btn btn-rounded-success btn-lg" style="' . style_rounded_success() . '">' . $param . '</button';
    }
    if ($param == 'Banned') {
        return '<button class="btn btn-rounded-danger btn-lg" style="' . style_rounded_danger() . '">Blocked</button';
    }
    if ($param == \App\Enums\SellerDocumentEnum::REJECTED) {
        return '<button class="btn btn-rounded-danger btn-lg" style="' . style_rounded_danger() . '">' . $param . '</button';
    }
    if ($param == \App\Enums\SellerDocumentEnum::NEEDAPPROVAL) {
        return '<button class="btn btn-rounded-danger btn-lg" style="' . style_rounded_orange() . '">Need Approval</button';
    }if(is_null($param)){
        return '<button class="btn btn-rounded-danger btn-lg" style="' . style_rounded_info() . '">N/A</button';
    }
}

/**
 * roundedAvatar  return small rounded image for datatable
 *
 * @param  mixed $image
 * @return void
 */
function roundedAvatar($image){
    $imageUrl=$image?Storage::disk('s3')->url($image):asset('img/default-avatar.svg');
    return '<img src="'.$imageUrl.'" alt="Product 1" class="mr-2 img-circle img-size-32">';
}

/**
 * style_rounded_danger  return rounded button with danger color
 *
 * @return void
 */
function style_rounded_danger()
{
    $style = '  background-color: #d9534f;border:none;border-radius:20px;color: white;text-align: center;text-decoration: none;display: inline-block;font-size: 16px;margin: 4px 2px;';
    return $style;
}

/**
 * style_rounded_info  return rounded button with info color
 *
 * @return void
 */
function style_rounded_info()
{
    $style = '  background-color: #17a2b8;border:none;border-radius:20px;color: white;text-align: center;text-decoration: none;display: inline-block;font-size: 16px;margin: 4px 2px;';
    return $style;
}
/**
 * style_rounded_success  return rounded button with success color
 *
 * @return void
 */
function style_rounded_success()
{
    $style = '  background-color: #5cb85c;border-radius:20px;border: none;color: white;text-align: center;text-decoration: none;display: inline-block;font-size: 16px;margin: 4px 2px;';
    return $style;
}
/**
 * style_rounded_success  return rounded button with success color
 *
 * @return void
 */
function style_rounded_orange()
{
    $style = '  background-color: #FF7F50;border-radius:20px;border: none;color: white;text-align: center;text-decoration: none;display: inline-block;font-size: 16px;margin: 4px 2px;';
    return $style;
}



/**
 * commonScripts  return common scripts
 *
 * @return void
 */
function commonScripts()
{
    return '
    <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap4.min.js"></script>
    <script data-require="datatables-responsive@*" data-semver="2.1.0" src="//cdn.datatables.net/responsive/2.1.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Dropify/0.2.2/js/dropify.min.js"></script>
    ';
}
/**
 * commonStyles  return common styles
 *
 * @return void
 */
function commonStyles()
{
    return '
    <link rel="stylesheet"  href="https://cdn.datatables.net/1.10.24/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Dropify/0.2.2/css/dropify.min.css"/>
    ';
}

function customDatatableCss()
{
    return '<style>
    .table-striped>tbody>tr:nth-child(odd)>td,
    .table-striped>tbody>tr:nth-child(odd)>th {
        background-color:#F3F4F6
    }
   .table-striped>tbody>tr:nth-child(even)>td,
  .table-striped>tbody>tr:nth-child(even)>th {
     background-color: white;
   }
   .bg-white{
    background:white
   }
   div.dataTables_length {
      float: left;
  }

  div.dataTables_filter {
      float: right;

  }

  div.dataTables_info {
      float: left;
      margin-left: 40px;

  }
  div.top {
      background:white

  }
  .dot {
    height: 8px;
    width: 8px;
    margin-bottom:1px;
    background-color: #19A2FF;
    border-radius: 50%;
    display: inline-block;
}

  </style>';
}

function select2CssCustom(){
    return  '<style>
    .select2-selection__choice__display{
        color:white;
        background:#3490dc;
        padding:30px;

       }
       .select2-selection__rendered {
        line-height: 31px !important;
    }
    .select2-container .select2-selection--single {
        height: 35px !important;
    }
    .select2-selection__arrow {
        height: 34px !important;
    } </style>';
}

function getCountOfTotalUsers(){
    return User::whereHas('roles',function ($q) {
        $q->whereIn('name', ['buyer','seller']);
    })->count();
}



