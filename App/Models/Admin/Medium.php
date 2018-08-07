<?php
/**
 * Created by Zbigglord
 * Company: HELLUX
 * Date: 2017-11-29
 * Time: 10:42
 */

namespace App\Models\Admin;


use Core\Filesystem;
use Core\Model;
use Core\Session;

class Medium extends Model
{

    public static function addSlider($data = array()){//MMED 0001

        $db = Model::getDB();
        $name = $data['name'];
        $active = $data['active'];
        $query = ("INSERT INTO slider (slider_name, slider_active) VALUES ('$name','$active')");
        $success = $db->query($query);
        if(!$success){

            Session::setFlash(ATXT_ERROR_RUNNING_QUERY.'[' . $db->error . ']','error');
            die(ATXT_ERROR_RUNNING_QUERY.'[' . $db->error . ']');

        }else{

            Session::setFlash(ATXT_DATA_SAVED,'ok');

        }

        $db->close();

    }//END function addSlider($data = array())

    public static function sliderGetListAll(){////MMED 0002

        $db = Model::getDB();
        $sliders = array();
        $images = array();
        $both = array();
        $query = ("SELECT * FROM slider");
        $success = $db->query($query);
        if(!$success){

            Session::setFlash(ATXT_ERROR_RUNNING_QUERY.'[' . $db->error . ']','error');
            die(ATXT_ERROR_RUNNING_QUERY.'[' . $db->error . ']');

        }

        $found = $success->num_rows;
        if($found > 0){

            while($result = $success->fetch_assoc()){
                $sliders[] = $result;
            }

            foreach($sliders as $slider){

                $slider_id = $slider['slider_id'];
                $img_query = ("SELECT * FROM slider_images WHERE slider_id = '$slider_id'");
                $success = $db->query($img_query);
                if(!$success){

                    Session::setFlash(ATXT_ERROR_RUNNING_QUERY.'[' . $db->error . ']','error');
                    die(ATXT_ERROR_RUNNING_QUERY.'[' . $db->error . ']');

                }

                $found = $success->num_rows;
                if($found > 0){

                    while($result = $success->fetch_assoc()){
                        $images[] = $result;
                    }

                }

                $slider['images'] = $images;
                $both[] = $slider;
                $images = array();


            }

            $db->close();
            return $both;

        }else{

            return FALSE;

        }

    }//END sliderGetListAll()

    public static function sliderAddImages($images = array(),$slider_id){////MMED 0003

        $db = Model::getDB();
        $errors = array();
        foreach($images as $image){

            $image_link = $image['image_link'];
            $image_alt = $image['image_alt'];
            $image_path = $image['image_path'];

            $add_query = ("INSERT INTO slider_images (slider_id,image_link,image_alt,image_path) VALUES ('$slider_id','$image_link','$image_alt','$image_path')");
            $success = $db->query($add_query);
            if(!$success){

                $errors[] = $db->error;

            }

        }

        $db->close();
        return $errors;

    }//END function sliderAddImages($images = array()

    public static function getSliderHeader($slider_id){////MMED 0004

        $db = Model::getDB();
        $slider = array();
        $query = ("SELECT * FROM slider WHERE slider_id = '$slider_id'");
        $success = $db->query($query);
        if(!$success){

            Session::setFlash(ATXT_ERROR_RUNNING_QUERY.'[' . $db->error . ']','error');
            die(ATXT_ERROR_RUNNING_QUERY.'[' . $db->error . ']');

        }

        $found = $success->num_rows;
        if($found > 0){

            while($result = $success->fetch_assoc()){
                $slider['slider_id'] = $result['slider_id'];
                $slider['slider_name'] = $result['slider_name'];
                $slider['slider_active'] = $result['slider_active'];
            }

        }

        $db->close();
        return $slider;

    }//END function getSliderHeader($slider_id)

