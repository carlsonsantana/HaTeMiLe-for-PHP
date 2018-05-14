<?php
/**
 * Class AccessibleDisplayScreenReaderImplementation.
 * 
 * @package hatemile\implementation
 * @author Carlson Santana Cruz
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache License 2.0
 * @copyright (c) 2018, HaTeMiLe
 */

namespace hatemile\implementation;

require_once join(DIRECTORY_SEPARATOR, array(
    dirname(dirname(__FILE__)),
    'AccessibleDisplay.php'
));
require_once join(DIRECTORY_SEPARATOR, array(
    dirname(dirname(__FILE__)),
    'util',
    'CommonFunctions.php'
));
require_once join(DIRECTORY_SEPARATOR, array(
    dirname(dirname(__FILE__)),
    'util',
    'Configure.php'
));
require_once join(DIRECTORY_SEPARATOR, array(
    dirname(dirname(__FILE__)),
    'util',
    'IDGenerator.php'
));
require_once join(DIRECTORY_SEPARATOR, array(
    dirname(dirname(__FILE__)),
    'util',
    'html',
    'HTMLDOMElement.php'
));
require_once join(DIRECTORY_SEPARATOR, array(
    dirname(dirname(__FILE__)),
    'util',
    'html',
    'HTMLDOMParser.php'
));

use \hatemile\AccessibleDisplay;
use \hatemile\util\CommonFunctions;
use \hatemile\util\Configure;
use \hatemile\util\IDGenerator;
use \hatemile\util\html\HTMLDOMElement;
use \hatemile\util\html\HTMLDOMParser;

/**
 * The AccessibleDisplayScreenReaderImplementation class is official
 * implementation of AccessibleDisplay for screen readers.
 */
class AccessibleDisplayScreenReaderImplementation implements AccessibleDisplay
{

    /**
     * The id of list element that contains the description of shortcuts, before
     * the whole content of page.
     * @var string
     */
    const ID_CONTAINER_SHORTCUTS_BEFORE = 'container-shortcuts-before';

    /**
     * The id of list element that contains the description of shortcuts, after
     * the whole content of page.
     * @var string
     */
    const ID_CONTAINER_SHORTCUTS_AFTER = 'container-shortcuts-after';

    /**
     * The HTML class of text of description of container shortcuts.
     * @var string
     */
    const CLASS_TEXT_SHORTCUTS = 'text-shortcuts';

    /**
     * The HTML class of content to force the screen reader show the current
     * state of element, before it.
     * @var string
     */
    const CLASS_FORCE_READ_BEFORE = 'force-read-before';

    /**
     * The HTML class of content to force the screen reader show the current
     * state of element, after it.
     * @var string
     */
    const CLASS_FORCE_READ_AFTER = 'force-read-after';

    /**
     * The name of attribute that links the description of shortcut of element.
     * @var string
     */
    const DATA_ATTRIBUTE_ACCESSKEY_OF = 'data-attributeaccesskeyof';

    /**
     * The name of attribute that links the content of download link.
     * @var string
     */
    const DATA_ATTRIBUTE_DOWNLOAD_OF = 'data-attributedownloadof';

    /**
     * The name of attribute that links the content of header cell with the
     * data cell.
     * @var string
     */
    const DATA_ATTRIBUTE_HEADERS_OF = 'data-headersof';

    /**
     * The name of attribute that links the description of language with the
     * element.
     * @var string
     */
    const DATA_ATTRIBUTE_LANGUAGE_OF = 'data-languageof';

    /**
     * The name of attribute that links the content of link that open a new
     * instance.
     * @var string
     */
    const DATA_ATTRIBUTE_TARGET_OF = 'data-attributetargetof';

    /**
     * The name of attribute that links the content of title of element.
     * @var string
     */
    const DATA_ATTRIBUTE_TITLE_OF = 'data-attributetitleof';

    /**
     * The name of attribute that links the content of autocomplete state of
     * field.
     * @var string
     */
    const DATA_ARIA_AUTOCOMPLETE_OF = 'data-ariaautocompleteof';

    /**
     * The name of attribute that links the content of busy state of element.
     * @var string
     */
    const DATA_ARIA_BUSY_OF = 'data-ariabusyof';

    /**
     * The name of attribute that links the content of checked state field.
     * @var string
     */
    const DATA_ARIA_CHECKED_OF = 'data-ariacheckedof';

    /**
     * The name of attribute that links the content of drop effect state of
     * element.
     * @var string
     */
    const DATA_ARIA_DROPEFFECT_OF = 'data-ariadropeffectof';

    /**
     * The name of attribute that links the content of expanded state of
     * element.
     * @var string
     */
    const DATA_ARIA_EXPANDED_OF = 'data-ariaexpandedof';

    /**
     * The name of attribute that links the content of grabbed state of element.
     * @var string
     */
    const DATA_ARIA_GRABBED_OF = 'data-ariagrabbedof';

    /**
     * The name of attribute that links the content that show if the field has
     * popup.
     * @var string
     */
    const DATA_ARIA_HASPOPUP_OF = 'data-ariahaspopupof';

    /**
     * The name of attribute that links the content of level state of element.
     * @var string
     */
    const DATA_ARIA_LEVEL_OF = 'data-arialevelof';

    /**
     * The name of attribute that links the content of orientation state of
     * element.
     * @var string
     */
    const DATA_ARIA_ORIENTATION_OF = 'data-ariaorientationof';

    /**
     * The name of attribute that links the content of pressed state of field.
     * @var string
     */
    const DATA_ARIA_PRESSED_OF = 'data-ariapressedof';

    /**
     * The name of attribute that links the content of minimum range state of
     * field.
     * @var string
     */
    const DATA_ARIA_RANGE_MIN_OF = 'data-attributevalueminof';

    /**
     * The name of attribute that links the content of maximum range state of
     * field.
     * @var string
     */
    const DATA_ARIA_RANGE_MAX_OF = 'data-attributevaluemaxof';

    /**
     * The name of attribute that links the content of required state of field.
     * @var string
     */
    const DATA_ARIA_REQUIRED_OF = 'data-attributerequiredof';

    /**
     * The name of attribute that links the content of selected state of field.
     * @var string
     */
    const DATA_ARIA_SELECTED_OF = 'data-ariaselectedof';

    /**
     * The name of attribute that links the content of sort state of element.
     * @var string
     */
    const DATA_ARIA_SORT_OF = 'data-ariasortof';

    /**
     * The name of attribute that links the content of role of element with the
     * element.
     * @var string
     */
    const DATA_ROLE_OF = 'data-roleof';

    /**
     * The HTML parser.
     * @var \hatemile\util\html\HTMLDOMParser
     */
    protected $parser;

    /**
     * The configuration of HaTeMiLe.
     * @var \hatemile\util\Configure
     */
    protected $configure;

    /**
     * The id generator.
     * @var \hatemile\util\IDGenerator
     */
    protected $idGenerator;

    /**
     * The browser shortcut prefix.
     * @var string
     */
    protected $shortcutPrefix;

    /**
     * The description of shortcut list, before all elements.
     * @var string
     */
    protected $attributeAccesskeyBefore;

    /**
     * The description of shortcut list, after all elements.
     * @var string
     */
    protected $attributeAccesskeyAfter;

    /**
     * The prefix description of shortcut list, before all elements.
     * @var string
     */
    protected $attributeAccesskeyPrefixBefore;

    /**
     * The suffix description of shortcut list, before all elements.
     * @var string
     */
    protected $attributeAccesskeySuffixBefore;

    /**
     * The prefix description of shortcut list, after all elements.
     * @var string
     */
    protected $attributeAccesskeyPrefixAfter;

    /**
     * The suffix description of shortcut list, after all elements.
     * @var string
     */
    protected $attributeAccesskeySuffixAfter;

    /**
     * The text of link that download a file, before it.
     * @var string
     */
    protected $attributeDownloadBefore;

    /**
     * The text of link that download a file, after it.
     * @var string
     */
    protected $attributeDownloadAfter;

    /**
     * The prefix text of header cell, before it content.
     * @var string
     */
    protected $attributeHeadersPrefixBefore;

