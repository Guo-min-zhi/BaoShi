<?php
/**
 * Created by PhpStorm.
 * User: guominzhi
 * Date: 14-10-30
 * Time: 下午9:59
 */

include "EXIF.php";

echo "EXIF information: <br/>";
var_dump(get_EXIF_JPEG("test.jpg"));