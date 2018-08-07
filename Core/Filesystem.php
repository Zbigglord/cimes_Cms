<?php
/**
 * Creator: BabooJaga
 * Date: 2016-04-24
 * Time: 15:07
 */

namespace Core;
use \DOMDocument;

class Filesystem extends Model{
// !IMPORTANT xml files must have names equal to root element in xml file (or root element named as filename:) ) !IMPORTANT

    public static function dataLoadFromXml($file = ''){//CF-001
        $file_path = '../app/files/xml/'.$file.'.xml';
        if(!$xml_content = simplexml_load_file($file_path)){
            Session::setFlash(ATXT_ERROR_READING_XML,'error');
            return false;
        }else{
            return $xml_content;
        }
    }

    public static function dataSaveToXml($root_parent='',$main_child='',$data=array()){//CF-002
//should work for most listings from database array of arrays

        $is_root_ok = (isset($root_parent) && $root_parent != '') ? true : false;
        $is_child_ok = (isset($main_child) && $main_child != '') ? true : false;
        $is_data_ok = (is_array($data) && !empty($data)) ? true : false;

        if($is_root_ok == false){
            Session::setFlash(ATXT_ERROR_FILESYSTEM.'CF-002-31','error');
            exit;
        }elseif($is_child_ok == false){
            Session::setFlash(ATXT_ERROR_FILESYSTEM.'CF-002-34','error');
            exit;
        }elseif($is_data_ok == false){
            Session::setFlash(ATXT_ERROR_FILESYSTEM.'CF-002-37','error');
            exit;
        }else{
            $doc = new DOMDocument('1.0','UTF-8');
            $doc->formatOutput = true;
            $root = $doc->createElement( $root_parent );//main parent element in xml
            $doc->appendChild( $root );
            foreach($data as $parent ){
                $data_parent = $doc->createElement( $main_child );//create object than create and append child nodes, and finally append whole object to MAIN NODE

                  foreach($parent as $key => $value){
                      $pom = $key;//no weird characters and empty spaces in xml elements name
                      $pom = str_replace( array( '\'', '"', ',' , ';', '<', '>','_', ), '', $pom);
                      $pom =  str_replace(' ', '', $pom );
                      $key_name = $doc->createElement( $pom );
                      $key_name->appendChild(
                          $doc->createTextNode( $value )
                      );
                      $data_parent->appendChild( $key_name );
                  }

                $root->appendChild( $data_parent );//append whole inner array to parent node $root
            }
            $root_path = dirname(dirname(dirname(dirname(__FILE__))));
            $file_path = $root_path.'/app/files/xml/'.$root_parent.'.xml';
            $file = fopen($file_path,'w');
            if(!$file){
                Session::setFlash(ATXT_ERROR_CREATE_FILE,'error');
                return false;
            }else{
                $finput = $doc->saveXML();
                fwrite($file,$finput);
                fclose($file);
                Session::setFlash(ATXT_SUCCESS_CREATE_FILE,'ok');
                return true;
            }
        }

    }
//IMAGES

        public static function uploadImage($images = array()){
            if(isset($images) && !empty($images)){
              $img_name = filter_var($images["name"], FILTER_SANITIZE_STRING);//sanitize names
              $img_name = mb_strtolower($img_name, 'UTF-8');//make all lowercase
              $img_name = str_replace(array('ą','ć','ę','ł','ó','ź','ż'), array('a','c','e','l','o','z','z'), $img_name);//replace polish chars in names if exist
              $img_type = $images["type"];
              $img_temp_path = $images["tmp_name"];
              $img_error = $images["error"];
              $img_size = round(($images["size"] / 1024) /1024, 3);
             // $current_file = '../'.__DIR__;
                $new_image = array(
                    'name' => $img_name,
                    'type' => $img_type,
                    'tmp' => $img_temp_path,
                    'error' => $img_error,
                    'size' => $img_size,
                );
                $new_path = '../public/upload/images/categories/'.$new_image['name'];
               if(!move_uploaded_file($new_image['tmp'], $new_path)){
                   Session::setFlash(ATXT_CATEGORY_MOVE_IMAGE_ERROR,'error');
                   return FALSE;
               }else{
                   Session::setFlash(ATXT_CATEGORY_IMAGE_MOVED,'ok');
                   $new_path = '/upload/images/categories/'.$new_image['name'];
                   return $new_path;
               }
            }else{
                return NULL;
            }
        }