    /**
     * The suffix text of header cell, before it content.
     * @var string
     */
    protected $attributeHeadersSuffixBefore;

    /**
     * The prefix text of header cell, after it content.
     * @var string
     */
    protected $attributeHeadersPrefixAfter;

    /**
     * The suffix text of header cell, after it content.
     * @var string
     */
    protected $attributeHeadersSuffixAfter;

    /**
     * The prefix text of description of language element, before it.
     * @var string
     */
    protected $attributeLanguagePrefixBefore;

    /**
     * The suffix text of description of language element, after it.
     * @var string
     */
    protected $attributeLanguageSuffixBefore;

    /**
     * The prefix text of description of language element, before it.
     * @var string
     */
    protected $attributeLanguagePrefixAfter;

    /**
     * The suffix text of description of language element, after it.
     * @var string
     */
    protected $attributeLanguageSuffixAfter;

    /**
     * The prefix text of role of element, before it.
     * @var string
     */
    protected $attributeRolePrefixBefore;

    /**
     * The suffix text of role of element, before it.
     * @var string
     */
    protected $attributeRoleSuffixBefore;

    /**
     * The prefix text of role of element, after it.
     * @var string
     */
    protected $attributeRolePrefixAfter;

    /**
     * The suffix text of role of element, after it.
     * @var string
     */
    protected $attributeRoleSuffixAfter;

    /**
     * The text of link that open new instance, before it.
     * @var string
     */
    protected $attributeTargetBlankBefore;

    /**
     * The text of link that open new instance, after it.
     * @var string
     */
    protected $attributeTargetBlankAfter;

    /**
     * The prefix text of title of element, before it.
     * @var string
     */
    protected $attributeTitlePrefixBefore;

    /**
     * The suffix text of title of element, before it.
     * @var string
     */
    protected $attributeTitleSuffixBefore;

    /**
     * The prefix text of title of element, after it.
     * @var string
     */
    protected $attributeTitlePrefixAfter;

    /**
     * The suffix text of title of element, after it.
     * @var string
     */
    protected $attributeTitleSuffixAfter;

    /**
     * The content of autocomplete inline and list state of field, before it.
     * @var string
     */
    protected $ariaAutoCompleteBothBefore;

    /**
     * The content of autocomplete inline and list state of field, after it.
     * @var string
     */
    protected $ariaAutoCompleteBothAfter;

    /**
     * The content of autocomplete inline state of field, before it.
     * @var string
     */
    protected $ariaAutoCompleteInlineBefore;

    /**
     * The content of autocomplete inline state of field, after it.
     * @var string
     */
    protected $ariaAutoCompleteInlineAfter;

    /**
     * The content of autocomplete list state of field, before it.
     * @var string
     */
    protected $ariaAutoCompleteListBefore;

    /**
     * The content of autocomplete list state of field, after it.
     * @var string
     */
    protected $ariaAutoCompleteListAfter;

    /**
     * The content of busy state of element, before it.
     * @var string
     */
    protected $ariaBusyTrueBefore;

    /**
     * The content of busy state of element, after it.
     * @var string
     */
    protected $ariaBusyTrueAfter;

    /**
     * The content of unchecked state field, before it.
     * @var string
     */
    protected $ariaCheckedFalseBefore;

    /**
     * The content of unchecked state field, after it.
     * @var string
     */
    protected $ariaCheckedFalseAfter;

    /**
     * The content of mixed checked state field, before it.
     * @var string
     */
    protected $ariaCheckedMixedBefore;

    /**
     * The content of mixed checked state field, after it.
     * @var string
     */
    protected $ariaCheckedMixedAfter;

    /**
     * The content of checked state field, before it.
     * @var string
     */
    protected $ariaCheckedTrueBefore;

    /**
     * The content of checked state field, after it.
     * @var string
     */
    protected $ariaCheckedTrueAfter;

    /**
     * The content of drop with copy effect state of element, before it.
     * @var string
     */
    protected $ariaDropeffectCopyBefore;

    /**
     * The content of drop with copy effect state of element, after it.
     * @var string
     */
    protected $ariaDropeffectCopyAfter;

    /**
     * The content of drop with execute effect state of element, before it.
     * @var string
     */
    protected $ariaDropeffectExecuteBefore;

    /**
     * The content of drop with execute effect state of element, after it.
     * @var string
     */
    protected $ariaDropeffectExecuteAfter;

    /**
     * The content of drop with link effect state of element, before it.
     * @var string
     */
    protected $ariaDropeffectLinkBefore;

    /**
     * The content of drop with link effect state of element, after it.
     * @var string
     */
    protected $ariaDropeffectLinkAfter;

    /**
     * The content of drop with move effect state of element, before it.
     * @var string
     */
    protected $ariaDropeffectMoveBefore;

    /**
     * The content of drop with move effect state of element, after it.
     * @var string
     */
    protected $ariaDropeffectMoveAfter;

    /**
     * The content of drop with popup effect state of element, before it.
     * @var string
     */
    protected $ariaDropeffectPopupBefore;

    /**
     * The content of drop with popup effect state of element, after it.
     * @var string
     */
    protected $ariaDropeffectPopupAfter;

    /**
     * The content of collapsed state of element, before it.
     * @var string
     */
    protected $ariaExpandedFalseBefore;

    /**
     * The content of collapsed state of element, after it.
     * @var string
     */
    protected $ariaExpandedFalseAfter;

    /**
     * The content of expanded state of element, before it.
     * @var string
     */
    protected $ariaExpandedTrueBefore;

    /**
     * The content of expanded state of element, after it.
     * @var string
     */
    protected $ariaExpandedTrueAfter;

    /**
     * The content of ungrabbed state of element, before it.
     * @var string
     */
    protected $ariaGrabbedFalseBefore;

    /**
     * The content of ungrabbed state of element, after it.
     * @var string
     */
    protected $ariaGrabbedFalseAfter;

    /**
     * The content of grabbed state of element, before it.
     * @var string
     */
    protected $ariaGrabbedTrueBefore;

    /**
     * The content of grabbed state of element, after it.
     * @var string
     */
    protected $ariaGrabbedTrueAfter;

    /**
     * The content that show if the field has popup, before it.
     * @var string
     */
    protected $ariaHaspopupTrueBefore;

    /**
     * The content that show if the field has popup, after it.
     * @var string
     */
    protected $ariaHaspopupTrueAfter;

    /**
     * The prefix content of level state of element, before it.
     * @var string
     */
    protected $ariaLevelPrefixBefore;

    /**
     * The suffix content of level state of element, before it.
     * @var string
     */
    protected $ariaLevelSuffixBefore;

    /**
     * The prefix content of level state of element, after it.
     * @var string
     */
    protected $ariaLevelPrefixAfter;

    /**
     * The suffix content of level state of element, after it.
     * @var string
     */
    protected $ariaLevelSuffixAfter;

    /**
     * The prefix content of maximum range state of field, before it.
     * @var string
     */
    protected $ariaValueMaximumPrefixBefore;

    /**
     * The suffix content of maximum range state of field, before it.
     * @var string
     */
    protected $ariaValueMaximumSuffixBefore;

    /**
     * The prefix content of maximum range state of field, after it.
     * @var string
     */
    protected $ariaValueMaximumPrefixAfter;

    /**
     * The suffix content of maximum range state of field, after it.
     * @var string
     */
    protected $ariaValueMaximumSuffixAfter;

    /**
     * The prefix content of minimum range state of field, before it.
     * @var string
     */
    protected $ariaValueMinimumPrefixBefore;

    /**
     * The suffix content of minimum range state of field, before it.
     * @var string
     */
    protected $ariaValueMinimumSuffixBefore;

    /**
     * The prefix content of minimum range state of field, after it.
     * @var string
     */
    protected $ariaValueMinimumPrefixAfter;

    /**
     * The suffix content of minimum range state of field, after it.
     * @var string
     */
    protected $ariaValueMinimumSuffixAfter;

    /**
     * The content of horizontal orientation state of element, before it.
     * @var string
     */
    protected $ariaOrientationHorizontalBefore;

