<?php
/**
 * Created by PhpStorm.
 * User: jaroslav
 * Date: 22.05.17
 * Time: 15:54
 */

namespace app\controllers;


use app\commands\ParseController;

class ParsingController extends ParseController
{

    public function actionRowcreator(){

        $file = file("log.txt");
        $mass = [];
        array_map(function($v) use (&$mass){
            $exp = explode(";",$v);
            $mass[] = [$exp[0],"phone" =>$exp[1]];
        },$file);

        $element = 1;
        $param1 = $mass[$element-1]['phone'];
        echo $param1;

    }

}