<?php
/**
 * Created by Zbigglord
 * Company: HELLUX
 * Date: 2017-11-29
 * Time: 10:41
 */

namespace App\Controllers\Admin;


use App\Models\Admin\Category;
use Core\Controller;
use Core\Session;
use Core\View;
use App\Models\Admin\Medium;
use Core\Filesystem;

class Media extends Controller
{

    public function sliderlistAction(){//ACMe 0001

        if(!isset($_SESSION['user_nick'])){

            Session::setFlash(ATXT_MUST_BE_LOGGED_TO_SEE_ADMIN,'warning');
            header("location: http://".$_SERVER['SERVER_NAME']."/admin");

        }else if(isset($_SESSION['user_nick']) && $_SESSION['show_admin'] == 1){

            $sliders = Medium::sliderGetListAll();
            View::renderAdmin('Media/Slider/list.phtml',['sliders' => $sliders]);

        }
    }//END sliderlistAction()

    public function newsliderAction(){//ACMe 0002

        if(!isset($_SESSION['user_nick'])){

            Session::setFlash(ATXT_MUST_BE_LOGGED_TO_SEE_ADMIN,'warning');
            header("location: http://".$_SERVER['SERVER_NAME']."/admin");

        }else if(isset($_SESSION['user_nick']) && $_SESSION['show_admin'] == 1){

            View::renderAdmin('Media/Slider/new.phtml');

        }
    }//END function newsliderAction()

    public function savesliderAction(){//ACMe 0003

        if(!isset($_SESSION['user_nick'])){

            Session::setFlash(ATXT_MUST_BE_LOGGED_TO_SEE_ADMIN,'warning');
            header("location: http://".$_SERVER['SERVER_NAME']."/admin");

        }else if(isset($_SESSION['user_nick']) && $_SESSION['show_admin'] == 1){

            if(isset($_POST['slider_name']) && $_POST['slider_name'] != ''){

                $new_slider = array();
                $new_slider['name'] = filter_var($_POST['slider_name'],FILTER_SANITIZE_STRING);
                $new_slider['active'] = $_POST['slider_active'];
                Medium::addSlider($new_slider);
                $message = Session::getFlash();
                Session::unsetFlash();
                View::renderAdmin('Media/Slider/list.phtml',['message' => $message]);

            }

        }
    }//END function newsliderAction()

