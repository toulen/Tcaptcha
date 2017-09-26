<?php
namespace Tcaptcha;

class OperationCaptcha
{

    public static function generateType(){
        $types = ['+', '-', '*', '/', '%'];

        shuffle($types);

        return $types[0];
    }

    public static function generateCaptcha($config = []){

        $digit = isset($config['digit']) ? intval($config['digit']) : 2;

        $maxNumber = pow(10, $digit);

        $number1 = rand(0, $maxNumber);

        $number2 = rand(1, $maxNumber);

        $type = isset($config['type']) ? $config['type'] : self::generateType();

        $answer = 0;

        switch($type){
            default:
            case '+':
                $answer = $number1 + $number2;
                break;
            case '-':
                $answer = $number1 - $number2;
                break;
            case '*':
                $answer = $number1 * $number2;
                break;
            case '/':
                $answer = floor($number1 / $number2);
                break;
            case '%':
                $answer = $number1 % $number2;
                break;
        }


        return [
            'show' => "$number1{$type}$number2=?",
            'answer' => $answer
        ];
    }
}