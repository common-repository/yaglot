<?php

namespace Yaglot\Client\Api;

use Yaglot\Client\Api\Shared\AbstractCollectionEntry;

/**
 * Class LanguageEntry
 * @package Yaglot\Client\Api
 */
class LanguageEntry extends AbstractCollectionEntry
{
    /**
     * ISO 639-1 code to identify language [en]
     *
     * @see https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes
     * @var string
     */
    protected $iso_639_1;

    /**
     * English name of the language
     *
     * @var string
     */
    protected $englishName;

    /**
     * Name of the language in the language
     *
     * @var string
     */
    protected $localName;

    /**
     * Name of the language in both variants [<English> (<Local>)]
     *
     * @var string
     */
    protected $fullName;

    /**
     * Date Time format of the language [Y-m-d H:i:s]
     *
     * @var string
     */
    protected $dateTimeFormat;

    /**
     * Date format of the language [Y-m-d]
     *
     * @var string
     */
    protected $dateFormat;

    /**
     * Language is right to left
     *
     * @var bool
     */
    protected $isRtl;

    /**
     * Language code [en-us]
     *
     * @see https://en.wikipedia.org/wiki/Language_localisation
     * @var bool
     */
    protected $languageCode;

    /**
     * Language locale code [en-US]
     *
     * @see https://en.wikipedia.org/wiki/Language_localisation
     * @var bool
     */
    protected $localeCode;

    /**
     * Language flag
     *
     * @var bool
     */
    protected $flag;

    /**
     * LanguageEntry constructor.
     * @param string $iso_639_1 ISO 639-1 code to identify language
     * @param string $englishName English name of the language
     * @param string $localName Name of the language in the language
     * @param string $fullName Name of the language in both variants [<English> (<Local>)]
     * @param string $dateTimeFormat Date Time format of the language [Y-m-d H:i:s]
     * @param string $dateFormat Date format of the language [Y-m-d]
     * @param bool $isRtl Language is right to left
     * @param string $languageCode Language code [en-us]
     * @param string $localeCode Language locale code [en-US]
     * @param string $flag Language flag
     */
    public function __construct(
        $iso_639_1,
        $englishName,
        $localName,
        $fullName,
        $dateTimeFormat,
        $dateFormat,
        $isRtl,
        $languageCode,
        $localeCode,
        $flag
    )
    {
        $this
            ->setIso639($iso_639_1)
            ->setEnglishName($englishName)
            ->setLocalName($localName)
            ->setFullName($fullName)
            ->setDateTimeFormat($dateTimeFormat)
            ->setDateFormat($dateFormat)
            ->setRtl($isRtl)
            ->setLanguageCode($languageCode)
            ->setLocaleCode($localeCode)
            ->setFlag($flag);
    }

    /**
     * @param $iso_639_1
     * @return $this
     */
    public function setIso639($iso_639_1)
    {
        $this->iso_639_1 = $iso_639_1;

        return $this;
    }

    /**
     * @return string
     */
    public function getIso639()
    {
        return $this->iso_639_1;
    }

    /**
     * @param string $englishName
     * @return $this
     */
    public function setEnglishName($englishName)
    {
        $this->englishName = $englishName;

        return $this;
    }

    /**
     * @return string
     */
    public function getEnglishName()
    {
        return $this->englishName;
    }

    /**
     * @param $localName
     * @return $this
     */
    public function setLocalName($localName)
    {
        $this->localName = $localName;

        return $this;
    }

    /**
     * @return string
     */
    public function getLocalName()
    {
        return $this->localName;
    }

    /**
     * @param $fullName
     * @return $this
     */
    public function setFullName($fullName)
    {
        $this->fullName = $fullName;

        return $this;
    }

    /**
     * @return string
     */
    public function getFullName()
    {
        return $this->fullName;
    }

    /**
     * @param $dateTimeFormat
     * @return $this
     */
    public function setDateTimeFormat($dateTimeFormat)
    {
        $this->dateTimeFormat = $dateTimeFormat;

        return $this;
    }

    /**
     * @return string
     */
    public function getDateTimeFormat()
    {
        return $this->dateTimeFormat;
    }

    /**
     * @param $dateFormat
     * @return $this
     */
    public function setDateFormat($dateFormat)
    {
        $this->dateFormat = $dateFormat;

        return $this;
    }

    /**
     * @return string
     */
    public function getDateFormat()
    {
        return $this->dateFormat;
    }

    /**
     * @param bool $rtl
     * @return $this
     */
    public function setRtl($rtl)
    {
        $this->isRtl = (bool)$rtl;

        return $this;
    }

    /**
     * @return bool
     */
    public function isRtl()
    {
        return $this->isRtl;
    }

    /**
     * @param string $languageCode
     * @return $this
     */
    public function setLanguageCode($languageCode)
    {
        $this->languageCode = $languageCode;

        return $this;
    }

    /**
     * @return bool
     */
    public function getLanguageCode()
    {
        return $this->languageCode;
    }

    /**
     * @param string $localeCode
     * @return $this
     */
    public function setLocaleCode($localeCode)
    {
        $this->localeCode = $localeCode;

        return $this;
    }

    /**
     * @return bool
     */
    public function getLocaleCode()
    {
        return $this->localeCode;
    }

    /**
     * @param string $flag
     * @return $this
     */
    public function setFlag($flag)
    {
        $this->flag = $flag;

        return $this;
    }

    /**
     * @return bool
     */
    public function getFlag()
    {
        return $this->flag;
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return [
            'code'      => $this->getIso639(),
            'english'   => $this->getEnglishName(),
            'local'     => $this->getLocalName(),
            'original'     => $this->getLocalName(),
            'fullName'  => $this->getFullName(),
            'dateTimeFormat' => $this->getDateTimeFormat(),
            'dateFormat' => $this->getDateFormat(),
            'rtl'       => $this->isRtl(),
            'langCode'       => $this->getLanguageCode(),
            'localeCode'       => $this->getLocaleCode(),
            'flag'       => 'vendor/yaglot/yaglot-php/' . $this->getFlag(),
        ];
    }
}
