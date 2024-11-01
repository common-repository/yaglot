<?php

require_once yaglot_path(['vendor', 'autoload.php']);

require_once yaglot_path(['carbon', 'containers', 'Yaglot_Admin_Page_Container.php']);
require_once yaglot_path(['carbon', 'Yaglot_Theme_Options_Datastore.php']);
require_once yaglot_path(['carbon', 'Yaglot_Key_Toolset.php']);

require_once yaglot_path(['includes', 'YaglotTranslate.php']);

require_once yaglot_path(['includes', 'widgets', 'YaglotSwitcherWidget.php']);
require_once yaglot_path(['includes', 'exceptions', 'ServerErrorException.php']);

require_once yaglot_path(['includes', 'services', 'YaglotLanguagesService.php']);
require_once yaglot_path(['includes', 'services', 'YaglotProjectService.php']);
require_once yaglot_path(['includes', 'services', 'YaglotOptionsService.php']);
require_once yaglot_path(['includes', 'services', 'YaglotPageTranslationService.php']);
require_once yaglot_path(['includes', 'services', 'YaglotRedirectService.php']);
require_once yaglot_path(['includes', 'services', 'YaglotRequestUrlService.php']);
require_once yaglot_path(['includes', 'services', 'YaglotParserService.php']);
require_once yaglot_path(['includes', 'services', 'YaglotReplaceLinkService.php']);
require_once yaglot_path(['includes', 'services', 'YaglotReplaceUrlService.php']);
require_once yaglot_path(['includes', 'services', 'YaglotMultisiteService.php']);
require_once yaglot_path(['includes', 'services', 'YaglotSwitchersService.php']);
require_once yaglot_path(['includes', 'services', 'YaglotShortcodesService.php']);
require_once yaglot_path(['includes', 'services', 'YaglotEmailTranslationService.php']);
require_once yaglot_path(['includes', 'services', 'YaglotMenuService.php']);
require_once yaglot_path(['includes', 'services', 'YaglotIntegrationsService.php']);

require_once yaglot_path(['includes', 'helpers', 'ServerHelper.php']);
require_once yaglot_path(['includes', 'helpers', 'TextHelper.php']);
require_once yaglot_path(['includes', 'helpers', 'UrlHelper.php']);
require_once yaglot_path(['includes', 'helpers', 'UrlConfigHelper.php']);
require_once yaglot_path(['includes', 'helpers', 'UrlTranslateHelper.php']);
require_once yaglot_path(['includes', 'helpers', 'JsonInlineHelper.php']);
require_once yaglot_path(['includes', 'helpers', 'UrlFilterHelper.php']);

require_once yaglot_path(['includes', 'entities', 'YaglotKeyInfo.php']);
require_once yaglot_path(['includes', 'entities', 'YaglotAccountEntity.php']);
require_once yaglot_path(['includes', 'entities', 'YaglotPlanEntity.php']);
require_once yaglot_path(['includes', 'entities', 'YaglotProjectEntity.php']);
require_once yaglot_path(['includes', 'entities', 'YaglotProjectUsageEntity.php']);
require_once yaglot_path(['includes', 'entities', 'YaglotProjectLanguageEntity.php']);

require_once yaglot_path(['includes', 'admin', 'YaglotAdmin.php']);
require_once yaglot_path(['includes', 'admin', 'tabs', 'YaglotAdminSettings.php']);
require_once yaglot_path(['includes', 'admin', 'tabs', 'YaglotAdminSwitcher.php']);
require_once yaglot_path(['includes', 'admin', 'tabs', 'YaglotAdminExclusion.php']);
require_once yaglot_path(['includes', 'admin', 'tabs', 'YaglotAdminOther.php']);