    public static function uploadImageCourse($images = array()){
        if(isset($images) && !empty($images)){
            $img_name = filter_var($images["name"], FILTER_SANITIZE_STRING);//sanitize names
            $img_name = mb_strtolower($img_name, 'UTF-8');//make all lowercase
            $img_name = str_replace(array('ą','ć','ę','ł','ó','ź','ż'), array('a','c','e','l','o','z','z'), $img_name);//replace polish chars in names if exist
            $img_type = $images["type"];
            $img_temp_path = $images["tmp_name"];
            $img_error = $images["error"];
            $img_size = round(($images["size"] / 1024) /1024, 3);
            // $current_file = '../'.__DIR__;
            $new_image = array(
                'name' => $img_name,
                'type' => $img_type,
                'tmp' => $img_temp_path,
                'error' => $img_error,
                'size' => $img_size,
            );
            $new_path = '../public/upload/images/courses/'.$new_image['name'];
            if(!move_uploaded_file($new_image['tmp'], $new_path)){
                Session::setFlash(ATXT_CATEGORY_MOVE_IMAGE_ERROR,'error');
                return FALSE;
            }else{
                Session::setFlash(ATXT_CATEGORY_IMAGE_MOVED,'ok');
                $new_path = '/upload/images/courses/'.$new_image['name'];
                return $new_path;
            }
        }else{
            return NULL;
        }
    }

    public static function uploadImageVarious($images = array(), $img_path){//strictly for uploading images to various paths on server
        if(isset($images) && !empty($images)){
            $img_name = filter_var($images["name"], FILTER_SANITIZE_STRING);//sanitize names
            $img_name = mb_strtolower($img_name, 'UTF-8');//make all lowercase
            $img_name = str_replace(array('ą','ć','ę','ł','ó','ź','ż'), array('a','c','e','l','o','z','z'), $img_name);//replace polish chars in names if exist
            $img_type = $images["type"];
            $img_temp_path = $images["tmp_name"];
            $img_error = $images["error"];
            $img_size = round(($images["size"] / 1024) /1024, 3);
            // $current_file = '../'.__DIR__;
            $new_image = array(
                'name' => $img_name,
                'type' => $img_type,
                'tmp' => $img_temp_path,
                'error' => $img_error,
                'size' => $img_size,
            );

            if (!file_exists('../public/upload/users/'.$img_path)) {
                mkdir('../public/upload/users/'.$img_path, 0777, true);
            }
            $new_path = '../public/upload/users/'.$img_path.'/'.$new_image['name'];
            if(!move_uploaded_file($new_image['tmp'], $new_path)){
                Session::setFlash(ATXT_CATEGORY_MOVE_IMAGE_ERROR,'error');
                return FALSE;
            }else{
                Session::setFlash(ATXT_CATEGORY_IMAGE_MOVED,'ok');
                $new_path = '/upload/users/'.$img_path.'/'.$new_image['name'];
                return $new_path;
            }
        }else{
            return NULL;
        }
    }

    public static function uploadImageLogo($images = array()){
        if(isset($images) && !empty($images)){
            $img_name = filter_var($images["name"], FILTER_SANITIZE_STRING);//sanitize names
            $img_name = mb_strtolower($img_name, 'UTF-8');//make all lowercase
            $img_name = str_replace(array('ą','ć','ę','ł','ó','ź','ż'), array('a','c','e','l','o','z','z'), $img_name);//replace polish chars in names if exist
            $img_type = $images["type"];
            $img_temp_path = $images["tmp_name"];
            $img_error = $images["error"];
            $img_size = round(($images["size"] / 1024) /1024, 3);
            // $current_file = '../'.__DIR__;
            $new_image = array(
             'name' => $img_name,
             'type' => $img_type,
             'tmp' => $img_temp_path,
             'error' => $img_error,
             'size' => $img_size,
            );
            $new_path = '../public/upload/images/config/'.$new_image['name'];
            if(!move_uploaded_file($new_image['tmp'], $new_path)){
                Session::setFlash(ATXT_CATEGORY_MOVE_IMAGE_ERROR,'error');
                return FALSE;
            }else{
                Session::setFlash(ATXT_CATEGORY_IMAGE_MOVED,'ok');
                $new_path = '/upload/images/config/'.$new_image['name'];
                return $new_path;
            }
        }else{
            return NULL;
        }
    }

// DIRECTORIES AND FILES
    public static function browseDir($path){//experimental - to do
        $folders_group = array();
        $files = new \RecursiveDirectoryIterator($path);
        $files->setFlags(\FilesystemIterator::SKIP_DOTS | \FilesystemIterator::UNIX_PATHS);

        foreach (new \RecursiveIteratorIterator($files, \RecursiveIteratorIterator::SELF_FIRST) as  $file) {
            if (!$file->isFile()) { //create hyperlink if this is a folder
                $folder = '<span><img src="/adminfiles/images/css/folder_small.png"><span style="margin-left: 5px;">'.$file->getPath().'/'. $file->getFilename() .'</span></span>';
               array_push($folders_group, $folder);
            }else{ //do not link if this is a file
                $_file = $file->getPath().'/'. $file->getFilename();
                array_push($folders_group, $_file);
             }
        }

       return $folders_group;
    }//END BROWSE DIR

