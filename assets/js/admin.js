(function (window, document, $) {

    var CarbonApi;

    var SwitchersTypesEnum = Object.freeze({
        "INLINE": "inline",
        "DROPDOWN": "dropdown",
        "SELECT": "select"
    });

    var FlagsEnum = Object.freeze({
        "ROUNDED": "rounded",
        "CIRCLE": "circle",
        "SQUARE": "square",
        "RECTANGLE": "rectangle"
    });

    /**
     * @constructor
     * @private
     */
    var YaglotSwitcher = function (data, languages) {

        this.languages = languages;

        this.type = [
            SwitchersTypesEnum.DROPDOWN,
            SwitchersTypesEnum.INLINE,
            SwitchersTypesEnum.SELECT
        ].indexOf(data.type) > -1
            ? data.type
            : SwitchersTypesEnum.DROPDOWN;

        this.flags = [
            FlagsEnum.SQUARE,
            FlagsEnum.CIRCLE,
            FlagsEnum.RECTANGLE,
            FlagsEnum.ROUNDED
        ].indexOf(data.flags) > -1
            ? data.flags
            : FlagsEnum.ROUNDED;

        this.id = data.id;
        this.target = data.target;
        this.sibling = data.sibling;
        this.style = data.style;
        this.class = typeof data.class === "string" ? data.class : null;
        this.css = typeof data.css === "string" ? data.css.trim() : null;
        this.hideTitle = typeof data.hideTitle === "boolean" ? data.hideTitle : false;
        this.hideFlag = typeof data.hideFlag === "boolean" ? data.hideFlag : false;
        this.shortTitle = typeof data.shortTitle === "boolean" ? data.shortTitle : false;
        this.defaultStyles = typeof data.defaultStyles === "boolean" ? data.defaultStyles : true;

        this.create(languages[0]);
    };

    YaglotSwitcher.prototype.onDropdownOpen = function (target) {

        var height = window.innerHeight,
            top = this.getOffset(target.parentNode).top - window.scrollY,
            position = window.getComputedStyle(target.parentNode).getPropertyValue("position"),
            bottom = window.getComputedStyle(target.parentNode).getPropertyValue("bottom");

        if (top > height / 2 || position === "fixed" && bottom !== "auto") {
            target.classList.add(this.options.switcherClass + "-open-up")
        } else {
            target.classList.remove(this.options.switcherClass + "-open-up");
        }

        target.classList.toggle("closed");
    };


    YaglotSwitcher.prototype.getOffset = function (element) {

        var top = 0,
            left = 0;

        do {
            top += element.offsetTop || 0;
            left += element.offsetLeft || 0;
            element = element.offsetParent;
        } while (element);

        return {
            top: top,
            left: left
        }
    };


    YaglotSwitcher.prototype.create = function (targetLanguage) {

        if (!this.target) {
            return;
        }

        this.container = this.createContainer();
        this.element = this.createElement(targetLanguage);
        this.container.appendChild(this.element);

        this.assignCustomStyles();
        this.assignCustomClass();
        this.appendCustomCss();

        this.insertContainer();

        if (this.type === SwitchersTypesEnum.DROPDOWN) {
            this.setDropdownSwitcherWidth();
        }
    };


    YaglotSwitcher.prototype.createContainer = function () {

        var container = document.createElement('div');
        container.classList.add(this.options.mainClass);
        container.classList.add(this.options.switcherClass + '-container');

        return container;
    };


    YaglotSwitcher.prototype.createElement = function (targetLanguage) {

        var element;

        switch (this.type) {

            case SwitchersTypesEnum.SELECT:
                element = this.createSelectSwitcher(targetLanguage);
                break;

            case SwitchersTypesEnum.INLINE:
                element = this.createInlineSwitcher(targetLanguage);
                break;

            default:
                element = this.createDropdownSwitcher(targetLanguage);
        }

        if (this.hideFlag) {
            element.classList.add(this.options.switcherClass + "-hide-flag");
        }

        if (this.hideTitle) {
            element.classList.add(this.options.switcherClass + "-hide-title");
        }

        if(!this.defaultStyles) {
            element.classList.add(this.options.switcherClass + "-no-styles");
        }

        element.classList.add(this.options.switcherClass + "-" + this.id);

        return element;
    };


    YaglotSwitcher.prototype.insertContainer = function () {
        this.append(this.target, this.container);
    };


    YaglotSwitcher.prototype.append = function (target, element) {
        target.appendChild(element);
    };

    YaglotSwitcher.prototype.setDropdownSwitcherWidth = function () {

        var dropdownWidth = this.element.querySelector("ul").offsetWidth,
            switcherWidth = this.element.offsetWidth;

        if (switcherWidth < dropdownWidth) {
            this.element.style.minWidth = dropdownWidth + "px";
        } else {
            this.element.style.minWidth = switcherWidth + "px";
        }
    };


    YaglotSwitcher.prototype.createSelectSwitcher = function (targetLanguage) {

        var self = this;

        var switcher = document.createElement("select");
        switcher.classList.add(this.options.mainClass);
        switcher.classList.add(this.options.switcherClass);
        switcher.classList.add(this.options.switcherClass + "-" + SwitchersTypesEnum.SELECT);
        switcher.onchange = function (e) {

            e.preventDefault();

            return false;
        };

        for (var i = 0; i < this.languages.length; i++) {

            var language = this.languages[i],
                option = document.createElement("option");

            option.className = this.options.mainClass;
            option.value = language.code;
            option.text = !this.shortTitle ? language.title : language.code.toUpperCase();

            option.setAttribute('data-href', this.createLanguageLink(language));
            option.setAttribute('name', language.code);

            if (language.code === targetLanguage.code) {
                option.selected = true;
            }

            switcher.appendChild(option)
        }

        return switcher;
    };


    YaglotSwitcher.prototype.createInlineSwitcher = function (targetLanguage) {

        var self = this;

        var switcher = document.createElement("aside");
        switcher.classList.add(this.options.mainClass);
        switcher.classList.add(this.options.switcherClass);
        switcher.classList.add(this.options.switcherClass + "-" + SwitchersTypesEnum.INLINE);
        switcher.classList.add(this.options.switcherClass + "-flags-" + this.flags);

        var ul = document.createElement("ul");

        for (var i = 0; i < this.languages.length; i++) {

            var language = this.languages[i];

            var li = document.createElement("li");
            li.className = language.code;
            li.setAttribute("data-l", language.code);
            li.onclick = function (e) {

                e.preventDefault();

                return false;
            };


            li.appendChild(this.createLanguageLink(language));

            if (language.code === targetLanguage.code) {
                li.classList.add('active');
            }

            ul.appendChild(li)
        }

        switcher.appendChild(ul);

        return switcher;
    };


    YaglotSwitcher.prototype.createDropdownSwitcher = function (targetLanguage) {

        var self = this;

        var switcher = document.createElement("aside");
        switcher.classList.add(this.options.mainClass);
        switcher.classList.add(this.options.switcherClass);
        switcher.classList.add(this.options.switcherClass + "-" + SwitchersTypesEnum.DROPDOWN);
        switcher.classList.add("closed");
        switcher.classList.add(this.options.switcherClass + "-flags-" + this.flags);
        switcher.onclick = function (e) {

            e.preventDefault();

            self.onDropdownOpen(e.currentTarget);

            return false;
        };

        var currentLang = document.createElement("div");
        currentLang.className = "active " + targetLanguage.code;
        currentLang.setAttribute("data-l", targetLanguage.code);
        currentLang.appendChild(this.createLanguageLink(targetLanguage));

        var ul = document.createElement("ul");

        for (var i = 0; i < this.languages.length; i++) {

            var language = this.languages[i];

            if (language.code === targetLanguage.code) {
                continue;
            }

            var li = document.createElement("li");
            li.className = language.code;
            li.setAttribute("data-l", language.code);
            li.onclick = function (e) {

                e.preventDefault();

                return false;
            };

            li.appendChild(this.createLanguageLink(language));
            ul.appendChild(li)
        }

        switcher.appendChild(currentLang);
        switcher.appendChild(ul);

        return switcher;
    };


    YaglotSwitcher.prototype.createLanguageLink = function (language) {

        var a = document.createElement("a");
        a.className = this.options.mainClass;
        a.title = language.title;
        a.textContent = !this.shortTitle ? language.title : language.code.toUpperCase();
        a.href = "#";

        return a;
    };


    YaglotSwitcher.prototype.assignCustomStyles = function () {

        if (typeof this.style === "string") {
            this.element.setAttribute("style", this.style);
        }

        if (typeof this.style === "object") {

            for (var property in this.style) {

                if (!this.style.hasOwnProperty(property)) {
                    continue;
                }

                if (!(typeof this.style[property] === "string" || !isNaN(this.style[property]))) {
                    continue;
                }

                this.element.style[property] = this.style[property];
            }
        }
    };


    YaglotSwitcher.prototype.assignCustomClass = function () {

        if (!this.class) {
            return;
        }

        this.element.className += " " + this.class;
    };


    YaglotSwitcher.prototype.appendCustomCss = function () {

        if (!this.css) {
            return;
        }

        var style = document.createElement('style'),
            css = this.css.replace(/[{]{2}[\s]*id[\s]*[}]{2}/g, "." + this.options.mainClass + '.' + this.options.switcherClass + '.' + this.options.switcherClass + '-' + this.id);

        style.type = 'text/css';

        if (style.styleSheet) {
            style.styleSheet.cssText = css;
        } else {
            style.appendChild(document.createTextNode(css));
        }

        this.target.appendChild(style);
    };


    YaglotSwitcher.prototype.options = {
        'switcherEvents': true,
        'switcherClass': 'yg-sw',
        'mainClass': 'yg'
    };


    $(document).on('carbonFields.apiLoaded', function (e, api) {

        CarbonApi = api;

        renderSwitchers();
        renderShortcodes();
        onTargetLanguagesChange();
        highlightUsedLanguages();
        disableSortable();
        setCollapseEvents();
    });

    $(document).on('carbonFields.fieldUpdated', function (e, fieldName, v) {

        clearFieldsErrors();

        if (fieldName === 'yg_target_languages') {
            onTargetLanguagesChange();
            highlightUsedLanguages();
        }

        if (fieldName === 'yg_original_language') {
            onOriginalLanguageChange();
            highlightUsedLanguages();
        }

        if(fieldName === 'yg_switcher_selectors') {
            onSwitcherSelectorsChange();
        }

        renderSwitchers();
        renderShortcodes();
    });


    $(document).on('click', '.close-error', function () {
        clearError($(this).parents('.carbon-field'));
    });

    $(document).on('click', '#publish', clearFieldsErrors);

    function disableSortable() {
        $('.carbon-groups-holder').sortable( "disable" );
    }

    function setCollapseEvents() {
        $(document).on('click', '.carbon-drag-handle', function(){
           $(this).parent().toggleClass('collapsed');
        });
    }

    function showUsedLanguages() {

        if( ! window.YaglotData.projectKeyInfo) {
            return;
        }

        var targetLanguages = CarbonApi.getFieldValue('yg_target_languages');
        if (window.YaglotData.projectKeyInfo.plan.limit_languages === -1) {
            return;
        }

        if (targetLanguages.length <= window.YaglotData.projectKeyInfo.plan.limit_languages) {
            return;
        }

        var $field = $('[name="_yg_target_languages"]');
        if( ! $field.length ) {
            return;
        }

        var $wrapper = $field.parents('.carbon-field');
        if($wrapper.find('.yaglot-languages-pairs').length) {
            return;
        }

        $wrapper.append("<div class='yaglot-languages-pairs'>"
            + "<label>List of language pairs already used in translations:</label>"
            + "<ol>"
            + window.YaglotData.projectKeyInfo.languages.map(function(pair){
                return "<li>" + window.YaglotData.languagesList[pair.from] + " > " + window.YaglotData.languagesList[pair.to] + "</li>";
            }).join('')
            + "</ol>"
            + "<p class='carbon-help-text'>In order to remove unused pairs, go to your personal account.</p>"
            + "<p class='carbon-help-text'>We highly recommend you to choose <a href='" + window.YaglotData.accountBillingUrl + "' target='_blank' rel='noopener noreferrer'>a more appropriate plan</a>.</p>"
            + "</div>");

    }

    function highlightUsedLanguages() {

        if( ! window.YaglotData.projectKeyInfo) {
            return;
        }

        var targetLanguages = CarbonApi.getFieldValue('yg_target_languages'),
            originalLanguage = CarbonApi.getFieldValue('yg_original_language');

        var $field = $('[name="_yg_target_languages"]');
        if( ! $field.length ) {
            return;
        }

        var $wrapper = $field.parents('.carbon-field');

        setTimeout(function () {
            targetLanguages.map(function(code, i){

                if( !window.YaglotData.projectKeyInfo.languages.filter(function(pair){
                    return code === pair.to && originalLanguage === pair.from;
                }).length ) {
                    return;
                }

                $wrapper.find('#react-select-2--value-' + i)
                    .parents('.Select-value')
                    .addClass('yaglot-language-active');
            });
        }, 0);
    }

    function setFieldError(field, error) {

        var $field = $('[name="_' + field + '"]');
        if( ! $field.length ) {
            return;
        }

        var $wrapper = $field.parents('.carbon-field');

        $wrapper.addClass('carbon-highlight');
        $wrapper.append("<em class=\"carbon-error\">" + error + " <span class=\"close-error\">×</span></em>");
    }

    function clearFieldsErrors() {

        $('.carbon-highlight').each(function () {
            clearError($(this))
        });
    }

    function clearError($wrapper) {

        $wrapper.removeClass('carbon-highlight');

        $wrapper.find('carbon-error')
            .remove();
    }

    function onSwitcherSelectorsChange() {

        var switcherSelectors = CarbonApi.getFieldValue('yg_switcher_selectors');
        switcherSelectors.map(function (switcher, i) {

            if(switcher.switcher_id) {
                return;
            }

            CarbonApi.setFieldValue('yg_switcher_selectors[' + i +']/switcher_id', Math.floor(Math.random() * (9999999 - 1 + 1)) + 1);
        });

    }

    function onTargetLanguagesChange() {

        var originalLanguage = CarbonApi.getFieldValue('yg_original_language'),
            targetLanguages = CarbonApi.getFieldValue('yg_target_languages'),
            index = targetLanguages.indexOf(originalLanguage);

        if (index !== -1) {

            targetLanguages.splice(index, 1);

            CarbonApi.setFieldValue('yg_target_languages', targetLanguages);

            return;
        }

        if (window.YaglotData.projectKeyInfo
            && window.YaglotData.projectKeyInfo.plan.limit_languages > -1) {

            if (targetLanguages.length <= window.YaglotData.projectKeyInfo.plan.limit_languages) {
                return;
            }

            setFieldError('yg_target_languages', 'Max ' + window.YaglotData.projectKeyInfo.plan.limit_languages + ' languages allowed in your current tariff plan');
            showUsedLanguages();
        }
    }

    function onOriginalLanguageChange() {

        $('.yaglot-language-active').removeClass('yaglot-language-active');

        var originalLanguage = CarbonApi.getFieldValue('yg_original_language'),
            targetLanguages = CarbonApi.getFieldValue('yg_target_languages'),
            index = targetLanguages.indexOf(originalLanguage);

        if (index === -1) {
            return;
        }

        targetLanguages.splice(index, 1);

        CarbonApi.setFieldValue('yg_target_languages', targetLanguages);
    }

    function renderShortcodes() {

        setTimeout(function () {

            CarbonApi.getFieldValue('yg_switcher_selectors').map(function (switcher, i) {

                var $input = $('input[name="_yg_switcher_selectors[' + i + '][value]"]'),
                    $group = $input.parents('.carbon-group-row'),
                    $element = $group.find('.switcher-shortcode');

                if (!$element.length) {
                    return;
                }

                $element.val('[yaglot_switcher id="' + switcher.switcher_id + '"]');
            });

        }, 0);

    }

    function renderSwitchers() {

        var languages = [];

        [CarbonApi.getFieldValue('yg_original_language')]
            .concat(CarbonApi.getFieldValue('yg_target_languages'))
            .map(function (code) {

                if (languages.filter(function (language) {
                    return language.code === code;
                }).length) {
                    return;
                }

                languages.push({
                    'title': window.YaglotData.languagesList[code],
                    'code': code
                })
            });

        setTimeout(function () {

            CarbonApi.getFieldValue('yg_switcher_selectors').map(function (switcher, i) {

                var $input = $('input[name="_yg_switcher_selectors[' + i + '][value]"]'),
                    $group = $input.parents('.carbon-group-row'),
                    $element = $group.find('.switcher-preview-container');


                if (!$element.length) {
                    return;
                }

                $element.html('');

                new YaglotSwitcher({
                    'id': switcher.switcher_id,
                    'target': $element.get(0),
                    'type': switcher.type,
                    'flags': switcher.flags,
                    'hideFlag': switcher.switcher_data === 'titles',
                    'hideTitle': switcher.switcher_data === 'flags',
                    'shortTitle': switcher.short_title,
                    'defaultStyles': switcher.default_styles,
                    'css': switcher.css,
                    'class': switcher.class
                }, languages);

            });

        }, 0);


    }

})(window, document, jQuery);