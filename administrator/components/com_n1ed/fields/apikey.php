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
 * Form Field class for the N1ED API Key.
 *
 * @package     Joomla
 */
class JFormFieldApikey extends JFormField
{
    /**
     * The form field type.
     *
     * @var    string
     * @since  1.7.0
     */
    protected $type = 'Text';

    /**
     * Name of the layout being used to render the field
     *
     * @var    string
     * @since  3.7
     */
    protected $layout = 'joomla.form.field.text';

    /**
     * Method to get certain otherwise inaccessible properties from the form field object.
     *
     * @param string $name The property name for which to get the value.
     *
     * @return  mixed  The property value or null.
     *
     * @since   3.2
     */
    public function __get($name)
    {
        return parent::__get($name);
    }

    /**
     * Method to set certain otherwise inaccessible properties of the form field object.
     *
     * @param string $name The property name for which to set the value.
     * @param mixed $value The value of the property.
     *
     * @return  void
     *
     * @since   3.2
     */
    public function __set($name, $value)
    {
        parent::__set($name, $value);
    }

    /**
     * Method to attach a JForm object to the field.
     *
     * @param SimpleXMLElement $element The SimpleXMLElement object representing the <field> tag for the form field object.
     * @param mixed $value The form field value to validate.
     * @param string $group The field name group control value. This acts as an array container for the field.
     *                                      For example if the field has name="foo" and the group value is set to "bar" then the
     *                                      full field name would end up being "bar[foo]".
     *
     * @return  boolean  True on success.
     *
     * @see     JFormField::setup()
     * @since   3.2
     */
    public function setup(SimpleXMLElement $element, $value, $group = null)
    {
        return parent::setup($element, $value, $group);
    }

    /**
     * Method to get the field input markup.
     *
     * @return  string  The field input markup.
     *
     * @since   1.7.0
     */
    protected function getInput()
    {
        $layout_data = $this->getLayoutData();

        $api_key = $layout_data['value'];
        $token = null;
        $i = strpos($api_key, ':');
        if ($i !== false) {
            $token = substr($api_key, $i + 1);
            $api_key = substr($api_key, 0, $i);
        }

        return '
            <div 
                id="n1ed-conf"
                style="border: 1px solid #c0c0c0; border-radius: 3px;padding: 70px; display: flex; justify-content: center; align-items: center; background-color: #fcfcfa"
            >
                <svg xmlns:svg="http://www.w3.org/2000/svg" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.0" width="16px" height="16px" viewBox="0 0 128 128" xml:space="preserve"><g><path d="M75.4 126.63a11.43 11.43 0 0 1-2.1-22.65 40.9 40.9 0 0 0 30.5-30.6 11.4 11.4 0 1 1 22.27 4.87h.02a63.77 63.77 0 0 1-47.8 48.05v-.02a11.38 11.38 0 0 1-2.93.37z" fill="#007FFF"/><animateTransform attributeName="transform" type="rotate" from="0 64 64" to="360 64 64" dur="800ms" repeatCount="indefinite"></animateTransform></g></svg>
                <div style="margin-left:10px">Loading N1ED control panel...</div>
            </div>
            <div style="display:none!important;">' .
            $this->getRenderer($this->layout)->render($layout_data) .
            '
                
                <style>
                    .N1EDCmsConf {
                        border-color: transparent;
                        background-color: white;
                    }                
                    
                    .n1ed-widget-with-error__error {
                        font-size: 13px !important;
                    }
                </style>
                
                <script>
                    var elInput = document.getElementById("jform_api_key");
                    elInput.parentNode.parentNode.setAttribute("style", "margin-left:0!important");
                    elInput.parentNode.parentNode.previousElementSibling.setAttribute("style", "display:none!important");
                    
                    var funcSave = null;
                    
                    var elForm = document.querySelector(\'#component-form\');
                    elForm.addEventListener("submit", function(event) {
                      if (!funcSave)
                        return; // N1ED configuration panel probably was not loaded yet.
                      event.preventDefault();
                      funcSave(function(isOk) {
                        if (isOk)
                          elForm.submit();
                      });
                    });
                </script>
                
                <script 
                    src="//' .
            (isset($_COOKIE['N1ED_PREFIX'])
                ? $_COOKIE['N1ED_PREFIX'] . '.'
                : '') .
            'n1ed.com/js/n1ed-cms-conf-3.js" 
                    onload=\'{
                    
                        var elConf = document.getElementById("n1ed-conf");
                        elConf.setAttribute("style", "");
                        elConf.innerHTML = "";
                        
                        funcSave = window.attachN1EDCmsConf({
                          el: elConf,
                          urlSetApiKeyAndToken: true,
                          apiKey: "' .
            $api_key .
            '",
                          token: ' .
            ($token == null ? 'null' : '"' . $token . '"') .
            ',
                          editorName: "tinymce",
                          integration: "joomla4",
                          integrationType: "n1ed",
                          isCheckBoxN1EDEcoEnabledVisible: false,
                          checkBoxN1EDEcoEnabledTitle: null,
                          checkBoxN1EDEcoEnabledValue: null,
                          onN1EDEcoEnabledChange: function (value, onFinished) {},
                          onApiKeyChange: function (apiKey, token) {
                            var elInput = document.getElementById("jform_api_key");
                            elInput.value = apiKey + (token ? (":" + token) : "");
                            Joomla.submitbutton("component.apply");
                          }
                        });
                        
                    }\'
                    onerror=\'{
                        var elConf = document.getElementById("n1ed-conf");
                        
                        elConf.innerHTML = "<svg role=\"img\" xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 352 512\" width=\"12\" height=\"12\"><path fill=\"#fe5c4b\" d=\"M242.72 256l100.07-100.07c12.28-12.28 12.28-32.19 0-44.48l-22.24-22.24c-12.28-12.28-32.19-12.28-44.48 0L176 189.28 75.93 89.21c-12.28-12.28-32.19-12.28-44.48 0L9.21 111.45c-12.28 12.28-12.28 32.19 0 44.48L109.28 256 9.21 356.07c-12.28 12.28-12.28 32.19 0 44.48l22.24 22.24c12.28 12.28 32.2 12.28 44.48 0L176 322.72l100.07 100.07c12.28 12.28 32.2 12.28 44.48 0l22.24-22.24c12.28-12.28 12.28-32.19 0-44.48L242.72 256z\"></path></svg>" +
                            "<div style=\"margin-left:10px\">Unable to load N1ED configuration panel. Please try to reload page.</div>";
                    }\'
                ></script>
        </div>
        ';
    }

    /**
     * Method to get the data to be passed to the layout for rendering.
     *
     * @return  array
     *
     * @since 3.7
     */
    protected function getLayoutData()
    {
        return parent::getLayoutData();
    }
}
