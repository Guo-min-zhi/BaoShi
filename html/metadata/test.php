<?php
/**
 * Created by PhpStorm.
 * User: guominzhi
 * Date: 14-10-30
 * Time: 下午9:59
 */

include "EXIF.php";

function dump($varVal, $isExit = FALSE){
    ob_start();
    var_dump($varVal);
    $varVal = ob_get_clean();
    $varVal = preg_replace("/\]\=\>\n(\s+)/m", "] => ", $varVal);
    echo '<pre>'.$varVal.'</pre>';
    $isExit && exit();
}

echo "EXIF information: <br/>";
dump(get_EXIF_JPEG("mm.jpg"));


?>