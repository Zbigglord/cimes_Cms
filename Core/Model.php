<?php
/**
 * Creator: BabooJaga
 * Date: 2016-02-14
 * Time: 16:35
 */

namespace Core;
use App\Config;

abstract class Model {


    protected function getDB(){

         $DB_conn = NULL;

        if($DB_conn === NULL) {

            $DB_conn = new \mysqli(config::DB_HOST, config::DB_USER, config::DB_PASSWORD, config::DB_NAME);

            /* check connection */
            if ($DB_conn->connect_errno > 0) {
                printf("Connect failed: %s\n", $DB_conn->connect_error);
                exit();
            }
        }
        return $DB_conn;
    }

} //END CLASS