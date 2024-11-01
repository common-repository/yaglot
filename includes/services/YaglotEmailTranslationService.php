<?php

namespace Yaglot\Services;

if (!defined('ABSPATH')) {
    exit;
}

class YaglotEmailTranslationService {

    /**
     * @var YaglotParserService
     */
    private $parserService;


    /**
     * @var string
     */
    private $originalLanguage;


    /**
     * YaglotEmailTranslationService constructor.
     * @param YaglotParserService $parserService
     * @param string $originalLanguage
     */
    public function __construct(YaglotParserService $parserService, $originalLanguage) {
        $this->parserService = $parserService;
        $this->originalLanguage = $originalLanguage;
    }

    /**
     * Translate email with parser
     *
     * @param array $args
     * @param string $language
     * @return array $args
     */
    public function translate(array $args, $language) {

        $parser = $this->parserService->getParser();

        try {
            $translated_subject = $parser->translate('<p>' . $args['subject'] . '</p>', $this->originalLanguage, $language);
        } catch (\Exception $e) {
            $translated_subject = '<p>' . $args['subject'] . '</p>';
        }

        try {
            $translated_message = $parser->translate($args['message'], $this->originalLanguage, $language);
        } catch (\Exception $e) {
            $translated_message = $args['message'];
        }

        return [
            'subject' => $translated_subject,
            'message' => $translated_message,
        ];
    }

}