    public static function editSlider($data = array()){

        $db = Model::getDB();
        $s_id = $data['slider_id'];
        $s_name = $data['slider_name'];
        $s_active = $data['slider_active'];

        $query = ("UPDATE slider SET slider_name = '$s_name', slider_active = '$s_active' WHERE slider_id = '$s_id'");
        $success = $db->query($query);
        if(!$success){

            Session::setFlash(ATXT_ERROR_RUNNING_QUERY.'[' . $db->error . ']','error');
            die(ATXT_ERROR_RUNNING_QUERY.'[' . $db->error . ']');

        }

        $db->close();
        return TRUE;

    }//END function editSlider($data = array())

    public static function getSliderImageSingle($data = array()){

        $db = Model::getDB();
        $image_data = array();
        $slider_id = $data['slider_id'];
        $image_id = $data['image_id'];
        $query = ("SELECT * FROM slider_images WHERE slider_id = '$slider_id' AND image_id = '$image_id'");
        $success = $db->query($query);
        if(!$success){

            Session::setFlash(ATXT_ERROR_RUNNING_QUERY.'[' . $db->error . ']','error');
            die(ATXT_ERROR_RUNNING_QUERY.'[' . $db->error . ']');

        }

        $found = $success->num_rows;
        if($found > 0){

            while($result = $success->fetch_assoc()){
                $image_data['image_id'] = $result['image_id'];
                $image_data['slider_id'] = $result['slider_id'];
                $image_data['image_link'] = $result['image_link'];
                $image_data['image_alt'] = $result['image_alt'];
                $image_data['image_path'] = $result['image_path'];
            }

            $db->close();
            return $image_data;

        }else{

          Session::setFlash(MED_IMAGE_NOT_FOUND,'error');
            return FALSE;

        }

    }//END function getSliderImageSingle($data = array())

    public static function editSliderImage($data = array()){

        $db = Model::getDB();
        $s_id = $data['slider_id'];
        $i_id = $data['image_id'];
        $i_path = $data['image_path'];
        $i_alt = $data['image_alt'];
        $i_link = $data['image_link'];

        $query = ("UPDATE slider_images SET image_link = '$i_link', image_alt = '$i_alt', image_path = '$i_path' WHERE slider_id = '$s_id' AND image_id = '$i_id'");
        $success = $db->query($query);
        if(!$success){

            Session::setFlash(ATXT_ERROR_RUNNING_QUERY.'[' . $db->error . ']','error');
            die(ATXT_ERROR_RUNNING_QUERY.'[' . $db->error . ']');

        }

        $db->close();
        return TRUE;

    }//END function editSlider($data = array())


    public static function deleteSliderImageSingle($slider_id, $image_id){

        $db = Model::getDB();
        $query = ("DELETE FROM slider_images WHERE slider_id = '$slider_id' AND image_id = '$image_id'");
        $success = $db->query($query);
        if(!$success){

            Session::setFlash(ATXT_ERROR_RUNNING_QUERY.'[' . $db->error . ']','error');
            die(ATXT_ERROR_RUNNING_QUERY.'[' . $db->error . ']');

        }

            $db->close();
            return TRUE;

    }//END function getSliderImageSingle($data = array())

    public static function checkSliderImages($slider_id){

        $db = Model::getDB();
        $query = ("SELECT * FROM slider_images WHERE slider_id = '$slider_id'");
        $success = $db->query($query);
        if(!$success){

            Session::setFlash(ATXT_ERROR_RUNNING_QUERY.'[' . $db->error . ']','error');
            die(ATXT_ERROR_RUNNING_QUERY.'[' . $db->error . ']');

        }
        $found = $success->num_rows;
        if($found > 0){

            return TRUE;

        }else{

            return FALSE;

        }

    }//END function checkSliderImages($slider_id)

    public static function deleteSlider($slider_id){

        $db = Model::getDB();
        $query = ("DELETE FROM slider WHERE slider_id = '$slider_id'");
        $success = $db->query($query);
        if(!$success){

            Session::setFlash(ATXT_ERROR_RUNNING_QUERY.'[' . $db->error . ']','error');
            die(ATXT_ERROR_RUNNING_QUERY.'[' . $db->error . ']');

        }

        return TRUE;

    }//END function deleteSlider($slider_id)