    public function slideraddimagesAction(){//ACMe 0004

        if(!isset($_SESSION['user_nick'])){

            Session::setFlash(ATXT_MUST_BE_LOGGED_TO_SEE_ADMIN,'warning');
            header("location: http://".$_SERVER['SERVER_NAME']."/admin");

        }else if(isset($_SESSION['user_nick']) && $_SESSION['show_admin'] == 1){

            if(isset($_FILES) && !empty($_FILES) && isset($_POST['slider_id']) && $_POST['slider_id'] != 0){

                $media_list = array();
                $slider_id = $_POST['slider_id'];

                foreach($_FILES as $file){
                    array_push($media_list, $file);
                }

                //first we need to change structure of data:
                //so far we have 5 arrays in array one with name another with sizes etc
                //what we need is multi array of images where every images is an array with all belongings
                $multi_images = array();
                $tmp_names = array();//will be independent array of names etc
                $tmp_types = array();
                $tmp_tmp_names = array();
                $tmp_errors = array();
                $tmp_sizes = array();
                $tmp_image_array = array();//will be temp array with gets cleaned every loop for i

                //so we need to make 5 independent array and than get first, second etc element of each 5 and put them together
                //into: array['image_1'], array['image_2'] ... structure
                foreach($media_list as $image){

                    foreach($image as $key => $value){

                        if($key == 'name'){

                            $tmp_names = $value;

                        }elseif($key == 'type'){

                            $tmp_types = $value;

                        }elseif($key == 'tmp_name'){

                            $tmp_tmp_names = $value;

                        }elseif($key == 'size'){

                            $tmp_sizes = $value;

                        }elseif($key == 'error'){

                            $tmp_errors = $value;

                        }else{
                            //do nothing
                        }

                    }
                }

                //now we get lenght of first array (must make it max = max paid plan if empty database if not must delete from array last (x = media already existing) elements) and in loop structurize data:

                for($i = 0; $i < count($tmp_names); $i++){

                    $tmp_image_array['name'] = $tmp_names[$i];
                    $tmp_image_array['type'] = $tmp_types[$i];
                    $tmp_image_array['tmp_name'] = $tmp_tmp_names[$i];
                    $tmp_image_array['size'] = $tmp_sizes[$i];
                    $tmp_image_array['error'] = $tmp_errors[$i];
                    array_push($multi_images,$tmp_image_array);
                    $tmp_image_array = array();

                }//now we have got matching structure where every array is one image with belongins

                $upload_to = 'sliders/'.$slider_id;//this is various path part whitch we send to function as argument
                $ready_to_insert_images = array();

                foreach($multi_images as $image){

                    $tmp = array();
                    $path = Filesystem::uploadImageVarious($image, $upload_to);
                    $tmp['image_path'] = $path;
                    $tmp['slider_id'] = $slider_id;
                    $tmp['image_link'] = '';
                    $tmp['image_alt'] = '';
                    array_push($ready_to_insert_images, $tmp);

                }

                $errors = Medium::sliderAddImages($ready_to_insert_images, $slider_id);
                $message = '';
                if(!empty($errors)){

                    foreach($errors as $key => $value){
                        $message = $message.';'.$value;
                    }

                }

                $sliders = Medium::sliderGetListAll();

                if($message != '' || $message != ';'){

                    View::renderAdmin('Media/Slider/list.phtml',['sliders' => $sliders,'message' => $message]);

                }else{

                    View::renderAdmin('Media/Slider/list.phtml',['sliders' => $sliders]);

                }


            }else{

                $sliders = Medium::sliderGetListAll();

                if($sliders != FALSE){

                    View::renderAdmin('Media/Slider/Images/add.phtml',['sliders' => $sliders]);

                }else{

                    Session::setFlash(MED_SLIDERS_EMPTY,'warning');
                    $message = Session::getFlash();
                    Session::unsetFlash();
                    View::renderAdmin('Media/Slider/list.phtml',['message' => $message]);

                }

            }

        }
    }//END function newsliderAction()


    public function editsliderAction(){//ACMe 0005

        if(!isset($_SESSION['user_nick'])){

            Session::setFlash(ATXT_MUST_BE_LOGGED_TO_SEE_ADMIN,'warning');
            header("location: http://".$_SERVER['SERVER_NAME']."/admin");

        }else if(isset($_SESSION['user_nick']) && $_SESSION['show_admin'] == 1){

            if(isset($_GET['slider_edit_id']) && $_GET['slider_edit_id'] != ''){

                View::renderAdmin('Media/Slider/edit.phtml',['slider_id' => $_GET['slider_edit_id']]);

            }elseif(isset($_POST['slider_id']) && $_POST['slider_id'] != '' && isset($_POST['slider_name']) && $_POST['slider_name'] != ''){

                $data = array();
                $data['slider_id'] = $_POST['slider_id'];
                $data['slider_name'] = $_POST['slider_name'];
                $data['slider_active'] = $_POST['slider_active'];

                $success = Medium::editSlider($data);
                $sliders = Medium::sliderGetListAll();
                $message = Session::getFlash();
                Session::unsetFlash();

                View::renderAdmin('Media/Slider/list.phtml',['sliders' => $sliders, 'message' => $message]);

            }
        }
    }//END function editsliderAction()

