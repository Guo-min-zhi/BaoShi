<?php

$operation = 2;
$a = "animal,googd,haha";
$tagname = "googd";


$tagArray = explode(",", trim($a));

echo sizeof($tagArray);
echo "<br/>";
if($operation == 1){
    // add tag
    if(empty($a)){
        $tagArray[0] = $tagname;
    }else{
        array_push($tagArray, $tagname);
    }

}elseif($operation == 2){
    echo $tagArray[0];
    echo "<br>";

    // delete tag
    for($i=0; $i<sizeof($tagArray); $i++){
        echo $tagArray[$i].";";
        if($tagArray[$i] == $tagname){
            array_splice($tagArray, $i, 1);
//            break;
//            unset($tagArray[$i]);
        }
    }
}
echo sizeof($tagArray);
echo "<br/>";
$newTag = join(",", $tagArray);
echo $newTag;
?>