    /**
     * The content of horizontal orientation state of element, after it.
     * @var string
     */
    protected $ariaOrientationHorizontalAfter;

    /**
     * The content of vertical orientation state of element, before it.
     * @var string
     */
    protected $ariaOrientationVerticalBefore;

    /**
     * The content of vertical orientation state of element, after it.
     * @var string
     */
    protected $ariaOrientationVerticalAfter;

    /**
     * The content of unpressed state of field, before it.
     * @var string
     */
    protected $ariaPressedFalseBefore;

    /**
     * The content of unpressed state of field, after it.
     * @var string
     */
    protected $ariaPressedFalseAfter;

    /**
     * The content of mixed pressed state of field, before it.
     * @var string
     */
    protected $ariaPressedMixedBefore;

    /**
     * The content of mixed pressed state of field, after it.
     * @var string
     */
    protected $ariaPressedMixedAfter;

    /**
     * The content of pressed state of field, before it.
     * @var string
     */
    protected $ariaPressedTrueBefore;

    /**
     * The content of pressed state of field, after it.
     * @var string
     */
    protected $ariaPressedTrueAfter;

    /**
     * The content of required state of field, before it.
     * @var string
     */
    protected $ariaRequiredTrueBefore;

    /**
     * The content of required state of field, after it.
     * @var string
     */
    protected $ariaRequiredTrueAfter;

    /**
     * The content of unselected state of field, before it.
     * @var string
     */
    protected $ariaSelectedFalseBefore;

    /**
     * The content of unselected state of field, after it.
     * @var string
     */
    protected $ariaSelectedFalseAfter;

    /**
     * The content of selected state of field, before it.
     * @var string
     */
    protected $ariaSelectedTrueBefore;

    /**
     * The content of selected state of field, after it.
     * @var string
     */
    protected $ariaSelectedTrueAfter;

    /**
     * The content of ascending sort state of element, before it.
     * @var string
     */
    protected $ariaSortAscendingBefore;

    /**
     * The content of ascending sort state of element, after it.
     * @var string
     */
    protected $ariaSortAscendingAfter;

    /**
     * The content of descending sort state of element, before it.
     * @var string
     */
    protected $ariaSortDescendingBefore;

    /**
     * The content of descending sort state of element, after it.
     * @var string
     */
    protected $ariaSortDescendingAfter;

    /**
     * The content of sorted state of element, before it.
     * @var string
     */
    protected $ariaSortOtherBefore;

    /**
     * The content of sorted state of element, after it.
     * @var string
     */
    protected $ariaSortOtherAfter;

    /**
     * The list element of shortcuts, before the whole content of page.
     * @var \hatemile\util\html\HTMLDOMElement
     */
    protected $listShortcutsBefore;

    /**
     * The list element of shortcuts, after the whole content of page.
     * @var \hatemile\util\html\HTMLDOMElement
     */
    protected $listShortcutsAfter;

    /**
     * The state that indicates if the list of shortcuts of page was added.
     * @var bool
     */
    protected $listShortcutsAdded;

