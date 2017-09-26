<?php
namespace Tcaptcha;

/**
 * 实现验证码的基类
 * Class Tcaptcha
 * @package Tcaptcha
 */
class Tcaptcha
{
    protected $config = [];

    /**
     * @var
     * 取值范围 [
     * 'normal', 'operation'
     * ]
     */
    public $type = 'normal';

    public $captchaOptions = [];

    public $width;

    public $height = 30;

    public $fontSize = 14;

    public $points = 100;

    public $angle = 45;

    /**
     * @var
     * like "100,255,100"
     */
    public $backgroundColor;


    /**
     * @var
     * like "100.234,255"
     */
    public $fontColor;


    public function __construct($config=[]){
        $this->setConfig($config);
    }

    private function setConfig($config){

        $this->config = array_merge($this->config, $config);

        foreach($this->config as $key => $cfg){
            if(property_exists(self::class, $key)) {
                $this->$key = $cfg;
            }
        }
    }

    /**
     * 执行生成验证码
     * @throws \Exception
     */
    public function generate(){
        $className = '\\Tcaptcha\\' . ucfirst($this->type) . 'Captcha';

        if(!class_exists($className)){
            throw new \Exception('类' . $className . '找不到!');
        }

        $captchaInfo = $className::generateCaptcha($this->captchaOptions);

        $this->genertateImage($captchaInfo);
    }


    /**
     * 生成验证码图片
     * @param $captchaInfo
     * @throws \Exception
     */
    private function genertateImage($captchaInfo){

        if(!isset($captchaInfo['show']) || !isset($captchaInfo['answer'])){
            throw new \Exception('请先生成验证码内容!');
        }

        // 存入SESSION
        try {
            session_start();

            $_SESSION['tCaptcha'] = md5($captchaInfo['answer']);

        }catch(\Exception $e){
            throw new \Exception('SESSION ERROR...');
        }

        if(!$this->width){
            $this->width = ($this->fontSize + 5) * mb_strlen($captchaInfo['show'], 'utf-8');
        }

        if(!$this->height){
            $this->height = $this->fontSize + 20;
        }

        ob_clean();

        @header("Content-Type:image/png");

        $image = imagecreatetruecolor($this->width, $this->height);

        if(!$this->backgroundColor){
            $bgColor[] = rand(0, 255);
            $bgColor[] = rand(0, 255);
            $bgColor[] = rand(0, 255);
        }else{
            $bgColor = explode(',', $this->backgroundColor);
            if(count($bgColor) < 3){
                if(isset($bgColor[1])){
                    $bgColor[2] = $bgColor[1];
                }else{
                    $bgColor[1] = $bgColor[0];
                    $bgColor[2] = $bgColor[0];
                }
            }
        }

        $backgroundColor = imagecolorallocate($image, $bgColor[0], $bgColor[1], $bgColor[2]);

        imagefill($image, 0, 0, $backgroundColor);

        $pix  = imagecolorallocate($image, 100, 100, 100);

        if(!$this->fontColor){
            $ftColor[] = 255 - $bgColor[0];
            $ftColor[] = 255 - $bgColor[1];
            $ftColor[] = 255 - $bgColor[2];
        }else{
            $ftColor = explode(',', $this->fontColor);
            if(count($ftColor) < 3){
                if(isset($ftColor[1])){
                    $ftColor[2] = $ftColor[1];
                }else{
                    $ftColor[1] = $ftColor[0];
                    $ftColor[2] = $ftColor[0];
                }
            }
        }

        $font = imagecolorallocate($image, $ftColor[0], $ftColor[1], $ftColor[2]);

        if($this->points) {
            mt_srand();
            for ($i = 0; $i < $this->points; $i++) {
                imagesetpixel($image, mt_rand(0, $this->width), mt_rand(0, $this->height), $pix);
            }
        }

        $show = $captchaInfo['show'];

        $fontX = ($this->width - 20) / mb_strlen($show, 'utf-8');

        $fontY = ($this->height - $this->fontSize) / 2;

        for($i = 0; $i < mb_strlen($show, 'utf-8'); $i++) {

            $str = mb_substr($show, $i, 1, 'utf-8');

            $x = (($fontX - $this->fontSize) / 2 )+ ($fontX * $i) + 10;

            imagettftext($image, $this->fontSize, rand(0 - $this->angle, $this->angle), $x, $fontY + $this->fontSize, $font, __DIR__ . '/../font/e.ttc', $str);

        }

        imagepng($image);

        imagedestroy($image);
    }


    /**
     * 验证验证码
     * @param $code
     * @return bool
     * @throws \Exception
     */
    public static function check($code){

        if(!$code){
            return false;
        }

        $code = md5($code);

        try{
            session_start();

            $sessionCode = $_SESSION['tCaptcha'];
        }catch(\Exception $e){

            throw new \Exception('SESSION ERROR...');
        }

        if($code == $sessionCode){

            unset($_SESSION['tCaptcha']);

            return true;
        }

        return false;

    }
}