    public static function delTree($dir){

    $files = array_diff(scandir($dir), array('.', '..'));

    foreach ($files as $file) {
        (is_dir("$dir/$file")) ? self::delTree("$dir/$file") : unlink("$dir/$file");
    }

    return rmdir($dir);
}

    public static function getFolderFromPath($path){
        $path = explode('/',$path);
        $path = end($path);
        return $path;
    }

    public static function changeGroupFolderName($old, $new){
        $root = dirname(dirname(__FILE__));
        $old_name = $root.'/'.'public/upload/kursy/'.$old;
        $new_name = $root.'/'.'public/upload/kursy/'.$new;
        $success = rename($old_name, $new_name);
        if(!$success){
            return FALSE;
        }else{
            return $new_name;
        }
    }

    public static function checkMimeType($ext){

        $mime_types=array();

        $mime_types['ai']    ='application/postscript';
        $mime_types['asx']   ='video/x-ms-asf';
        $mime_types['au']    ='audio/basic';
        $mime_types['avi']   ='video/x-msvideo';
        $mime_types['bmp']   ='image/bmp';
        $mime_types['css']   ='text/css';
        $mime_types['doc']   ='application/msword';
        $mime_types['docx']   ='application/msword';
        $mime_types['eps']   ='application/postscript';
        $mime_types['exe']   ='application/octet-stream';
        $mime_types['gif']   ='image/gif';
        $mime_types['htm']   ='text/html';
        $mime_types['html']  ='text/html';
        $mime_types['ico']   ='image/x-icon';
        $mime_types['jpe']   ='image/jpeg';
        $mime_types['jpeg']  ='image/jpeg';
        $mime_types['jpg']   ='image/jpeg';
        $mime_types['js']    ='application/x-javascript';
        $mime_types['mid']   ='audio/mid';
        $mime_types['mov']   ='video/quicktime';
        $mime_types['mp3']   ='audio/mpeg';
        $mime_types['mpeg']  ='video/mpeg';
        $mime_types['mpg']   ='video/mpeg';
        $mime_types['pdf']   ='application/pdf';
        $mime_types['pps']   ='application/vnd.ms-powerpoint';
        $mime_types['ppt']   ='application/vnd.ms-powerpoint';
        $mime_types['ps']    ='application/postscript';
        $mime_types['pub']   ='application/x-mspublisher';
        $mime_types['qt']    ='video/quicktime';
        $mime_types['rtf']   ='application/rtf';
        $mime_types['svg']   ='image/svg+xml';
        $mime_types['swf']   ='application/x-shockwave-flash';
        $mime_types['tif']   ='image/tiff';
        $mime_types['tiff']  ='image/tiff';
        $mime_types['txt']   ='text/plain';
        $mime_types['wav']   ='audio/x-wav';
        $mime_types['wmf']   ='application/x-msmetafile';
        $mime_types['xls']   ='application/vnd.ms-excel';
        $mime_types['xml']   ='application/xml';
        $mime_types['zip']   ='application/zip';

        $mime_value = '';

        if(array_key_exists($ext,$mime_types)){
            foreach($mime_types as $key => $value){
                if($key == $ext){
                    $mime_value = $value;
                }
            }

            return $mime_value;

        }else{

            return FALSE;
        }
    }//END function checkMimeType($ext)

    public static function matchImageToExt($ext){

        $imgs = array(
            'ai',
            'avi',
            'css',
            'csv',
            'dbf',
            'doc',
            'docx',
            'dwg',
            'exe',
            'html',
            'iso',
            'jpg',
            'js',
            'json',
            'mp3',
            'mp4',
            'pdf',
            'png',
            'ppt',
            'psd',
            'rtf',
            'svg',
            'txt',
            'xls',
            'xlsx',
            'xml',
            'zip',
            'rar'
        );

        if(in_array($ext, $imgs)){

            return $ext;

        }else{

            return 'file';

        }

    }//END matchImageToExt($ext)

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////IMAGES

public static function createNewImage($absolute_path){

    $allowed_types = [1,2,3];
    $image_type = exif_imagetype($absolute_path);
    $new_image = array();//prepare for new image
    $img_data = getimagesize($absolute_path);
    $width = $img_data[0];
    $height = $img_data[1];
    if(in_array($image_type, $allowed_types)){

        switch($image_type){

            case 1:
                $new_image['image'] = imagecreatefromgif($absolute_path);
                $new_image['type'] = 1;
                break;
            case 2:
                $new_image['image'] = imagecreatefromjpeg($absolute_path);
                $new_image['type'] = 2;
                break;
            case 3:
                $new_image['image'] = imagecreatefrompng($absolute_path);
                $new_image['type'] = 3;
                break;
            default:
                $new_image['image'] = imagecreatefromjpeg($absolute_path);
                $new_image['type'] = 2;
                break;

        }

    }else{//if not than create replacement

        $new_image['image']  = imagecreatetruecolor(400,300);
        $bgc = imagecolorallocate($new_image['image'], 255, 255, 255);
        $tc  = imagecolorallocate($new_image['image'], 0, 0, 0);
        imagefilledrectangle($new_image['image'], 0, 0, 400, 300, $bgc);
        imagestring($new_image['image'], 1, 5, 5, 'Error loading image', $tc);
        $new_image['type'] = 2;
    }

    $new_image['width'] = $width;
    $new_image['height'] = $height;

    return $new_image;

}//END function createNewImage($absolute_path)