    /**
     * Initializes a new object that manipulate the display for screen readers
     * of parser.
     * @param \hatemile\util\html\HTMLDOMParser $parser The HTML parser.
     * @param \hatemile\util\Configure $configure The configuration of HaTeMiLe.
     * @param string $userAgent The user agent of browser.
     */
    public function __construct(
        HTMLDOMParser $parser,
        Configure $configure,
        $userAgent = null
    ) {
        $this->parser = $parser;
        $this->configure = $configure;
        $this->idGenerator = new IDGenerator('display');
        $this->shortcutPrefix = $this->getShortcutPrefix(
            $userAgent,
            $configure->getParameter('attribute-accesskey-default')
        );
        $this->attributeAccesskeyBefore = $configure->getParameter(
            'attribute-accesskey-before'
        );
        $this->attributeAccesskeyAfter = $configure->getParameter(
            'attribute-accesskey-after'
        );
        $this->attributeAccesskeyPrefixBefore = $configure->getParameter(
            'attribute-accesskey-prefix-before'
        );
        $this->attributeAccesskeySuffixBefore = $configure->getParameter(
            'attribute-accesskey-suffix-before'
        );
        $this->attributeAccesskeyPrefixAfter = $configure->getParameter(
            'attribute-accesskey-prefix-after'
        );
        $this->attributeAccesskeySuffixAfter = $configure->getParameter(
            'attribute-accesskey-suffix-after'
        );
        $this->attributeDownloadBefore = $configure->getParameter(
            'attribute-download-before'
        );
        $this->attributeDownloadAfter = $configure->getParameter(
            'attribute-download-after'
        );
        $this->attributeHeadersPrefixBefore = $configure->getParameter(
            'attribute-headers-prefix-before'
        );
        $this->attributeHeadersSuffixBefore = $configure->getParameter(
            'attribute-headers-suffix-before'
        );
        $this->attributeHeadersPrefixAfter = $configure->getParameter(
            'attribute-headers-prefix-after'
        );
        $this->attributeHeadersSuffixAfter = $configure->getParameter(
            'attribute-headers-suffix-after'
        );
        $this->attributeLanguagePrefixBefore = $configure->getParameter(
            'attribute-language-prefix-before'
        );
        $this->attributeLanguageSuffixBefore = $configure->getParameter(
            'attribute-language-suffix-before'
        );
        $this->attributeLanguagePrefixAfter = $configure->getParameter(
            'attribute-language-prefix-after'
        );
        $this->attributeLanguageSuffixAfter = $configure->getParameter(
            'attribute-language-suffix-after'
        );
        $this->attributeRolePrefixBefore = $configure->getParameter(
            'attribute-role-prefix-before'
        );
        $this->attributeRoleSuffixBefore = $configure->getParameter(
            'attribute-role-suffix-before'
        );
        $this->attributeRolePrefixAfter = $configure->getParameter(
            'attribute-role-prefix-after'
        );
        $this->attributeRoleSuffixAfter = $configure->getParameter(
            'attribute-role-suffix-after'
        );
        $this->attributeTargetBlankBefore = $configure->getParameter(
            'attribute-target-blank-before'
        );
        $this->attributeTargetBlankAfter = $configure->getParameter(
            'attribute-target-blank-after'
        );
        $this->attributeTitlePrefixBefore = $configure->getParameter(
            'attribute-title-prefix-before'
        );
        $this->attributeTitleSuffixBefore = $configure->getParameter(
            'attribute-title-suffix-before'
        );
        $this->attributeTitlePrefixAfter = $configure->getParameter(
            'attribute-title-prefix-after'
        );
        $this->attributeTitleSuffixAfter = $configure->getParameter(
            'attribute-title-suffix-after'
        );

        $this->ariaAutoCompleteBothBefore = $configure->getParameter(
            'aria-autocomplete-both-before'
        );
        $this->ariaAutoCompleteBothAfter = $configure->getParameter(
            'aria-autocomplete-both-after'
        );
        $this->ariaAutoCompleteInlineBefore = $configure->getParameter(
            'aria-autocomplete-inline-before'
        );
        $this->ariaAutoCompleteInlineAfter = $configure->getParameter(
            'aria-autocomplete-inline-after'
        );
        $this->ariaAutoCompleteListBefore = $configure->getParameter(
            'aria-autocomplete-list-before'
        );
        $this->ariaAutoCompleteListAfter = $configure->getParameter(
            'aria-autocomplete-list-after'
        );
        $this->ariaBusyTrueBefore = $configure->getParameter(
            'aria-busy-true-before'
        );
        $this->ariaBusyTrueAfter = $configure->getParameter(
            'aria-busy-true-after'
        );
        $this->ariaCheckedFalseBefore = $configure->getParameter(
            'aria-checked-false-before'
        );
        $this->ariaCheckedFalseAfter = $configure->getParameter(
            'aria-checked-false-after'
        );
        $this->ariaCheckedMixedBefore = $configure->getParameter(
            'aria-checked-mixed-before'
        );
        $this->ariaCheckedMixedAfter = $configure->getParameter(
            'aria-checked-mixed-after'
        );
        $this->ariaCheckedTrueBefore = $configure->getParameter(
            'aria-checked-true-before'
        );
        $this->ariaCheckedTrueAfter = $configure->getParameter(
            'aria-checked-true-after'
        );
        $this->ariaDropeffectCopyBefore = $configure->getParameter(
            'aria-dropeffect-copy-before'
        );
        $this->ariaDropeffectCopyAfter = $configure->getParameter(
            'aria-dropeffect-copy-after'
        );
        $this->ariaDropeffectExecuteBefore = $configure->getParameter(
            'aria-dropeffect-execute-before'
        );
        $this->ariaDropeffectExecuteAfter = $configure->getParameter(
            'aria-dropeffect-execute-after'
        );
        $this->ariaDropeffectLinkBefore = $configure->getParameter(
            'aria-dropeffect-link-before'
        );
        $this->ariaDropeffectLinkAfter = $configure->getParameter(
            'aria-dropeffect-link-after'
        );
        $this->ariaDropeffectMoveBefore = $configure->getParameter(
            'aria-dropeffect-move-before'
        );
        $this->ariaDropeffectMoveAfter = $configure->getParameter(
            'aria-dropeffect-move-after'
        );
        $this->ariaDropeffectPopupBefore = $configure->getParameter(
            'aria-dropeffect-popup-before'
        );
        $this->ariaDropeffectPopupAfter = $configure->getParameter(
            'aria-dropeffect-popup-after'
        );
        $this->ariaExpandedFalseBefore = $configure->getParameter(
            'aria-expanded-false-before'
        );
        $this->ariaExpandedFalseAfter = $configure->getParameter(
            'aria-expanded-false-after'
        );
        $this->ariaExpandedTrueBefore = $configure->getParameter(
            'aria-expanded-true-before'
        );
        $this->ariaExpandedTrueAfter = $configure->getParameter(
            'aria-expanded-true-after'
        );
        $this->ariaGrabbedFalseBefore = $configure->getParameter(
            'aria-grabbed-false-before'
        );
        $this->ariaGrabbedFalseAfter = $configure->getParameter(
            'aria-grabbed-false-after'
        );
        $this->ariaGrabbedTrueBefore = $configure->getParameter(
            'aria-grabbed-true-before'
        );
        $this->ariaGrabbedTrueAfter = $configure->getParameter(
            'aria-grabbed-true-after'
        );
        $this->ariaHaspopupTrueBefore = $configure->getParameter(
            'aria-haspopup-true-before'
        );
        $this->ariaHaspopupTrueAfter = $configure->getParameter(
            'aria-haspopup-true-after'
        );
        $this->ariaLevelPrefixBefore = $configure->getParameter(
            'aria-level-prefix-before'
        );
        $this->ariaLevelSuffixBefore = $configure->getParameter(
            'aria-level-suffix-before'
        );
        $this->ariaLevelPrefixAfter = $configure->getParameter(
            'aria-level-prefix-after'
        );
        $this->ariaLevelSuffixAfter = $configure->getParameter(
            'aria-level-suffix-after'
        );
        $this->ariaValueMaximumPrefixBefore = $configure->getParameter(
            'aria-value-maximum-prefix-before'
        );
        $this->ariaValueMaximumSuffixBefore = $configure->getParameter(
            'aria-value-maximum-suffix-before'
        );
        $this->ariaValueMaximumPrefixAfter = $configure->getParameter(
            'aria-value-maximum-prefix-after'
        );
        $this->ariaValueMaximumSuffixAfter = $configure->getParameter(
            'aria-value-maximum-suffix-after'
        );
        $this->ariaValueMinimumPrefixBefore = $configure->getParameter(
            'aria-value-minimum-prefix-before'
        );
        $this->ariaValueMinimumSuffixBefore = $configure->getParameter(
            'aria-value-minimum-suffix-before'
        );
        $this->ariaValueMinimumPrefixAfter = $configure->getParameter(
            'aria-value-minimum-prefix-after'
        );
        $this->ariaValueMinimumSuffixAfter = $configure->getParameter(
            'aria-value-minimum-suffix-after'
        );
        $this->ariaOrientationHorizontalBefore = $configure->getParameter(
            'aria-orientation-horizontal-before'
        );
        $this->ariaOrientationHorizontalAfter = $configure->getParameter(
            'aria-orientation-horizontal-after'
        );
        $this->ariaOrientationVerticalBefore = $configure->getParameter(
            'aria-orientation-vertical-before'
        );
        $this->ariaOrientationVerticalAfter = $configure->getParameter(
            'aria-orientation-vertical-after'
        );
        $this->ariaPressedFalseBefore = $configure->getParameter(
            'aria-pressed-false-before'
        );
        $this->ariaPressedFalseAfter = $configure->getParameter(
            'aria-pressed-false-after'
        );
        $this->ariaPressedMixedBefore = $configure->getParameter(
            'aria-pressed-mixed-before'
        );
        $this->ariaPressedMixedAfter = $configure->getParameter(
            'aria-pressed-mixed-after'
        );
        $this->ariaPressedTrueBefore = $configure->getParameter(
            'aria-pressed-true-before'
        );
        $this->ariaPressedTrueAfter = $configure->getParameter(
            'aria-pressed-true-after'
        );
        $this->ariaRequiredTrueBefore = $configure->getParameter(
            'aria-required-true-before'
        );
        $this->ariaRequiredTrueAfter = $configure->getParameter(
            'aria-required-true-after'
        );
        $this->ariaSelectedFalseBefore = $configure->getParameter(
            'aria-selected-false-before'
        );
        $this->ariaSelectedFalseAfter = $configure->getParameter(
            'aria-selected-false-after'
        );
        $this->ariaSelectedTrueBefore = $configure->getParameter(
            'aria-selected-true-before'
        );
        $this->ariaSelectedTrueAfter = $configure->getParameter(
            'aria-selected-true-after'
        );
        $this->ariaSortAscendingBefore = $configure->getParameter(
            'aria-sort-ascending-before'
        );
        $this->ariaSortAscendingAfter = $configure->getParameter(
            'aria-sort-ascending-after'
        );
        $this->ariaSortDescendingBefore = $configure->getParameter(
            'aria-sort-descending-before'
        );
        $this->ariaSortDescendingAfter = $configure->getParameter(
            'aria-sort-descending-after'
        );
        $this->listShortcutsAdded = false;
        $this->listShortcutsBefore = null;
        $this->listShortcutsAfter = null;
    }

    /**
     * Returns the shortcut prefix of browser.
     * @param string $userAgent The user agent of browser.
     * @param string $standartPrefix The default prefix.
     * @return string The shortcut prefix of browser.
     */
    protected function getShortcutPrefix($userAgent, $standartPrefix)
    {
        if ($userAgent !== null) {
            $userAgent = strtolower($userAgent);
            $opera = strpos($userAgent, 'opera') !== false;
            $mac = strpos($userAgent, 'mac') !== false;
            $konqueror = strpos($userAgent, 'konqueror') !== false;
            $spoofer = strpos($userAgent, 'spoofer') !== false;
            $safari = strpos($userAgent, 'applewebkit') !== false;
            $windows = strpos($userAgent, 'windows') !== false;
            $chrome = strpos($userAgent, 'chrome') !== false;
            $firefox = (
                strpos($userAgent, 'firefox') !== false
                || strpos($userAgent, 'minefield') !== false
            );
            $ie = (
                (strpos($userAgent, 'msie') !== false)
                || (strpos($userAgent, 'trident') !== false)
            );

            if ($opera) {
                return 'SHIFT + ESC';
            } elseif ($chrome && $mac && !$spoofer) {
                return 'CTRL + OPTION';
            } elseif ($safari && !$windows && !$spoofer) {
                return 'CTRL + ALT';
            } elseif (!$windows && ($safari || $mac || $konqueror)) {
                return 'CTRL';
            } elseif ($firefox) {
                return 'ALT + SHIFT';
            } elseif ($chrome || $ie) {
                return 'ALT';
            } else {
                return $standartPrefix;
            }
        } else {
            return $standartPrefix;
        }
    }