    public static function createGallery($data){

        $db = Model::getDB();

        $exists = self::checkGallery($data);
        if($exists != TRUE){
        $gallery_category_id = $data['gallery_category_id'];
        $gallery_name = $data['gallery_name'];
        $gallery_metadata = $data['gallery_metadata'];
        $gallery_description = $data['gallery_description'];
        $author = Session::get('user_nick');

        $query = ("INSERT INTO galleries (
                   gallery_category_id,
                   gallery_name,
                   gallery_metadata,
                   gallery_description,
                   gallery_created,
                   gallery_author)
                   VALUES (
                   '$gallery_category_id',
                   '$gallery_name',
                   '$gallery_metadata',
                   '$gallery_description',
                   NOW(),
                   '$author'
                   )");

        $success = $db->query($query);
        if(!$success){

            Session::setFlash(ATXT_ERROR_RUNNING_QUERY.'[' . $db->error . ']','error');
            die(ATXT_ERROR_RUNNING_QUERY.'[' . $db->error . ']');

        }

        $last_id = $db->insert_id;
        $db->close();
        return $last_id;
        }else{
            Session::setFlash(AMENU_IMAGE_GALLERY_EXISTS,'error');
            $db->close();
            return FALSE;
        }
    }//END function createGallery($data)

    public static function checkGallery($data = array()){

        $db = Model::getDB();
        $name = $data['gallery_name'];
        $category_id = $data['gallery_category_id'];
        $check_query = ("SELECT * FROM galleries WHERE gallery_category_id='$category_id' AND gallery_name='$name'");
        $success = $db->query($check_query);
        if(!$success){

            Session::setFlash(ATXT_ERROR_RUNNING_QUERY.'[' . $db->error . ']','error');
            die(ATXT_ERROR_RUNNING_QUERY.'[' . $db->error . ']');

        }

        $found = $success->num_rows;
        if($found > 0){
            return TRUE;
        }else{
            return FALSE;
        }

    }//END function endGallery($data)

    public static function galleryAddImages($images = array(),$gallery_id){

        $db = Model::getDB();
        $errors = array();
        foreach($images as $image){

            $image_gallery_category_id = $image['gallery_category_id'];
            $image_gallery_name = $image['gallery_name'];
            $image_path = $image['image_path'];

            $add_query = ("INSERT INTO gallery_images (image_gallery_id, image_gallery_category_id, image_path, image_gallery_name) VALUES ('$gallery_id','$image_gallery_category_id','$image_path','$image_gallery_name')");
            $success = $db->query($add_query);
            if(!$success){

                $errors[] = $db->error;

            }

        }

        $db->close();
        return $errors;

    }//END function galleryAddImages($images = array()

    public static function getGalleriesList(){

        $db = Model::getDB();
        $temp = array();
        $galleries = array();
        $images = array();

        $select_galleries_query = ("SELECT * FROM galleries ORDER BY gallery_created DESC");
        $success = $db->query($select_galleries_query);
        if(!$success){

            Session::setFlash(ATXT_ERROR_RUNNING_QUERY.'[' . $db->error . ']','error');
            die(ATXT_ERROR_RUNNING_QUERY.'[' . $db->error . ']');

        }

        $found = $success->num_rows;
        if($found > 0){
            while($result = $success->fetch_assoc()){
                array_push($temp, $result);
            }

            foreach($temp as $gallery){

                $gallery_id = $gallery['gallery_id'];
                $get_images_query = ("SELECT * FROM gallery_images WHERE image_gallery_id = '$gallery_id'");
                $success = $db->query($get_images_query);
                if(!$success){

                    Session::setFlash(ATXT_ERROR_RUNNING_QUERY.'[' . $db->error . ']','error');
                    die(ATXT_ERROR_RUNNING_QUERY.'[' . $db->error . ']');

                }

                $found = $success->num_rows;
                if($found > 0){
                    while($result = $success->fetch_assoc()){
                        array_push($images, $result);
                    }
                }

                $gallery['images'] = $images;
                $images = array();
                array_push($galleries,$gallery);

            }

            $db->close();
            return $galleries;

        }else{

            Session::setFlash(AMENU_IMAGE_GALLERIES_LIST_EMPTY, 'warning');
            $db->close();
            return FALSE;

        }

    }//END function getGaleriesList()

