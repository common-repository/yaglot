<?php

namespace Yaglot\Client\Factory;

use Yaglot\Client\Api\LanguageEntry;

/**
 * Class Languages
 * @package Yaglot\Client\Factory
 */
class Languages
{
    /**
     * @var array
     */
    protected $language;

    /**
     * Languages constructor.
     * @param array $language
     */
    public function __construct(array $language)
    {
        $this->language = $language;
    }

    /**
     * @param array $language
     * @return $this
     */
    public function setLanguage(array $language)
    {
        $this->language = $language;

        return $this;
    }

    /**
     * @param null $key
     * @return array|string|bool
     */
    public function getLanguage($key = null)
    {
        if ($key !== null && isset($this->language[$key])) {
            return $this->language[$key];
        }
        return $this->language;
    }

    /**
     * @return LanguageEntry
     */
    public function handle()
    {
        $language = new LanguageEntry(
            $this->getLanguage('code'),
            $this->getLanguage('english'),
            $this->getLanguage('local'),
            $this->getLanguage('fullName'),
            $this->getLanguage('dateTimeFormat'),
            $this->getLanguage('dateFormat'),
            $this->getLanguage('rtl'),
            $this->getLanguage('langCode'),
            $this->getLanguage('localeCode'),
            $this->getLanguage('flag')
        );
        return $language;
    }

    /**
     * @return array
     */
    public static function data()
    {
        $langListFile = implode(DIRECTORY_SEPARATOR, [
            rtrim(__DIR__, DIRECTORY_SEPARATOR),
            '..',
            '..',
            '..',
            'data',
            'lang',
            'lang.json'
        ]);

        if(file_exists($langListFile)) {
            return array_map(
                function($lang) {
                    return [
                        'code' => $lang['iso_code'],
                        'english' => $lang['name'],
                        'local' => $lang['original'],
                        'fullName' => $lang['full_name'],
                        'dateTimeFormat' => $lang['date_format_full'],
                        'dateFormat' => $lang['date_format_lite'],
                        'rtl' => $lang['is_rtl'] === '1',
                        'langCode' => $lang['language_code'],
                        'localeCode' => $lang['locale'],
                        'flag' => $lang['flag'],
                    ];
                },
                json_decode(file_get_contents($langListFile), true)
            );

        }

        return [];
    }
}