    /**
     * Returns the description of role.
     * @param string $role The role.
     * @return string The description of role.
     */
    protected function getRoleDescription($role)
    {
        $parameter = 'role-' . strtolower($role);
        if ($this->configure->hasParameter($parameter)) {
            return $this->configure->getParameter($parameter);
        } else {
            return null;
        }
    }

    /**
     * Returns the description of language.
     * @param string $languageCode The BCP 47 code language.
     * @return string The description of language.
     */
    protected function getLanguageDescription($languageCode)
    {
        $language = strtolower($languageCode);
        $parameter = 'language-' . $language;
        if ($this->configure->hasParameter($parameter)) {
            return $this->configure->getParameter($parameter);
        } elseif (strpos($language, '-') !== false) {
            $codes = \preg_split('/\-/', $language);
            $parameter = 'language-' . $codes[0];
            if ($this->configure->hasParameter($parameter)) {
                return $this->configure->getParameter($parameter);
            }
        }
        return null;
    }

    /**
     * Returns the description of element.
     * @param \hatemile\util\html\HTMLDOMElement $element The element.
     * @return string The description of element.
     */
    protected function getDescription(HTMLDOMElement $element)
    {
        if ($element->hasAttribute('title')) {
            $description = $element->getAttribute('title');
        } elseif ($element->hasAttribute('aria-label')) {
            $description = $element->getAttribute('aria-label');
        } elseif ($element->hasAttribute('alt')) {
            $description = $element->getAttribute('alt');
        } elseif ($element->hasAttribute('label')) {
            $description = $element->getAttribute('label');
        } elseif (
            ($element->hasAttribute('aria-labelledby'))
            || ($element->hasAttribute('aria-describedby'))
        ) {
            if ($element->hasAttribute('aria-labelledby')) {
                $descriptionIds = preg_split(
                    '/[ \n\t\r]+/',
                    $element->getAttribute('aria-labelledby')
                );
            } else {
                $descriptionIds = preg_split(
                    '/[ \n\t\r]+/',
                    $element->getAttribute('aria-describedby')
                );
            }
            foreach ($descriptionIds as $descriptionId) {
                $elementDescription = $this->parser->find(
                    '#' .
                    $descriptionId
                )->firstResult();
                if ($elementDescription !== null) {
                    $description = $elementDescription->getTextContent();
                    break;
                }
            }
        } elseif (
            ($element->getTagName() === 'INPUT')
            && ($element->hasAttribute('type'))
        ) {
            $type = strtolower($element->getAttribute('type'));
            if (
                (
                    ($type === 'button')
                    || ($type === 'submit')
                    || ($type === 'reset')
                )
                && ($element->hasAttribute('value'))
            ) {
                $description = $element->getAttribute('value');
            }
        }
        if (empty($description)) {
            $description = $element->getTextContent();
        }
        return \trim(\preg_replace('/[ \n\r\t]+/', ' ', $description));
    }

    /**
     * Generate the list of shortcuts of page.
     */
    protected function generateListShortcuts()
    {
        $local = $this->parser->find('body')->firstResult();
        if ($local !== null) {
            $containerBefore = $this->parser->find(
                '#' .
                AccessibleDisplayScreenReaderImplementation
                    ::ID_CONTAINER_SHORTCUTS_BEFORE
            )->firstResult();
            if (
                ($containerBefore === null)
                && (!empty($this->attributeAccesskeyBefore))
            ) {
                $containerBefore = $this->parser->createElement('div');
                $containerBefore->setAttribute(
                    'id',
                    AccessibleDisplayScreenReaderImplementation
                            ::ID_CONTAINER_SHORTCUTS_BEFORE
                );

                $textContainer = $this->parser->createElement('span');
                $textContainer->setAttribute(
                    'class',
                    AccessibleDisplayScreenReaderImplementation
                            ::CLASS_TEXT_SHORTCUTS
                );
                $textContainer->appendText($this->attributeAccesskeyBefore);

                $containerBefore->appendElement($textContainer);
                $local->prependElement($containerBefore);
            }
            if ($containerBefore !== null) {
                $this->listShortcutsBefore = $this->parser->find(
                    $containerBefore
                )->findChildren('ul')->firstResult();
                if ($this->listShortcutsBefore === null) {
                    $this->listShortcutsBefore = $this->parser->createElement(
                        'ul'
                    );
                    $containerBefore->appendElement($this->listShortcutsBefore);
                }
            }


            $containerAfter = $this->parser->find(
                '#' .
                AccessibleDisplayScreenReaderImplementation
                    ::ID_CONTAINER_SHORTCUTS_AFTER
            )->firstResult();
            if (
                ($containerAfter === null)
                && (!empty($this->attributeAccesskeyAfter))
            ) {
                $containerAfter = $this->parser->createElement('div');
                $containerAfter->setAttribute(
                    'id',
                    AccessibleDisplayScreenReaderImplementation
                            ::ID_CONTAINER_SHORTCUTS_AFTER
                );

                $textContainer = $this->parser->createElement('span');
                $textContainer->setAttribute(
                    'class',
                    AccessibleDisplayScreenReaderImplementation
                            ::CLASS_TEXT_SHORTCUTS
                );
                $textContainer->appendText($this->attributeAccesskeyAfter);

                $containerAfter->appendElement($textContainer);
                $local->appendElement($containerAfter);
            }
            if ($containerAfter !== null) {
                $this->listShortcutsAfter = $this->parser->find(
                    $containerAfter
                )->findChildren('ul')->firstResult();
                if ($this->listShortcutsAfter === null) {
                    $this->listShortcutsAfter = $this->parser->createElement(
                        'ul'
                    );
                    $containerAfter->appendElement($this->listShortcutsAfter);
                }
            }
        }
        $this->listShortcutsAdded = true;
    }

    /**
     * Insert a element before or after other element.
     * @param \hatemile\util\html\HTMLDOMElement $element The reference element.
     * @param \hatemile\util\html\HTMLDOMElement $insertedElement The element
     * that be inserted.
     * @param bool $before To insert the element before the other element.
     */
    protected function insert(
        HTMLDOMElement $element,
        HTMLDOMElement $insertedElement,
        $before
    ) {
        $tagName = $element->getTagName();
        $appendTags = array(
            'BODY',
            'A',
            'FIGCAPTION',
            'LI',
            'DT',
            'DD',
            'LABEL',
            'OPTION',
            'TD',
            'TH'
        );
        $controls = array('INPUT', 'SELECT', 'TEXTAREA');
        if ($tagName === 'HTML') {
            $body = $this->parser->find('body')->firstResult();
            if ($body !== null) {
                $this->insert($body, $insertedElement, $before);
            }
        } elseif (in_array($tagName, $appendTags)) {
            if ($before) {
                $element->prependElement($insertedElement);
            } else {
                $element->appendElement($insertedElement);
            }
        } elseif (in_array($tagName, $controls)) {
            $labels = array();
            if ($element->hasAttribute('id')) {
                $labels = $this->parser->find(
                    'label[for="' .
                    $element->getAttribute('id') .
                    '"]'
                )->listResults();
            }
            if (empty($labels)) {
                $labels = $this->parser->find($element)->findAncestors(
                    'label'
                )->listResults();
            }
            foreach ($labels as $label) {
                $this->insert($label, $insertedElement, $before);
            }
        } elseif ($before) {
            $element->insertBefore($insertedElement);
        } else {
            $element->insertAfter($insertedElement);
        }
    }

