<?php
/**
 * Created by PhpStorm.
 * User: David
 * Date: 12-6-14
 * Time: 13:48
 */

class Button
{
    public function __construct($name, $postValue)
    {
        echo    "<form action=\"/index.php\" method=\"post\">";
        echo    "<input type=\"hidden\" name=\"display\" value=".$postValue.">";
        echo    "<input class=\"nav\" type=\"submit\" value=".$name.">";
        echo    "</form>";
    }
} 