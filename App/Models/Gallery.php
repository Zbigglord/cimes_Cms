<?php
/**
 * Creator: BabooJaga
 * Date: 2016-04-17
 * Time: 10:01
 */
namespace App\Models;

use Core\Model;
use Core\Session;

class Gallery extends Model{

    public static function getRandomImageFromGallery($gallery_id){//gets random image to add to gallery as thumbnail

        $db = Model::getDB();
        $ids = array();
        $random_id = 0;
        $image_path = '';
        $query = ("SELECT * FROM gallery_images WHERE image_gallery_id = '$gallery_id'");
        $success = $db->query($query);
        if(!$success){
            die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
        }

        $found = $success->num_rows;
        if($found > 0){
            while($result = $success->fetch_assoc()){
                $ids[] = $result['image_id'];
            }
        }

        $random_id = $ids[array_rand($ids)];
        $query = ("SELECT * FROM gallery_images WHERE image_id = '$random_id'");
        $success = $db->query($query);
        if(!$success){
            die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
        }
        if($found > 0){
            while($result = $success->fetch_assoc()){
                $image_path = $result['image_path'];
            }

            return $image_path;
        }else{

            return FALSE;
        }

    }//END function getRandomImageFromGallery($gallery_id)

    public static function getAllGalleriesLight($category_id = 0){//arg will be optional if set than all galeries from category if not than absolutely all galleries

        $db = Model::getDB();
        $galleries = array();
        if($category_id != 0){
            $query = ("SELECT * FROM galleries WHERE gallery_category_id = '$category_id' ORDER BY gallery_modified DESC");
        }else{
            $query = ("SELECT * FROM galleries ORDER BY gallery_modified DESC");
        }
        $success = $db->query($query);
        if(!$success){
            die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
        }

        $found = $success->num_rows;
        if($found > 0){
            while($result = $success->fetch_assoc()){
                $gal_temp_id = $result['gallery_id'];
                $thumbnail = self::getRandomImageFromGallery($gal_temp_id);
                if($thumbnail != FALSE){
                    $result['thumbnail'] = $thumbnail;
                }else{
                    $result['thumbnail'] = 'none';
                }
                $galleries[] = $result;
            }
        }

        $db->close();
        return $galleries;

    }//END function getAllGalleriesLight($category_id = 0)

    public static function galleryGetItems($gallery_id){

        $db = Model::getDB();
        $items = array();
        $query = ("SELECT * FROM gallery_images WHERE image_gallery_id = '$gallery_id'");
        $success = $db->query($query);
        if(!$success){
            die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
        }

        $found = $success->num_rows;
        if($found > 0){
            while($result = $success->fetch_assoc()){
               array_push($items,$result) ;
            }

            $db->close();
            return $items;
        }else{
            Session::setFlash(AMENU_IMAGE_GALLERIES_IMAGES_EMPTY,'warning');
            $db->close();
            return FALSE;
        }

    }//END function galleryGetItems($gallery_id)

    public static function getGalleryById($gallery_id){

        $db = Model::getDB();
        $data = array();

        $query = ("SELECT * FROM galleries WHERE gallery_id = '$gallery_id'");

        $success = $db->query($query);
        if(!$success){
            die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
        }

        $found = $success->num_rows;
        if($found > 0){
            while($result = $success->fetch_assoc()){
                array_push($data,$result) ;
            }

        }

        $db->close();
        return $data;

    }//END function getGalleryById($_gallery_id)

    public static function getNewGalleries($limit){

        $db = Model::getDB();
        $data = array();

        $query = ("SELECT * FROM galleries ORDER BY gallery_created LIMIT $limit");

        $success = $db->query($query);
        if(!$success){
            die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
        }

        $found = $success->num_rows;
        if($found > 0){
            while($result = $success->fetch_assoc()){
                array_push($data,$result) ;
            }

        }

        $db->close();
        return $data;

    }//END function getGalleryById($_gallery_id)

}//END CLASS