    /**
     * Force the screen reader display an information of element.
     * @param \hatemile\util\html\HTMLDOMElement $element The reference element.
     * @param string $textBefore The text content to show before the element.
     * @param string $textAfter The text content to show after the element.
     * @param string $dataOf The name of attribute that links the content with
     * element.
     */
    protected function forceReadSimple(
        HTMLDOMElement $element,
        $textBefore,
        $textAfter,
        $dataOf
    ) {
        $this->idGenerator->generateId($element);
        $identifier = $element->getAttribute('id');
        $selector = '[' . $dataOf . '="' . $identifier . '"]';

        $referenceBefore = $this->parser->find(
            '.' .
            AccessibleDisplayScreenReaderImplementation
                    ::CLASS_FORCE_READ_BEFORE .
            $selector
        )->firstResult();
        $referenceAfter = $this->parser->find(
            '.' .
            AccessibleDisplayScreenReaderImplementation
                    ::CLASS_FORCE_READ_AFTER .
            $selector
        )->firstResult();
        $references = $this->parser->find($selector)->listResults();
        for ($i = sizeof($references) - 1; $i >= 0; $i--) {
            if (
                ($references[$i]->equals($referenceBefore))
                || ($references[$i]->equals($referenceAfter))
            ) {
                array_splice($references, $i, 1);
            }
        }

        if (empty($references)) {
            if (!empty($textBefore)) {
                if ($referenceBefore !== null) {
                    $referenceBefore->removeNode();
                }

                $span = $this->parser->createElement('span');
                $span->setAttribute(
                    'class',
                    AccessibleDisplayScreenReaderImplementation
                            ::CLASS_FORCE_READ_BEFORE
                );
                $span->setAttribute($dataOf, $identifier);
                $span->appendText($textBefore);
                $this->insert($element, $span, true);
            }
            if (!empty($textAfter)) {
                if ($referenceAfter !== null) {
                    $referenceAfter->removeNode();
                }

                $span = $this->parser->createElement('span');
                $span->setAttribute(
                    'class',
                    AccessibleDisplayScreenReaderImplementation
                            ::CLASS_FORCE_READ_AFTER
                );
                $span->setAttribute($dataOf, $identifier);
                $span->appendText($textAfter);
                $this->insert($element, $span, false);
            }
        }
    }

    /**
     * Force the screen reader display an information of element with prefixes
     * or suffixes.
     * @param \hatemile\util\html\HTMLDOMElement $element The reference element.
     * @param string $value The value to be show.
     * @param string $textPrefixBefore The prefix of value to show before the
     * element.
     * @param string $textSuffixBefore The suffix of value to show before the
     * element.
     * @param string $textPrefixAfter The prefix of value to show after the
     * element.
     * @param string $textSuffixAfter The suffix of value to show after the
     * element.
     * @param string $dataOf The name of attribute that links the content with
     * element.
     */
    protected function forceRead(
        HTMLDOMElement $element,
        $value,
        $textPrefixBefore,
        $textSuffixBefore,
        $textPrefixAfter,
        $textSuffixAfter,
        $dataOf
    ) {
        $textBefore = '';
        $textAfter = '';
        if ((!empty($textPrefixBefore)) || (!empty($textSuffixBefore))) {
            $textBefore = $textPrefixBefore . $value . $textSuffixBefore;
        }
        if ((!empty($textPrefixAfter)) || (!empty($textSuffixAfter))) {
            $textAfter = $textPrefixAfter . $value . $textSuffixAfter;
        }
        $this->forceReadSimple($element, $textBefore, $textAfter, $dataOf);
    }

    public function displayShortcut(HTMLDOMElement $element)
    {
        if ($element->hasAttribute('accesskey')) {
            $description = $this->getDescription($element);
            if (!$element->hasAttribute('title')) {
                $this->idGenerator->generateId($element);
                $element->setAttribute(
                    AccessibleDisplayScreenReaderImplementation
                            ::DATA_ATTRIBUTE_TITLE_OF,
                    $element->getAttribute('id')
                );
                $element->setAttribute('title', $description);
            }

            if (!$this->listShortcutsAdded) {
                $this->generateListShortcuts();
            }

            $keys = preg_split(
                '/[ \n\t\r]+/',
                strtoupper($element->getAttribute('accesskey'))
            );
            foreach ($keys as $key) {
                $selector = (
                    '[' .
                    AccessibleDisplayScreenReaderImplementation
                            ::DATA_ATTRIBUTE_ACCESSKEY_OF .
                    '="' .
                    $key .
                    '"]'
                );
                $shortcut = $this->shortcutPrefix . ' + ' . $key;
                $this->forceRead(
                    $element,
                    $shortcut,
                    $this->attributeAccesskeyPrefixBefore,
                    $this->attributeAccesskeySuffixBefore,
                    $this->attributeAccesskeyPrefixAfter,
                    $this->attributeAccesskeySuffixAfter,
                    AccessibleDisplayScreenReaderImplementation
                            ::DATA_ATTRIBUTE_ACCESSKEY_OF
                );

                $item = $this->parser->createElement('li');
                $item->setAttribute(
                    AccessibleDisplayScreenReaderImplementation
                            ::DATA_ATTRIBUTE_ACCESSKEY_OF,
                    $key
                );
                $item->appendText($shortcut . ': ' . $description);
                if (
                    ($this->listShortcutsBefore)
                    && ($this->parser->find(
                        $this->listShortcutsBefore
                    )->findChildren($selector)->firstResult() === null)
                ) {
                    $this->listShortcutsBefore->appendElement(
                        $item->cloneElement()
                    );
                }
                if (
                    ($this->listShortcutsAfter)
                    && ($this->parser->find(
                        $this->listShortcutsAfter
                    )->findChildren($selector)->firstResult() === null)
                ) {
                    $this->listShortcutsAfter->appendElement(
                        $item->cloneElement()
                    );
                }
            }
        }
    }

    public function displayAllShortcuts()
    {
        $elements = $this->parser->find('[accesskey]')->listResults();
        foreach ($elements as $element) {
            if (CommonFunctions::isValidElement($element)) {
                $this->displayShortcut($element);
            }
        }
    }

    public function displayRole(HTMLDOMElement $element)
    {
        if ($element->hasAttribute('role')) {
            $roleDescription = $this->getRoleDescription(
                $element->getAttribute('role')
            );
            if ($roleDescription !== null) {
                $this->forceRead(
                    $element,
                    $roleDescription,
                    $this->attributeRolePrefixBefore,
                    $this->attributeRoleSuffixBefore,
                    $this->attributeRolePrefixAfter,
                    $this->attributeRoleSuffixAfter,
                    AccessibleDisplayScreenReaderImplementation::DATA_ROLE_OF
                );
            }
        }
    }

    public function displayAllRoles()
    {
        $elements = $this->parser->find('[role]')->listResults();
        foreach ($elements as $element) {
            if (CommonFunctions::isValidElement($element)) {
                $this->displayRole($element);
            }
        }
    }

    public function displayCellHeader(HTMLDOMElement $tableCell)
    {
        if ($tableCell->hasAttribute('headers')) {
            $textHeader = '';
            $idsHeaders = preg_split(
                '/[ \n\t\r]+/',
                $tableCell->getAttribute('headers')
            );
            foreach ($idsHeaders as $idHeader) {
                $header = $this->parser->find('#' . $idHeader)->firstResult();
                if ($header !== null) {
                    if ($textHeader === '') {
                        $textHeader = \trim($header->getTextContent());
                    } else {
                        $textHeader = (
                            $textHeader .
                            ' ' .
                            \trim($header->getTextContent())
                        );
                    }
                }
            }
            if (!empty(\trim($textHeader))) {
                $this->forceRead(
                    $tableCell,
                    $textHeader,
                    $this->attributeHeadersPrefixBefore,
                    $this->attributeHeadersSuffixBefore,
                    $this->attributeHeadersPrefixAfter,
                    $this->attributeHeadersSuffixAfter,
                    AccessibleDisplayScreenReaderImplementation
                            ::DATA_ATTRIBUTE_HEADERS_OF
                );
            }
        }
    }

    public function displayAllCellHeaders()
    {
        $elements = $this->parser->find(
            'td[headers],th[headers]'
        )->listResults();
        foreach ($elements as $element) {
            if (CommonFunctions::isValidElement($element)) {
                $this->displayCellHeader($element);
            }
        }
    }

