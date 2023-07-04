<?php
/**
 * Created by PhpStorm.
 * User: TSDC-User
 * Date: 2/4/2017
 * Time: 5:13 PM
 */
//header("Location: http://www.google.com/");
//echo "dsd";

if(isset($_GET['params']) && !empty($_GET['params'])){
    ////$myfile = fopen("newfile.txt", "w") or die("Unable to open file!");
    $params = trim(strip_tags($_GET['params']));
    $aDecodeData = base64_decode($params);
    $aParamData = json_decode($aDecodeData);
    var_dump($aParamData);
}