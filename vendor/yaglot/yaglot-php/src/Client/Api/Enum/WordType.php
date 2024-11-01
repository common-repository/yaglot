<?php

namespace Yaglot\Client\Api\Enum;

/**
 * Enum WordType
 * Used to define where was the text we are parsing
 *
 * @package Yaglot\Client\Api\Enum
 */
abstract class WordType
{
//    const OTHER = 0;
//    const TEXT = 1;
//    const VALUE = 2;
//    const PLACEHOLDER = 3;
//    const META_CONTENT = 4;
//    const IFRAME_SRC = 5;
//    const IMG_SRC = 6;
//    const IMG_ALT = 7;
//    const PDF_HREF = 8;

    const TYPE_TEXT = 0;
    const TYPE_IMAGE = 1;
    const TYPE_VIDEO = 2;
    const TYPE_AUDIO = 3;
    const TYPE_PLACEHOLDER = 4;
    const TYPE_BUTTON = 5;
    const TYPE_META = 6;

    const TYPE_IFRAME_SRC = 7;
    const TYPE_IMG_ALT = 8;
    const TYPE_PDF_HREF = 9;

    /**
     * Only for internal use, if you have to add a value in this enum,
     * please increments the __MAX value.
     */
    const __MIN = 0;
    const __MAX = 9;
}