    public function displayWAIARIAStates(HTMLDOMElement $element)
    {
        if (
            ($element->hasAttribute('aria-busy'))
            && ($element->getAttribute('aria-busy') === 'true')
        ) {
            $this->forceReadSimple(
                $element,
                $this->ariaBusyTrueBefore,
                $this->ariaBusyTrueAfter,
                AccessibleDisplayScreenReaderImplementation::DATA_ARIA_BUSY_OF
            );
        }
        if ($element->hasAttribute('aria-checked')) {
            $attributeValue = $element->getAttribute('aria-checked');
            if ($attributeValue === 'true') {
                $this->forceReadSimple(
                    $element,
                    $this->ariaCheckedTrueBefore,
                    $this->ariaCheckedTrueAfter,
                    AccessibleDisplayScreenReaderImplementation
                            ::DATA_ARIA_CHECKED_OF
                );
            } elseif ($attributeValue === 'false') {
                $this->forceReadSimple(
                    $element,
                    $this->ariaCheckedFalseBefore,
                    $this->ariaCheckedFalseAfter,
                    AccessibleDisplayScreenReaderImplementation
                            ::DATA_ARIA_CHECKED_OF
                );
            } elseif ($attributeValue === 'mixed') {
                $this->forceReadSimple(
                    $element,
                    $this->ariaCheckedMixedBefore,
                    $this->ariaCheckedMixedAfter,
                    AccessibleDisplayScreenReaderImplementation
                            ::DATA_ARIA_CHECKED_OF
                );
            }
        }
        if ($element->hasAttribute('aria-expanded')) {
            $attributeValue = $element->getAttribute('aria-expanded');
            if ($attributeValue === 'true') {
                $this->forceReadSimple(
                    $element,
                    $this->ariaExpandedTrueBefore,
                    $this->ariaExpandedTrueAfter,
                    AccessibleDisplayScreenReaderImplementation
                            ::DATA_ARIA_EXPANDED_OF
                );
            } elseif ($attributeValue === 'false') {
                $this->forceReadSimple(
                    $element,
                    $this->ariaExpandedFalseBefore,
                    $this->ariaExpandedFalseAfter,
                    AccessibleDisplayScreenReaderImplementation
                            ::DATA_ARIA_EXPANDED_OF
                );
            }
        }
        if (
            ($element->hasAttribute('aria-haspopup'))
            && ($element->getAttribute('aria-haspopup') === 'true')
        ) {
            $this->forceReadSimple(
                $element,
                $this->ariaHaspopupTrueBefore,
                $this->ariaHaspopupTrueAfter,
                AccessibleDisplayScreenReaderImplementation
                        ::DATA_ARIA_HASPOPUP_OF
            );
        }
        if ($element->hasAttribute('aria-level')) {
            $this->forceRead(
                $element,
                $element->getAttribute('aria-level'),
                $this->ariaLevelPrefixBefore,
                $this->ariaLevelSuffixBefore,
                $this->ariaLevelPrefixAfter,
                $this->ariaLevelSuffixAfter,
                AccessibleDisplayScreenReaderImplementation::DATA_ARIA_LEVEL_OF
            );
        }
        if ($element->hasAttribute('aria-orientation')) {
            $attributeValue = $element->getAttribute('aria-orientation');
            if ($attributeValue === 'vertical') {
                $this->forceReadSimple(
                    $element,
                    $this->ariaOrientationVerticalBefore,
                    $this->ariaOrientationVerticalAfter,
                    AccessibleDisplayScreenReaderImplementation
                            ::DATA_ARIA_ORIENTATION_OF
                );
            } elseif ($attributeValue === 'horizontal') {
                $this->forceReadSimple(
                    $element,
                    $this->ariaOrientationHorizontalBefore,
                    $this->ariaOrientationHorizontalAfter,
                    AccessibleDisplayScreenReaderImplementation
                            ::DATA_ARIA_ORIENTATION_OF
                );
            }
        }
        if ($element->hasAttribute('aria-pressed')) {
            $attributeValue = $element->getAttribute('aria-pressed');
            if ($attributeValue === 'true') {
                $this->forceReadSimple(
                    $element,
                    $this->ariaPressedTrueBefore,
                    $this->ariaPressedTrueAfter,
                    AccessibleDisplayScreenReaderImplementation
                            ::DATA_ARIA_PRESSED_OF
                );
            } elseif ($attributeValue === 'false') {
                $this->forceReadSimple(
                    $element,
                    $this->ariaPressedFalseBefore,
                    $this->ariaPressedFalseAfter,
                    AccessibleDisplayScreenReaderImplementation
                            ::DATA_ARIA_PRESSED_OF
                );
            } elseif ($attributeValue === 'mixed') {
                $this->forceReadSimple(
                    $element,
                    $this->ariaPressedMixedBefore,
                    $this->ariaPressedMixedAfter,
                    AccessibleDisplayScreenReaderImplementation
                            ::DATA_ARIA_PRESSED_OF
                );
            }
        }
        if ($element->hasAttribute('aria-selected')) {
            $attributeValue = $element->getAttribute('aria-selected');
            if ($attributeValue === 'true') {
                $this->forceReadSimple(
                    $element,
                    $this->ariaSelectedTrueBefore,
                    $this->ariaSelectedTrueAfter,
                    AccessibleDisplayScreenReaderImplementation
                            ::DATA_ARIA_SELECTED_OF
                );
            } elseif ($attributeValue === 'false') {
                $this->forceReadSimple(
                    $element,
                    $this->ariaSelectedFalseBefore,
                    $this->ariaSelectedFalseAfter,
                    AccessibleDisplayScreenReaderImplementation
                            ::DATA_ARIA_SELECTED_OF
                );
            }
        }
        if ($element->hasAttribute('aria-sort')) {
            $attributeValue = $element->getAttribute('aria-sort');
            if ($attributeValue === 'ascending') {
                $this->forceReadSimple(
                    $element,
                    $this->ariaSortAscendingBefore,
                    $this->ariaSortAscendingAfter,
                    AccessibleDisplayScreenReaderImplementation
                            ::DATA_ARIA_SORT_OF
                );
            } elseif ($attributeValue === 'descending') {
                $this->forceReadSimple(
                    $element,
                    $this->ariaSortDescendingBefore,
                    $this->ariaSortDescendingAfter,
                    AccessibleDisplayScreenReaderImplementation
                            ::DATA_ARIA_SORT_OF
                );
            } elseif ($attributeValue === 'other') {
                $this->forceReadSimple(
                    $element,
                    $this->ariaSortOtherBefore,
                    $this->ariaSortOtherAfter,
                    AccessibleDisplayScreenReaderImplementation
                            ::DATA_ARIA_SORT_OF
                );
            }
        }
        if (
            ($element->hasAttribute('aria-required'))
            && ($element->getAttribute('aria-required') === 'true')
        ) {
            $this->forceReadSimple(
                $element,
                $this->ariaRequiredTrueBefore,
                $this->ariaRequiredTrueAfter,
                AccessibleDisplayScreenReaderImplementation
                        ::DATA_ARIA_REQUIRED_OF
            );
        }
        if ($element->hasAttribute('aria-valuemin')) {
            $this->forceRead(
                $element,
                $element->getAttribute('aria-valuemin'),
                $this->ariaValueMinimumPrefixBefore,
                $this->ariaValueMinimumSuffixBefore,
                $this->ariaValueMinimumPrefixAfter,
                $this->ariaValueMinimumSuffixAfter,
                AccessibleDisplayScreenReaderImplementation
                        ::DATA_ARIA_RANGE_MIN_OF
            );
        }
        if ($element->hasAttribute('aria-valuemax')) {
            $this->forceRead(
                $element,
                $element->getAttribute('aria-valuemax'),
                $this->ariaValueMaximumPrefixBefore,
                $this->ariaValueMaximumSuffixBefore,
                $this->ariaValueMaximumPrefixAfter,
                $this->ariaValueMaximumSuffixAfter,
                AccessibleDisplayScreenReaderImplementation
                        ::DATA_ARIA_RANGE_MAX_OF
            );
        }
        if ($element->hasAttribute('aria-autocomplete')) {
            $attributeValue = $element->getAttribute('aria-autocomplete');
            if ($attributeValue === 'both') {
                $this->forceReadSimple(
                    $element,
                    $this->ariaAutoCompleteBothBefore,
                    $this->ariaAutoCompleteBothAfter,
                    AccessibleDisplayScreenReaderImplementation
                            ::DATA_ARIA_AUTOCOMPLETE_OF
                );
            } elseif ($attributeValue === 'inline') {
                $this->forceReadSimple(
                    $element,
                    $this->ariaAutoCompleteListBefore,
                    $this->ariaAutoCompleteListAfter,
                    AccessibleDisplayScreenReaderImplementation
                            ::DATA_ARIA_AUTOCOMPLETE_OF
                );
            } elseif ($attributeValue === 'list') {
                $this->forceReadSimple(
                    $element,
                    $this->ariaAutoCompleteInlineBefore,
                    $this->ariaAutoCompleteInlineAfter,
                    AccessibleDisplayScreenReaderImplementation
                            ::DATA_ARIA_AUTOCOMPLETE_OF
                );
            }
        }
        if ($element->hasAttribute('aria-dropeffect')) {
            $attributeValue = $element->getAttribute('aria-dropeffect');
            if ($attributeValue === 'copy') {
                $this->forceReadSimple(
                    $element,
                    $this->ariaDropeffectCopyBefore,
                    $this->ariaDropeffectCopyAfter,
                    AccessibleDisplayScreenReaderImplementation
                            ::DATA_ARIA_DROPEFFECT_OF
                );
            } elseif ($attributeValue === 'move') {
                $this->forceReadSimple(
                    $element,
                    $this->ariaDropeffectMoveBefore,
                    $this->ariaDropeffectMoveAfter,
                    AccessibleDisplayScreenReaderImplementation
                            ::DATA_ARIA_DROPEFFECT_OF
                );
            } elseif ($attributeValue === 'link') {
                $this->forceReadSimple(
                    $element,
                    $this->ariaDropeffectLinkBefore,
                    $this->ariaDropeffectLinkAfter,
                    AccessibleDisplayScreenReaderImplementation
                            ::DATA_ARIA_DROPEFFECT_OF
                );
            } elseif ($attributeValue === 'execute') {
                $this->forceReadSimple(
                    $element,
                    $this->ariaDropeffectExecuteBefore,
                    $this->ariaDropeffectExecuteAfter,
                    AccessibleDisplayScreenReaderImplementation
                            ::DATA_ARIA_DROPEFFECT_OF
                );
            } elseif ($attributeValue === 'popup') {
                $this->forceReadSimple(
                    $element,
                    $this->ariaDropeffectPopupBefore,
                    $this->ariaDropeffectPopupAfter,
                    AccessibleDisplayScreenReaderImplementation
                            ::DATA_ARIA_DROPEFFECT_OF
                );
            }
        }
        if ($element->hasAttribute('aria-grabbed')) {
            $attributeValue = $element->getAttribute('aria-grabbed');
            if ($attributeValue === 'true') {
                $this->forceReadSimple(
                    $element,
                    $this->ariaGrabbedTrueBefore,
                    $this->ariaGrabbedTrueAfter,
                    AccessibleDisplayScreenReaderImplementation
                            ::DATA_ARIA_GRABBED_OF
                );
            } elseif ($attributeValue === 'false') {
                $this->forceReadSimple(
                    $element,
                    $this->ariaGrabbedFalseBefore,
                    $this->ariaGrabbedFalseAfter,
                    AccessibleDisplayScreenReaderImplementation
                            ::DATA_ARIA_GRABBED_OF
                );
            }
        }
    }