    public function editimageAction(){//ACMe 0006

        if(!isset($_SESSION['user_nick'])){

            Session::setFlash(ATXT_MUST_BE_LOGGED_TO_SEE_ADMIN,'warning');
            header("location: http://".$_SERVER['SERVER_NAME']."/admin");

        }else if(isset($_SESSION['user_nick']) && $_SESSION['show_admin'] == 1){

            if(isset($_GET['slider_id']) && $_GET['slider_id'] != '' && isset($_GET['image_id']) && $_GET['image_id'] != ''){

                $image_data = array();
                $image_data['slider_id'] = $_GET['slider_id'];
                $image_data['image_id'] = $_GET['image_id'];
                $image = Medium::getSliderImageSingle($image_data);
                $message = Session::getFlash();
                Session::unsetFlash();

                View::renderAdmin('Media/Slider/Images/edit.phtml',['image' => $image, 'message' => $message]);

            }elseif(isset($_POST['slider_id']) && $_POST['slider_id'] != '' && isset($_POST['image_id']) && $_POST['image_id'] != '' && isset($_POST['slider_image']) && $_POST['slider_image'] != ''){

               $image = array();
                $image['slider_id'] = $_POST['slider_id'];
                $image['image_id'] = $_POST['image_id'];
                $image['image_path'] = $_POST['slider_image'];
                $image['image_link'] = $_POST['image_link'];
                $image['image_alt'] = $_POST['image_alt'];

                 $edited = Medium::editSliderImage($image);
                $sliders = Medium::sliderGetListAll();
                $message = Session::getFlash();
                Session::unsetFlash();

                View::renderAdmin('Media/Slider/list.phtml',['sliders' => $sliders, 'message' => $message]);

            }

        }

    }//END function editimageAction()

    public function sliderdeleteimageAction(){//ACMe 0007

        if(!isset($_SESSION['user_nick'])){

            Session::setFlash(ATXT_MUST_BE_LOGGED_TO_SEE_ADMIN,'warning');
            header("location: http://".$_SERVER['SERVER_NAME']."/admin");

        }else if(isset($_SESSION['user_nick']) && $_SESSION['show_admin'] == 1){

            if(isset($_GET['slider_id']) && $_GET['slider_id'] != '' && isset($_GET['image_id']) && $_GET['image_id'] != ''){

                $deleted = Medium::deleteSliderImageSingle($_GET['slider_id'], $_GET['image_id']);
                $message = Session::getFlash();
                Session::unsetFlash();
                $sliders = Medium::sliderGetListAll();
                View::renderAdmin('Media/Slider/list.phtml',['sliders' => $sliders, 'message' => $message]);

            }

        }else{



        }
    }//END sliderlistAction()

    public function deletesliderAction(){

        if(!isset($_SESSION['user_nick'])){

            Session::setFlash(ATXT_MUST_BE_LOGGED_TO_SEE_ADMIN,'warning');
            header("location: http://".$_SERVER['SERVER_NAME']."/admin");

        }else if(isset($_SESSION['user_nick']) && $_SESSION['show_admin'] == 1){

            if(isset($_GET['slider_delete_id']) && $_GET['slider_delete_id'] != ''){

                $has_images = Medium::checkSliderImages($_GET['slider_delete_id']);
                if($has_images === TRUE){

                    Session::setFlash(MED_SLIDER_HAS_IMAGES,'warning');
                    $message = Session::getFlash();
                    Session::unsetFlash();
                    $sliders = Medium::sliderGetListAll();
                    View::renderAdmin('Media/Slider/list.phtml',['sliders' => $sliders, 'message' => $message]);

                }else{

                    $deleted = Medium::deleteSlider($_GET['slider_delete_id']);
                    Session::setFlash(MED_SLIDER_DELETED,'ok');
                    $message = Session::getFlash();
                    Session::unsetFlash();
                    $sliders = Medium::sliderGetListAll();
                    View::renderAdmin('Media/Slider/list.phtml',['sliders' => $sliders, 'message' => $message]);

                }

            }

        }else{



        }

    }//END deletesliderAction()

