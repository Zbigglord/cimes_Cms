<?php
/**
 * Created by Zbigglord
 * Company: HELLUX
 * Date: 2017-12-11
 * Time: 11:24
 */

namespace App\Models\Admin;


class Pagination
{

    public static function splitPages($items, $offset, $limit){

        $output = array_slice($items, $offset, $limit);
        return $output;

    }//END splitPages($items, $page, $limit)

    public static function preparePages($items, $limit){

        if($limit > 0){
            return $pages = ceil(count($items)/$limit);
        }else{
            return 1;
        }

    }//END function preparePages($salons, $limit)

    public static function prepareNexPage($offset, $limit){

        return $next_page = $offset*$limit;

    }//END function preparePages($salons, $limit)

    public static function cleanUrl($current_url){

        $current_part = explode('?',$current_url);

        return $current_part[0];

    }//END function preparePages($salons, $limit)

    public static function limitUrl($current_url){

        $current_part = explode('&',$current_url);

        return $current_part[1];

    }//END function preparePages($salons, $limit)

    public static function gridUrl($current_url){

        $current_part = explode('&',$current_url);

        return $current_part[2];

    }//END function preparePages($salons, $limit)

    public static function prepareAddressForSelect($clean_url, $page){

        if($page > 1){
            return $clean_url.'?page='.$page.'&limit=';
        }else{
            return $clean_url.'?page=0&limit=';
        }

    }

}//END CLASS