    public function displayAllWAIARIAStates()
    {
        $elements = $this->parser->find(
            '[aria-busy=true],[aria-checked],[aria-dropeffect],' .
            '[aria-expanded],[aria-grabbed],[aria-haspopup],[aria-level],' .
            '[aria-orientation],[aria-pressed],[aria-selected],[aria-sort],' .
            '[aria-required=true],[aria-valuemin],[aria-valuemax],' .
            '[aria-autocomplete]'
        )->listResults();
        foreach ($elements as $element) {
            if (CommonFunctions::isValidElement($element)) {
                $this->displayWAIARIAStates($element);
            }
        }
    }

    public function displayLinkAttributes(HTMLDOMElement $link)
    {
        if ($link->hasAttribute('download')) {
            $this->forceReadSimple(
                $link,
                $this->attributeDownloadBefore,
                $this->attributeDownloadAfter,
                AccessibleDisplayScreenReaderImplementation
                        ::DATA_ATTRIBUTE_DOWNLOAD_OF
            );
        }
        if (
            ($link->hasAttribute('target'))
            && ($link->getAttribute('target') === '_blank')
        ) {
            $this->forceReadSimple(
                $link,
                $this->attributeTargetBlankBefore,
                $this->attributeTargetBlankAfter,
                AccessibleDisplayScreenReaderImplementation
                        ::DATA_ATTRIBUTE_TARGET_OF
            );
        }
    }

    public function displayAllLinksAttributes()
    {
        $elements = $this->parser->find(
            'a[download],a[target="_blank"]'
        )->listResults();
        foreach ($elements as $element) {
            if (CommonFunctions::isValidElement($element)) {
                $this->displayLinkAttributes($element);
            }
        }
    }

    public function displayTitle(HTMLDOMElement $element)
    {
        if ($element->getTagName() === 'IMG') {
            $this->displayAlternativeTextImage($element);
        } elseif (
            ($element->hasAttribute('title'))
            && (!empty($element->getAttribute('title')))
        ) {
            $this->forceRead(
                $element,
                $element->getAttribute('title'),
                $this->attributeTitlePrefixBefore,
                $this->attributeTitleSuffixBefore,
                $this->attributeTitlePrefixAfter,
                $this->attributeTitleSuffixAfter,
                AccessibleDisplayScreenReaderImplementation
                        ::DATA_ATTRIBUTE_TITLE_OF
            );
        }
    }

    public function displayAllTitles()
    {
        $elements = $this->parser->find('body [title]')->listResults();
        foreach ($elements as $element) {
            if (CommonFunctions::isValidElement($element)) {
                $this->displayTitle($element);
            }
        }
    }

    public function displayLanguage(HTMLDOMElement $element)
    {
        $languageCode = null;
        if ($element->hasAttribute('lang')) {
            $languageCode = $element->getAttribute('lang');
        } elseif ($element->hasAttribute('hreflang')) {
            $languageCode = $element->getAttribute('hreflang');
        }
        $language = $this->getLanguageDescription($languageCode);
        if ($language !== null) {
            $this->forceRead(
                $element,
                $language,
                $this->attributeLanguagePrefixBefore,
                $this->attributeLanguageSuffixBefore,
                $this->attributeLanguagePrefixAfter,
                $this->attributeLanguageSuffixAfter,
                AccessibleDisplayScreenReaderImplementation
                        ::DATA_ATTRIBUTE_LANGUAGE_OF
            );
        }
    }

    public function displayAllLanguages()
    {
        $elements = $this->parser->find(
            'html[lang],body[lang],body [lang],body [hreflang]'
        )->listResults();
        foreach ($elements as $element) {
            if (CommonFunctions::isValidElement($element)) {
                $this->displayLanguage($element);
            }
        }
    }

    public function displayAlternativeTextImage(HTMLDOMElement $image)
    {
        if (($image->hasAttribute('alt')) || ($image->hasAttribute('title'))) {
            if (
                ($image->hasAttribute('alt'))
                && (!$image->hasAttribute('title'))
            ) {
                $image->setAttribute('title', $image->getAttribute('alt'));
            } elseif (
                ($image->hasAttribute('title'))
                && (!$image->hasAttribute('alt'))
            ) {
                $image->setAttribute('alt', $image->getAttribute('title'));
            }
            $this->idGenerator->generateId($image);
            $image->setAttribute(
                AccessibleDisplayScreenReaderImplementation
                        ::DATA_ATTRIBUTE_TITLE_OF,
                $image->getAttribute('id')
            );
        } else {
            $image->setAttribute('alt', '');
            $image->setAttribute('role', 'presentation');
            $image->setAttribute('aria-hidden', 'true');
        }
    }

    public function displayAllAlternativeTextImages()
    {
        $images = $this->parser->find('img')->listResults();
        foreach ($images as $image) {
            if (CommonFunctions::isValidElement($image)) {
                $this->displayAlternativeTextImage($image);
            }
        }
    }
}
