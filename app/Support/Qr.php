<?php

namespace App\Support;

use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

class Qr
{
    public static function svg($text)
    {
        $options = new QROptions([
            'outputType' => QRCode::OUTPUT_MARKUP_SVG,
            'imageBase64' => false,
            'addQuietzone' => true,
            'quietzoneSize' => 2,
        ]);

        return (new QRCode($options))->render($text);
    }
}