    public static function imgaleriesnewAction(){

        if(!isset($_SESSION['user_nick'])){

            Session::setFlash(ATXT_MUST_BE_LOGGED_TO_SEE_ADMIN,'warning');
            header("location: http://".$_SERVER['SERVER_NAME']."/admin");

        }else if(isset($_SESSION['user_nick']) && $_SESSION['show_admin'] == 1){

               $categories_list = Category::categoryGetList(0, 2);

            View::renderAdmin('Media/Galleries/new.phtml',['categories' => $categories_list]);

        }else{



        }

    }

    public static function imgalerieslistAction(){

        if(!isset($_SESSION['user_nick'])){

            Session::setFlash(ATXT_MUST_BE_LOGGED_TO_SEE_ADMIN,'warning');
            header("location: http://".$_SERVER['SERVER_NAME']."/admin");

        }else if(isset($_SESSION['user_nick']) && $_SESSION['show_admin'] == 1){

            $galleries = Medium::getGalleriesList();
            $message = Session::getFlash();
            Session::unsetFlash();
            View::renderAdmin('Media/Galleries/list.phtml',['galleries' => $galleries, 'message' => $message]);

        }else{



        }

    }//END function imgaleriesnewAction()

    public function savegalleryAction(){//ACMe 0004

        if(!isset($_SESSION['user_nick'])){

            Session::setFlash(ATXT_MUST_BE_LOGGED_TO_SEE_ADMIN,'warning');
            header("location: http://".$_SERVER['SERVER_NAME']."/admin");

        }else if(isset($_SESSION['user_nick']) && $_SESSION['show_admin'] == 1){

            if(isset($_FILES) && !empty($_FILES) && isset($_POST['gallery-category-id']) && $_POST['gallery-category-id'] != 0){

                $gallery_name = isset($_POST['gallery-name']) ? filter_var($_POST['gallery-name'], FILTER_SANITIZE_STRING) : 'no name';//just in case
                $gallery_category_id = $_POST['gallery-category-id'];
                $gallery_metadata = isset($_POST['gallery-metawords']) ? filter_var($_POST['gallery-metawords'], FILTER_SANITIZE_STRING) : '';
                $gallery_description = isset($_POST['gallery-description']) ? filter_var($_POST['gallery-description'], FILTER_SANITIZE_STRING) : '';

                $gallery = array();
                $gallery['gallery_category_id'] = $gallery_category_id;
                $gallery['gallery_name'] = $gallery_name;
                $gallery['gallery_metadata'] = $gallery_metadata;
                $gallery['gallery_description'] = $gallery_description;

                $media_list = array();

                foreach($_FILES as $file){
                    array_push($media_list, $file);
                }

                //first we need to change structure of data:
                //so far we have few arrays in array one with name another with sizes etc
                //what we need is multi array of images where every images is an array with all belongings
                $multi_images = array();
                $tmp_names = array();//will be independent array of names etc
                $tmp_types = array();
                $tmp_tmp_names = array();
                $tmp_errors = array();
                $tmp_sizes = array();
                $tmp_image_array = array();//will be temp array with gets cleaned every loop for i

                //so we need to make 5 independent array and than get first, second etc element of each 5 and put them together
                //into: array['image_1'], array['image_2'] ... structure
                foreach($media_list as $image){

                    foreach($image as $key => $value){

                        if($key == 'name'){

                            $tmp_names = $value;

                        }elseif($key == 'type'){

                            $tmp_types = $value;

                        }elseif($key == 'tmp_name'){

                            $tmp_tmp_names = $value;

                        }elseif($key == 'size'){

                            $tmp_sizes = $value;

                        }elseif($key == 'error'){

                            $tmp_errors = $value;

                        }else{
                            //do nothing
                        }

                    }
                }

                for($i = 0; $i < count($tmp_names); $i++){

                    $tmp_image_array['name'] = $tmp_names[$i];
                    $tmp_image_array['type'] = $tmp_types[$i];
                    $tmp_image_array['tmp_name'] = $tmp_tmp_names[$i];
                    $tmp_image_array['size'] = $tmp_sizes[$i];
                    $tmp_image_array['error'] = $tmp_errors[$i];
                    array_push($multi_images,$tmp_image_array);
                    $tmp_image_array = array();

                }//now we have got matching structure where every array is one image with belongins

                $last_inserted_gallery_id = Medium::createGallery($gallery);//first we need to create gallery, then get last inserted id

                $upload_to = 'galleries/'.$gallery_category_id.'/'.$last_inserted_gallery_id;//this is various path part whitch we send to function as argument
                $ready_to_insert_images = array();

                foreach($multi_images as $image){

                    $tmp = array();
                    $path = Filesystem::uploadImageVarious($image, $upload_to);
                    $tmp['image_path'] = $path;
                    $tmp['gallery_category_id'] = $gallery_category_id;
                    $tmp['gallery_name'] = $gallery_name;
                    array_push($ready_to_insert_images, $tmp);

                }


                if($last_inserted_gallery_id != FALSE){

                    $errors = Medium::galleryAddImages($ready_to_insert_images, $last_inserted_gallery_id);
                    $galleries = Medium::getGalleriesList();
                    Session::setFlash(AMENU_IMAGE_GALLERY_ADDED,'ok');
                    $message = Session::getFlash();
                    Session::unsetFlash();
                    View::renderAdmin('Media/Galleries/list.phtml',['galleries' => $galleries, 'message' => $message]);

                }else{

                    $galleries = Medium::getGalleriesList();
                    $message = Session::getFlash();
                    Session::unsetFlash();
                    View::renderAdmin('Media/Galleries/list.phtml',['galleries' => $galleries, 'message' => $message]);

                }
            }else{

                $galleries = Medium::getGalleriesList();
                $message = Session::getFlash();
                Session::unsetFlash();
                View::renderAdmin('Media/Galleries/list.phtml',['galleries' => $galleries, 'message' => $message]);

            }

        }
    }//END function savegalleryAction()

