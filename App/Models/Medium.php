<?php
/**
 * Created by Zbigglord
 * Company: HELLUX
 * Date: 2017-08-24
 * Time: 08:42
 */

namespace App\Models;

use Core\Model;
use Core\Session;

class Medium extends Model{

    public static function getSlider($slider_id){

        $db = Model::getDB();
        $slider = array();
        $query = ("SELECT * FROM slider_images WHERE slider_id = '$slider_id'");
        $success = $db->query($query);
        if(!$success){
            Session::setFlash(ATXT_ERROR_RUNNING_QUERY.'[' . $db->error . ']','error');
            die(ATXT_ERROR_RUNNING_QUERY.'[' . $db->error . ']');
        }

        $found = $success->num_rows;
        if($found > 0){
            while($result = $success->fetch_assoc()){
                array_push($slider, $result);
            }
        }

        $db->close();
        return $slider;

    }//END function getSlider($slider_id)

}//END CLASS