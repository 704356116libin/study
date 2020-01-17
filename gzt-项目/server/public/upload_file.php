<?php
/**
 * Created by PhpStorm.
 * User: bin
 * Date: 2019/4/21
 * Time: 10:40
 */
if ($_FILES["file"]["error"] > 0)
{
    echo "错误：" . $_FILES["file"]["error"] . "<br>";
}
else {
    echo "上传文件名: " . $_FILES["file"]["name"] . "<br>";
    echo "文件类型: " . $_FILES["file"]["type"] . "<br>";
    echo "文件大小: " . ($_FILES["file"]["size"] / 1024) . " kB<br>";
    echo "文件临时存储位置: " . $_FILES["file"]["tmp_name"]  . " <br>";
}