    public function addgalleryimageAction(){

        if(!isset($_SESSION['user_nick'])){

            Session::setFlash(ATXT_MUST_BE_LOGGED_TO_SEE_ADMIN,'warning');
            header("location: http://".$_SERVER['SERVER_NAME']."/admin");

        }else{

            if(isset($_FILES) && !empty($_FILES) && isset($_POST['gallery-id']) && $_POST['gallery-id'] != '') {

                $media_list = array();
                $gallery_id = $_POST['gallery-id'];
                $gallery = Medium::getGalleryById($gallery_id);
                $gallery_category_id = $gallery[0]['gallery_category_id'];
                $gallery_name = $gallery[0]['gallery_name'];

                foreach($_FILES as $file){
                    array_push($media_list, $file);
                }

                //first we need to change structure of data:
                //so far we have few arrays in array one with name another with sizes etc
                //what we need is multi array of images where every images is an array with all belongings
                $multi_images = array();
                $tmp_names = array();//will be independent array of names etc
                $tmp_types = array();
                $tmp_tmp_names = array();
                $tmp_errors = array();
                $tmp_sizes = array();
                $tmp_image_array = array();//will be temp array with gets cleaned every loop for i

                //so we need to make 5 independent array and than get first, second etc element of each 5 and put them together
                //into: array['image_1'], array['image_2'] ... structure
                foreach($media_list as $image){

                    foreach($image as $key => $value){

                        if($key == 'name'){

                            $tmp_names = $value;

                        }elseif($key == 'type'){

                            $tmp_types = $value;

                        }elseif($key == 'tmp_name'){

                            $tmp_tmp_names = $value;

                        }elseif($key == 'size'){

                            $tmp_sizes = $value;

                        }elseif($key == 'error'){

                            $tmp_errors = $value;

                        }else{
                            //do nothing
                        }

                    }
                }

                for($i = 0; $i < count($tmp_names); $i++){

                    $tmp_image_array['name'] = $tmp_names[$i];
                    $tmp_image_array['type'] = $tmp_types[$i];
                    $tmp_image_array['tmp_name'] = $tmp_tmp_names[$i];
                    $tmp_image_array['size'] = $tmp_sizes[$i];
                    $tmp_image_array['error'] = $tmp_errors[$i];
                    array_push($multi_images,$tmp_image_array);
                    $tmp_image_array = array();

                }//now we have got matching structure where every array is one image with belongins

                $upload_to = 'galleries/'.$gallery_category_id.'/'.$gallery_id;//this is various path part whitch we send to function as argument
                $ready_to_insert_images = array();

                foreach($multi_images as $image){

                    $tmp = array();
                    $path = Filesystem::uploadImageVarious($image, $upload_to);
                    $tmp['image_path'] = $path;
                    $tmp['gallery_category_id'] = $gallery_category_id;
                    $tmp['gallery_name'] = $gallery_name;
                    array_push($ready_to_insert_images, $tmp);

                }

                $errors = Medium::galleryAddImages($ready_to_insert_images, $gallery_id);
                $images = Medium::getGalleryById($gallery_id,1);
                $message = '';

                if(!empty($errors)){

                    foreach($errors as $error){

                        $message = $error;

                    }

                }else{

                    Session::setFlash(AMENU_IMAGES_ADDED, 'ok');
                    $message = Session::getFlash();
                    Session::unsetFlash();
                }

                View::renderAdmin('Media/Galleries/Images/list.phtml', ['images' => $images, 'message' => $message]);

            }else{

                $gallery = Medium::getGalleryById($_POST['gallery-id']);
                View::renderAdmin('Media/Galleries/Images/add.phtml', ['gallery' => $gallery]);

            }
        }

    }//END function addgalleryimageAction()