    public static function getGalleryById($gallery_id, $need_images = 0){

        $db = Model::getDB();
        $gallery = array();
        $images = array();
        if($need_images === 0){
            $get_query = ("SELECT * FROM galleries WHERE gallery_id = '$gallery_id'");
            $success = $db->query($get_query);
            if(!$success){

                Session::setFlash(ATXT_ERROR_RUNNING_QUERY.'[' . $db->error . ']','error');
                die(ATXT_ERROR_RUNNING_QUERY.'[' . $db->error . ']');

            }

            $found = $success->num_rows;
            if($found > 0){
                while($result = $success->fetch_assoc()){
                    array_push($gallery, $result);
                }
            }
            $db->close();
            return $gallery;
        }else{
            $get_query = ("SELECT * FROM galleries WHERE gallery_id = '$gallery_id'");
            $success = $db->query($get_query);
            if(!$success){

                Session::setFlash(ATXT_ERROR_RUNNING_QUERY.'[' . $db->error . ']','error');
                die(ATXT_ERROR_RUNNING_QUERY.'[' . $db->error . ']');

            }

            $found = $success->num_rows;
            if($found > 0){
                while($result = $success->fetch_assoc()){
                    array_push($gallery, $result);
                }
            }
            $images_query = ("SELECT * FROM gallery_images WHERE image_gallery_id = '$gallery_id'");
            $success = $db->query($images_query);
            if(!$success){

                Session::setFlash(ATXT_ERROR_RUNNING_QUERY.'[' . $db->error . ']','error');
                die(ATXT_ERROR_RUNNING_QUERY.'[' . $db->error . ']');

            }

            $found = $success->num_rows;
            if($found > 0){
                while($result = $success->fetch_assoc()){
                    array_push($images, $result);
                }
            }

            $gallery['images'] = $images;
            $db->close();
            return $gallery;
        }

    }//END FUNCTION getGalleryById($gallery_id)

