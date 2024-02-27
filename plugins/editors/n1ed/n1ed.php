<?php

/**
 * N1ED module for Joomla 3
 * Developer: N1ED
 * Website: https://n1ed.com/
 * License: GNU General Public License Version 3 or later
 **/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * N1ED Plugin
 *
 * @since  1.5
 */
class plgEditorN1ed extends JPlugin
{
    protected $autoloadLanguage = true;

    /**
     * Method to handle the onInitEditor event.
     *  - Initialises the Editor
     *
     * @return  void
     *
     * @since 1.5
     */
    public function onInit()
    {
        // Check API key
        $item_params = new JRegistry();
        $item_params->merge($this->params);

        $component_params = JComponentHelper::getParams('com_n1ed');
        $api_key = $component_params->get('api_key', 'N1J424RR');
        $token = null;
        $i = strpos($api_key, ':');
        if ($i !== false) {
            $token = substr($api_key, $i + 1);
            $api_key = substr($api_key, 0, $i);
        }

        $app = JFactory::getApplication();

        $url_setApiKey = null;
        $url_flmngr = null;
        $url_n1ed = null;

        if ($app->isClient('administrator')) {
            $url_setApiKey =
                JUri::root() .
                'administrator/index.php?option=com_n1ed&task=setApiKey&site=admin';
            $url_flmngr =
                JUri::root() .
                'administrator/index.php?option=com_n1ed&task=flmngr&site=admin';
        }

        if (!$app->isClient('administrator')) {
            $url_setApiKey =
                JUri::root() . '?option=com_n1ed&task=setApiKey&site=admin';
            $url_flmngr =
                JUri::root() . '?option=com_n1ed&task=flmngr&site=site';
        }

        $user = JFactory::getUser();
        if ($user->authorise('core.edit', 'com_n1ed') !== true) {
            $token = null; // do not set token for non-admin users
        }

        JFactory::getDocument()->addStyleDeclaration("

        /* Hide editor textarea */
        .n1ed-field {
            display: none !important;
        }

        /* Fix preview modal */
        #sbox-window {
            z-index: 70000;
        }
    ");

        // Add N1ED core
        JFactory::getDocument()->addScriptDeclaration(
            "

    function initN1ED() {
          var el = document.querySelector(\"[data-n1ed]\");
         


          Joomla.editors.instances['n1ed'] = {
              'id': 'n1ed',
              'element':  el,
              'getValue': function ()     { return this.el.value; },
              'setValue': function (text) { return this.el.value = text; },
          };
    }

    var elHead = document.querySelector(\"head\");
    var elScript = document.createElement(\"script\");
    elScript.src = 'https://cloud.n1ed.com/cdn/" .
                $api_key .
                "/n1tinymce.js';
    elScript.addEventListener('error', function() {
      alert('Unable to load N1ED from CDN: check your network connection and try again.');
    });
    elScript.addEventListener('load', function() {
      if (document.readyState === \"complete\" || document.readyState === \"loaded\" || document.readyState === \"interactive\")
        initN1ED();
      else
        document.addEventListener('DOMContentLoaded', initN1ED);
    });
    elScript.type = \"text/javascript\";
    elHead.appendChild(elScript);
    function setupNow(editor_id) {
      tinymce.init({
        selector: '[data-n1ed]',
        Flmngr: {
            urlFileManager: '" . $url_flmngr . "',
            urlFiles: '" . rtrim(parse_url(JURI::root())['path'], '/') . "/images',
        },
        extended_valid_elements: 'span[*]',
        integration: 'joomla4',
        urlSetApiKeyAndToken: '" . $url_setApiKey . "',
        apiKey: '" . $api_key . "',
        token : '" . $token . "',
        relative_urls: false,
        framework: 'bootstrap5',
        document_base_url: window.location.origin,
        include: {
            frameworkCssOnEditorPage: false,
            frameworkJsOnEditorPage: false
        }
      });
    }
    
    function waitForEditor() {
      if (window.tinymce) {
          setupNow();
      } else {
        setTimeout(function () {
          waitForEditor();
        }, 100);
      }
    }
    
    waitForEditor();

"
        );
    }

    /**
     * Get the editor content.
     *
     * @param string $id The id of the editor field.
     *
     * @return  string
     *
     * @deprecated 4.0 Use directly the returned code
     */
    public function onGetContent($id)
    {
        return 'Joomla.editors.instances[' . json_encode($id) . '].getValue();';
    }

    /**
     * Set the editor content.
     *
     * @param string $id The id of the editor field.
     * @param string $html The content to set.
     *
     * @return  string
     *
     * @deprecated 4.0 Use directly the returned code
     */
    public function onSetContent($id, $html)
    {
        return 'Joomla.editors.instances[' .
            json_encode($id) .
            '].setValue(' .
            json_encode($html) .
            ');';
    }

    /**
     * Display the editor area.
     *
     * @param string $name The control name.
     * @param string $content The contents of the text area.
     * @param string $width The width of the text area (px or %).
     * @param string $height The height of the text area (px or %).
     * @param integer $col The number of columns for the textarea.
     * @param integer $row The number of rows for the textarea.
     * @param boolean $buttons True and the editor buttons will be displayed.
     * @param string $id An optional ID for the textarea (note: since 1.6). If
     *   not supplied the name is used.
     * @param string $asset The object asset
     * @param object $author The author.
     * @param array $params Associative array of editor parameters.
     *
     * @return  string
     */
    public function onDisplay(
        $name,
        $content,
        $width,
        $height,
        $col,
        $row,
        $buttons = true,
        $id = null,
        $asset = null,
        $author = null,
        $params = []
    ) {
        if (empty($id)) {
            $id = $name;
        }

        $editor =
            '
            <div class="n1ed-field-wrapper">
                    <textarea class="n1ed-field" data-n1ed="true" name="' .
            $name .
            '" id="' .
            $id .
            '">' .
            $content .
            '</textarea>
            </div>
        ';

        return $editor;
    }

    /**
     * Displays the editor buttons.
     *
     * @param string $name The control name.
     * @param mixed $buttons [array with button objects | boolean true to display
     *   buttons]
     * @param string $asset The object asset
     * @param object $author The author.
     *
     * @return  void|string HTML
     */
    public function _displayButtons($name, $buttons, $asset, $author)
    {
        if (is_array($buttons) || (is_bool($buttons) && $buttons)) {
            $buttons = $this->_subject->getButtons(
                $name,
                $buttons,
                $asset,
                $author
            );

            return JLayoutHelper::render('joomla.editors.buttons', $buttons);
        }
    }

    /**
     * Helper for get info about extensions.
     *
     * @param string $element The extension name.
     * @param string $type The extension type (plugin, module, component).
     * @param string $folder The extension folder (editors, captcha...).
     */
    private function getItemByElement($element, $type, $folder = null)
    {
        $dbo = JFactory::getDbo();
        $query = $dbo->getQuery(true);
        $query->select('extension_id, manifest_cache');
        $query->from('#__extensions');

        $query->where('element = ' . $dbo->quote($element));
        $query->where('type = ' . $dbo->quote($type));

        if ($type == 'plugin' && !empty($folder)) {
            $query->where('folder = ' . $dbo->quote($folder));
        }

        $dbo->setQuery($query);
        $extensionRecord = $dbo->loadAssoc();

        if (!is_null($extensionRecord)) {
            $manifestData = json_decode($extensionRecord['manifest_cache']);
            $extensionRecord['manifest'] = $manifestData;
        }

        return $extensionRecord;
    }
}