    public function editgalleryAction(){

        if(!isset($_SESSION['user_nick'])){

            Session::setFlash(ATXT_MUST_BE_LOGGED_TO_SEE_ADMIN,'warning');
            header("location: http://".$_SERVER['SERVER_NAME']."/admin");

        }else if(isset($_SESSION['user_nick']) && $_SESSION['show_admin'] == 1){

           if(isset($_POST) && !empty($_POST) && !isset($_POST['gallery-category-id'])){

               $gallery_ids = array();

               foreach($_POST as $key => $value){

                   $part = explode('_',$key);

                   if($part[0] == 'gallery'){

                       array_push($gallery_ids, $value);

                   }
               }

               if(count($gallery_ids) > 1){

                   $galleries = Medium::getGalleriesList();
                   Session::setFlash(AMENU_GALLERIES_CHECKBOX_CHECKED_MORE,'warning');
                   $message = Session::getFlash();
                   Session::unsetFlash();
                   View::renderAdmin('Media/Galleries/list.phtml',['galleries' => $galleries, 'message' => $message]);

               }else{

                    $gallery = Medium::getGalleryById($gallery_ids[0]);
                    $message = Session::getFlash();
                    Session::unsetFlash();
                   View::renderAdmin('Media/Galleries/edit.phtml',['gallery' => $gallery, 'message' => $message]);

               }

           }elseif(isset($_POST['gallery-category-id']) && $_POST['gallery-category-id'] != 0 && isset($_POST['gallery-id']) && $_POST['gallery-id'] != 0){

               $gallery = array();
               $gallery['gallery_category_id'] = $_POST['gallery-category-id'];
               $gallery['gallery_id'] = $_POST['gallery-id'];
               $gallery['gallery_name'] = isset($_POST['gallery-name']) ? filter_var($_POST['gallery-name'], FILTER_SANITIZE_STRING) : 'no name';//just in case
               $gallery['gallery_metadata'] = isset($_POST['gallery-metawords']) ? filter_var($_POST['gallery-metawords'], FILTER_SANITIZE_STRING) : '';
               $gallery['gallery_description'] = isset($_POST['gallery-description']) ? filter_var($_POST['gallery-description'], FILTER_SANITIZE_STRING) : '';

                $success = Medium::updateGallery($gallery);
               $galleries = Medium::getGalleriesList();
               $message = Session::getFlash();
               Session::unsetFlash();
               View::renderAdmin('Media/Galleries/list.phtml',['galleries' => $galleries, 'message' => $message]);

           }
        }

    }//END function editgalleryAction()

