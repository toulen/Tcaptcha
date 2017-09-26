<?php
namespace Tcaptcha;

class NormalCaptcha
{

    private static function generateLetter(){
        $letters = array_merge(range('a', 'z'), range('A', 'Z'));

        $diff = ['I','l','o','O','v','V'];

        $lastLetters = array_diff($letters, $diff);

        shuffle($lastLetters);

        return $lastLetters;
    }

    public static function generateCaptcha($config = []){

        // size, type, showCodes

        $type = isset($config['type']) ? $config['type'] : 'number';

        if(!isset($config['showCodes'])){
            switch($type){
                default:
                case "number":
                    $codes = range(0, 9);
                    break;

                case "letter":
                    $codes = self::generateLetter();
                    break;

                case 'blend':
                    $codes = array_merge(range(1, 9), self::generateLetter());
                    break;
                case 'zh_cn':
                    $codes = ['你','我','他','都','是','好','人'];
                    break;
            }
        }else{
            $codes = $config['showCodes'];
        }

        if(is_array($codes)){
            $codes = join('', $codes);
        }


        $size = isset($config['size']) ? intval($config['size']) : 4;

        $captchaCode = '';

        for($i = 0; $i < $size; $i ++){
            $randKey = rand(0, mb_strlen($codes, 'utf-8') - 1);
            $subStr = $codes;
            $captchaCode .= mb_substr($subStr, $randKey, 1, 'utf-8');
        }

        if($type == 'zh_cn'){
//            $captchaCode = iconv('gb2312', 'utf-8', $captchaCode);
//            $captchaCode = iconv('utf-8', 'gb2312', $captchaCode);
        }
        return [
            'show' => $captchaCode,
            'answer' => $captchaCode
        ];

    }
}