    public static function updateGallery($data = array()){

        $db = Model::getDB();
        $gallery_id = $data['gallery_id'];
        $gallery_category_id = $data['gallery_category_id'];
        $gallery_name = $data['gallery_name'];
        $gallery_metadata = $data['gallery_metadata'];
        $gallery_description = $data['gallery_description'];
        $gallery_author = Session::get('user_nick');

        $update_gallery_query = ("UPDATE galleries SET
                                  gallery_category_id = '$gallery_category_id',
                                  gallery_name = '$gallery_name',
                                  gallery_metadata = '$gallery_metadata',
                                  gallery_description = '$gallery_description',
                                  gallery_modified  = NOW(),
                                  gallery_author = '$gallery_author' WHERE gallery_id = '$gallery_id'");

        $success = $db->query($update_gallery_query);
        if(!$success){

            Session::setFlash(ATXT_ERROR_RUNNING_QUERY.'[' . $db->error . ']','error');
            die(ATXT_ERROR_RUNNING_QUERY.'[' . $db->error . ']');

        }

        $update_images_query = ("UPDATE gallery_images SET
                                  image_gallery_category_id = '$gallery_category_id',
                                  image_gallery_name = '$gallery_name'
                                  WHERE image_gallery_id = '$gallery_id'");

        $success = $db->query($update_images_query);
        if(!$success){

            Session::setFlash(ATXT_ERROR_RUNNING_QUERY.'[' . $db->error . ']','error');
            die(ATXT_ERROR_RUNNING_QUERY.'[' . $db->error . ']');

        }

        Session::setFlash(AMENU_IMAGE_GALLERIES_EDIT_DONE,'ok');
        $db->close();
        return TRUE;
    }//END function updateGallery($data = array())


    public static function getGalleryImage($gallery_id, $image_id){

        $db = Model::getDB();
        $image = array();
        $query = ("SELECT * FROM gallery_images WHERE image_gallery_id = '$gallery_id' AND image_id = '$image_id'");
        $success = $db->query($query);
        if(!$success){

            Session::setFlash(ATXT_ERROR_RUNNING_QUERY.'[' . $db->error . ']','error');
            die(ATXT_ERROR_RUNNING_QUERY.'[' . $db->error . ']');

        }
        $found = $success->num_rows;
        if($found > 0){
            while($result = $success->fetch_assoc()){
                array_push($image, $result);
            }
        }

        $db->close();
        return $image;

    }//END function getGalleryImage($gallery_id, $image_id)

    public static function updateGalleryImage($image = array()){

        $db = Model::getDB();
        $image_id = $image['image_id'];
        $image_path = $image['image_path'];
        $gallery_id = $image['gallery_id'];
        $image_title = $image['image_title'];
        $image_alt = $image['image_alt'];
        $image_description = $image['image_description'];

        $query = ("UPDATE gallery_images SET
                   image_title = '$image_title',
                   image_alt = '$image_alt',
                   image_description = '$image_description',
                   image_path = '$image_path'
                   WHERE image_id = '$image_id' AND image_gallery_id = '$gallery_id'");

        $success = $db->query($query);
        if(!$success){

            Session::setFlash(ATXT_ERROR_RUNNING_QUERY.'[' . $db->error . ']','error');
            die(ATXT_ERROR_RUNNING_QUERY.'[' . $db->error . ']');

        }

        Session::setFlash(AMENU_IMAGE_EDITED,'ok');
        $db->close();
        return TRUE;

    }//END function updateGalleryImage($image = array())

    public static function deleteGalleryImageSingle($gallery_id, $image_id){
        $db = Model::getDB();
        $query = ("DELETE FROM gallery_images WHERE image_id = '$image_id' AND image_gallery_id = '$gallery_id'");
        $success = $db->query($query);
        if(!$success){

            Session::setFlash(ATXT_ERROR_RUNNING_QUERY.'[' . $db->error . ']','error');
            die(ATXT_ERROR_RUNNING_QUERY.'[' . $db->error . ']');

        }

        Session::setFlash(AMENU_IMAGE_DELETED,'ok');
        $db->close();
        return TRUE;

    }//END function deleteGalleryImageSingle($gallery_id, $image_id)

    public static function deleteGallery($gallery_id){
        $db = Model::getDB();
        //needt to get category id as path to gallery folder was made from cat_id and gallery_id
        $category_id = 0;
        $temp_cat_id = self::getGalleryById($gallery_id);
        foreach($temp_cat_id as $id){
            $category_id = $id['gallery_category_id'];
        }
        $query = ("DELETE FROM galleries WHERE gallery_id = '$gallery_id'");
        $success = $db->query($query);
        if(!$success){

            Session::setFlash(ATXT_ERROR_RUNNING_QUERY.'[' . $db->error . ']','error');
            die(ATXT_ERROR_RUNNING_QUERY.'[' . $db->error . ']');

        }

        $dir = '../public/upload/users/galleries/'.$category_id.'/'.$gallery_id;
        $remove_files = Filesystem::delTree($dir);
        Session::setFlash(AMENU_IMAGE_DELETED,'ok');
        $db->close();
        return TRUE;

    }//END function function deleteGallery($gallery_id)

    public static function galleryCheckEmpty($gallery_id){

        $db = Model::getDB();
        $check_query = ("SELECT * FROM gallery_images WHERE image_gallery_id='$gallery_id'");
        $success = $db->query($check_query);
        if(!$success){

            Session::setFlash(ATXT_ERROR_RUNNING_QUERY.'[' . $db->error . ']','error');
            die(ATXT_ERROR_RUNNING_QUERY.'[' . $db->error . ']');

        }

        $found = $success->num_rows;
        if($found > 0){
            return FALSE;
        }else{
            return TRUE;
        }

    }//END function endGallery($data)

    public static function galleryGetList($gallery_id){

        $db = Model::getDB();
        $check_query = ("SELECT * FROM gallery_images WHERE image_gallery_id='$gallery_id'");
        $success = $db->query($check_query);
        if(!$success){

            Session::setFlash(ATXT_ERROR_RUNNING_QUERY.'[' . $db->error . ']','error');
            die(ATXT_ERROR_RUNNING_QUERY.'[' . $db->error . ']');

        }

        $found = $success->num_rows;
        if($found > 0){
            return FALSE;
        }else{
            return TRUE;
        }

    }//END function endGallery($data)


}//END CLASS