    public function editgalleryimageAction(){

        if(!isset($_SESSION['user_nick'])){

            Session::setFlash(ATXT_MUST_BE_LOGGED_TO_SEE_ADMIN,'warning');
            header("location: http://".$_SERVER['SERVER_NAME']."/admin");

        }else if(isset($_SESSION['user_nick']) && $_SESSION['show_admin'] == 1){

            if(isset($_POST) && !empty($_POST) && !isset($_POST['image-id'])){

                    $image_ids = array();

                    foreach($_POST as $key => $value){

                        $part = explode('_',$key);

                        if($part[0] == 'image'){

                            array_push($image_ids, $value);

                        }
                    }

                    if (count($image_ids) > 1) {

                        $images = Medium::getGalleryById($_POST['gallery-id'],1);
                        Session::setFlash(AMENU_IMAGESS_CHECKBOX_CHECKED_MORE, 'warning');
                        $message = Session::getFlash();
                        Session::unsetFlash();
                        View::renderAdmin('Media/Galleries/Images/list.phtml', ['images' => $images, 'message' => $message]);

                    }else{

                        $image = Medium::getGalleryImage($_POST['gallery-id'], $image_ids[0]);
                        $message = Session::getFlash();
                        Session::unsetFlash();
                        View::renderAdmin('Media/Galleries/editimage.phtml',['image' => $image, 'message' => $message]);

                    }


            }elseif(isset($_POST['gallery-id']) && $_POST['gallery-id'] != 0 && isset($_POST['image-id']) && $_POST['image-id'] != 0){

                $image = array();
                $image['image_id'] = $_POST['image-id'];
                $image['image_path'] = $_POST['gallery_image'];
                $image['gallery_id'] = $_POST['gallery-id'];
                $image['image_title'] = isset($_POST['image_title']) ? filter_var($_POST['image_title'], FILTER_SANITIZE_STRING) : 'no name';//just in case
                $image['image_alt'] = isset($_POST['image_alt']) ? filter_var($_POST['image_alt'], FILTER_SANITIZE_STRING) : '';
                $image['image_description'] = isset($_POST['image_description']) ? filter_var($_POST['image_description'], FILTER_SANITIZE_STRING) : '';

                $success = Medium::updateGalleryImage($image);
                $images = Medium::getGalleryById($_POST['gallery-id'],1);
                $message = Session::getFlash();
                Session::unsetFlash();
                View::renderAdmin('Media/Galleries/Images/list.phtml',['images' => $images, 'message' => $message]);

            }
        }

    }//END function editgalleryimageAction()