    public static function cropToThumbnail($new_image,$filename,$start = 'center', $width, $height){

        $x = 0;
        $y = 0;

        if($start == 'center'){//todo

            $x = ($new_image['width'])/2;
            $y = ($new_image['height'])/2;

            if($new_image['width']  < ($x + $width)){

                $x = $new_image['width'] - $width;
            }

            if($new_image['height'] < ($y + $height)){

                $y = $new_image['height'] - $height;
            }
        }


        $cropped = imagecrop($new_image['image'], ['x' => $x, 'y' => $y, 'width' => $width, 'height' => $height]);

        switch($new_image['image']){

            case 1:
                imagegif($cropped,TEMP_DIR.$filename);
                break;
            case 2:
                imagejpeg($cropped,TEMP_DIR.$filename);
                break;
            case 3:
                imagepng($cropped,TEMP_DIR.$filename);
                break;
            default:
                imagejpeg($cropped,TEMP_DIR.$filename);
                break;

        }

        imagedestroy($cropped);

    }//END function cropToThumbnail($new_image)

    public static function generateCaptcha(){

        $image = imagecreatetruecolor(205, 50);
        $background_color = imagecolorallocate($image, 255, 255, 255);
        imagefilledrectangle($image,0,0,205,50,$background_color);
//generate lines
        $line_color = imagecolorallocate($image, 64,64,64);
        for($i=0;$i<10;$i++) {
            imageline($image,0,rand()%50,205,rand()%50,$line_color);
        }
//generate dots
        $pixel_color = imagecolorallocate($image, 0,0,255);
        for($i=0;$i<1000;$i++) {
            imagesetpixel($image,rand()%205,rand()%50,$pixel_color);
        }
//generate letters
        $word = '';
        $letters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz123456789';
        $len = strlen($letters);
        $font = ROOT_DIR.'/upload/font/OpenSans.ttf';
        $text_color = imagecolorallocate($image, 0,0,0);
        for ($i = 0; $i< 6;$i++) {
            $letter = $letters[rand(0, $len-1)];
//imagestring($image, 35,  5+($i*30), 20, $letter, $text_color);
            $word.=$letter;
        }
        imagettftext($image, 26, 0, 30, 40, $text_color, $font, $word);
//$_SESSION['captcha_string'] = $word;
        imagepng($image, TEMP_DIR.'captcha.png');
        return $word;

    }//END generateCaptcha()

    public static function uploadFile($images = array(), $img_path){//strictly for uploading images to various paths on server

        if(isset($images) && !empty($images)){
            $img_name = filter_var($images["name"], FILTER_SANITIZE_STRING);//sanitize names
            $img_name = mb_strtolower($img_name, 'UTF-8');//make all lowercase
            $img_name = str_replace(array('ą','ć','ę','ł','ó','ź','ż'), array('a','c','e','l','o','z','z'), $img_name);//replace polish chars in names if exist
            $img_type = $images["type"];
            $img_temp_path = $images["tmp_name"];
            $img_error = $images["error"];
            $img_size = round(($images["size"] / 1024) /1024, 3);
            // $current_file = '../'.__DIR__;
            $new_image = array(
                'name' => $img_name,
                'type' => $img_type,
                'tmp' => $img_temp_path,
                'error' => $img_error,
                'size' => $img_size,
            );

            if (!file_exists('../public/upload/files/'.$img_path)) {
                mkdir('../public/upload/files/'.$img_path, 0777, true);
            }
            $new_path = '../public/upload/files/'.$img_path.$new_image['name'];
            if(!move_uploaded_file($new_image['tmp'], $new_path)){
                Session::setFlash(ATXT_CATEGORY_MOVE_IMAGE_ERROR,'error');
                return FALSE;
            }else{
                Session::setFlash(ATXT_CATEGORY_IMAGE_MOVED,'ok');
                $new_path = '/upload/files/'.$img_path.$new_image['name'];
                return $new_path;
            }

        }else{

            return NULL;

        }
    }


} //END CLASS FILESYSTEM