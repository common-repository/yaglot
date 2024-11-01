<?php

namespace Yaglot\Client\Api\Enum;

/**
 * Enum BotType
 * Used to define which bot is parsing the page.
 * Basically, most of time we recommend to use as "human"
 *
 * @package Yaglot\Client\Api\Enum
 */
abstract class BotType
{
    const HUMAN = 0;
    const OTHER = 1;
    const GOOGLE = 2;
    const BING = 3;
    const YAHOO = 4;
    const BAIDU = 5;
    const YANDEX = 6;
}