    public function gallerydeleteimageAction(){

        if(!isset($_SESSION['user_nick'])){

            Session::setFlash(ATXT_MUST_BE_LOGGED_TO_SEE_ADMIN,'warning');
            header("location: http://".$_SERVER['SERVER_NAME']."/admin");

        }else if(isset($_SESSION['user_nick']) && $_SESSION['show_admin'] == 1){

            if(isset($_POST) && !empty($_POST)){

                $image_ids = array();

                foreach($_POST as $key => $value){

                    $part = explode('_',$key);

                    if($part[0] == 'image'){

                        array_push($image_ids, $value);

                    }
                }

                $gallery_id = $_POST['gallery-id'];

                if(count($image_ids) > 0){

                    foreach($image_ids as $image_id){

                        Medium::deleteGalleryImageSingle($gallery_id, $image_id);

                    }

                    $images = Medium::getGalleryById($gallery_id,1);
                    $message = Session::getFlash();
                    Session::unsetFlash();
                    View::renderAdmin('Media/Galleries/Images/list.phtml', ['images' => $images, 'message' => $message]);

                }else{

                    $images = Medium::getGalleryById($gallery_id,1);
                    Session::setFlash(AMENU_IMAGESS_CHECKBOX_CHECKED_MORE, 'warning');
                    $message = Session::getFlash();
                    Session::unsetFlash();
                    View::renderAdmin('Media/Galleries/Images/list.phtml', ['images' => $images, 'message' => $message]);

                }

            }

        }

    }//END function gallerydeleteimageAction()

    public function deletegalleryAction(){

        if(!isset($_SESSION['user_nick'])){

            Session::setFlash(ATXT_MUST_BE_LOGGED_TO_SEE_ADMIN,'warning');
            header("location: http://".$_SERVER['SERVER_NAME']."/admin");

        }else if(isset($_SESSION['user_nick']) && $_SESSION['show_admin'] == 1){

            if (isset($_POST) && !empty($_POST)){

                $gallery_ids = array();

                foreach ($_POST as $key => $value) {

                    $part = explode('_', $key);

                    if ($part[0] == 'gallery') {

                        array_push($gallery_ids, $value);

                    }
                }

                $deleted_counter = 0;
                $has_images = FALSE;

                foreach($gallery_ids as $key => $value){

                    $has_images = Medium::galleryCheckEmpty($value);
                    if($has_images === TRUE){

                        $deleted = Medium::deleteGallery($value);
                        if($deleted === TRUE){

                            $deleted_counter++;

                        }

                        $has_images = FALSE;
                    }

                }

                $galleries = Medium::getGalleriesList();
                Session::setFlash(AMENU_GALLERY_DELETED.' '.$deleted_counter,'ok');
                $message = Session::getFlash();
                Session::unsetFlash();
                View::renderAdmin('Media/Galleries/list.phtml',['galleries' => $galleries, 'message' => $message]);

            }

        }
    }//END function deletegalleryAction()

    public function galleryimagesAction(){

        if(!isset($_SESSION['user_nick'])){

            Session::setFlash(ATXT_MUST_BE_LOGGED_TO_SEE_ADMIN,'warning');
            header("location: http://".$_SERVER['SERVER_NAME']."/admin");

        }else {

            if (isset($_POST) && !empty($_POST)) {

                $gallery_ids = array();

                foreach ($_POST as $key => $value) {

                    $part = explode('_', $key);

                    if ($part[0] == 'gallery') {

                        array_push($gallery_ids, $value);

                    }
                }

                if (count($gallery_ids) > 1) {

                    $galleries = Medium::getGalleriesList();
                    Session::setFlash(AMENU_GALLERIES_CHECKBOX_CHECKED_MORE, 'warning');
                    $message = Session::getFlash();
                    Session::unsetFlash();
                    View::renderAdmin('Media/Galleries/list.phtml', ['galleries' => $galleries, 'message' => $message]);

                } else {

                    $images = Medium::getGalleryById($gallery_ids[0],1);
                    $message = Session::getFlash();
                    Session::unsetFlash();
                    View::renderAdmin('Media/Galleries/Images/list.phtml', ['images' => $images, 'message' => $message]);

                }

            }elseif(isset($_GET['gallery_id'])){//in case button cancel was pressed

                $images = Medium::getGalleryById($_GET['gallery_id'],1);
                $message = Session::getFlash();
                Session::unsetFlash();
                View::renderAdmin('Media/Galleries/Images/list.phtml', ['images' => $images, 'message' => $message]);

            }
        }
    }//END function galleryimagesAction()


}//END CLASS