<?php

namespace App\Http\Controllers\Api;

use Gregwar\Captcha\CaptchaBuilder;
use Gregwar\Captcha\PhraseBuilder;
use Illuminate\Http\Request;

class CaptchaController extends Controller
{
    public function makeCaptcha()
    {
        $config=config('captcha');
        $key = 'captcha_'.str_random(config('captcha.key_length'));
        $phraseBuilder = new PhraseBuilder(config('captcha.default.length'));
        $captchaBuilder=new CaptchaBuilder(null,$phraseBuilder);
        $captcha = $captchaBuilder->build(config('captcha.default.width'),config('captcha.default.height'));
        $expiredAt = now()->addMinutes(config('captcha.timeout'));
        cache([$key=>[ 'captcha_code' => $captcha->getPhrase()]], $expiredAt);
        $result = [
            'captcha_key' => $key,
            'expired_at' => $expiredAt->toDateTimeString(),
            'captcha_image_content' => $captcha->inline()
        ];
        return $this->response->array($result)->setStatusCode(200);
    }
}
