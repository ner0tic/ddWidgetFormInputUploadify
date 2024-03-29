<?php

/**
 * ddWidgetFormInputUploadify class
 * 
 * This provides file upload widget for file uploads with the Uploadify
 * javascript library.
 *
 * @package ddWidgetFormInputUploadifyPlugin
 * @author David Durost <david.durost@gmail.com>
 * @author Chris LeBlanc <chris@webPragmatist.com>
 * @see 
 */
class ddWidgetFormInputUploadify extends sfWidgetFormInputFile
{
  /**
   * Instance counter
   *
   * @var integer
   */
  protected static $INSTANCE_COUNT = 0;

  /**
   * @param array $options     An array of options
   * @param array $attributes  An array of default HTML attributes
   *
   * @see sfWidgetFormInput
   */
  protected function configure($options = array(), $attributes = array())
  {
    parent::configure($options, $attributes);
  }

  /**
   * Gets the JavaScript paths associated with the widget.
   *
   * @return array An array of JavaScript paths
   */
  public function getJavaScripts()
  {
    $js = array(
      sfConfig::get('app_ddWidgetFormInputUploadifyPlugin_uploadify_path') . '/swfobject.js',
      sfConfig::get('app_ddWidgetFormInputUploadifyPlugin_uploadify_path') . '/jquery.uploadify.min.js'
    );

    if($this->getOption('include_jquery'))
      $js[] = "http://ajax.googleapis.com/ajax/libs/jquery/1.7/jquery.min.js";
      
    return $js;
  }

  public function render($name, $value = null, $attributes = array(), $errors = array())
  {
    self::$INSTANCE_COUNT++;

    $output = parent::render($name, $value, $attributes, $errors);

    $widget_id  = $this->getAttribute('id') ? $this->getAttribute('id') : $this->generateId($name);
    $session_name = ini_get('session.name');
    $session_id = session_id();
    
    $uploader = sfConfig::get('app_ddWidgetFormInputUploadifyPlugin_uploadify_path').'/'.sfConfig::get('app_ddWidgetFormInputUploadifyPlugin_uploader');
    $cancel_img = sfConfig::get('app_ddWidgetFormInputUploadifyPlugin_uploadify_path').'/'.sfConfig::get('app_ddWidgetFormInputUploadifyPlugin_cancel_img');
    
    $sim_upload_limit = sfConfig::get('app_ddWidgetFormInputUploadifyPlugin_sim_upload_limit');
    $display_data = sfConfig::get('app_ddWidgetFormInputUploadifyPlugin_display_data');
    $auto = sfConfig::get('app_ddWidgetFormInputUploadifyPlugin_auto');
    $multi = sfConfig::get('app_ddWidgetFormInputUploadifyPlugin_multi');
    
    $form = new BaseForm();
    $csrf_token = $form->getCSRFToken();
    
    $output .= <<<EOF
      <div class="swfupload-buttontarget">
        <noscript>
          We're sorry.  SWFUpload could not load.  You must have JavaScript enabled to enjoy SWFUpload.
        </noscript>
      </div>
      <script type="text/javascript">
        //<![CDATA[
        $(document).ready(function() {
          $('#$widget_id').uploadify({
            'scriptData': {'$session_name':'$session_id', '_csrf_token':'$csrf_token'},
            'uploader': '$uploader',
            'cancelImg': '$cancel_img',
            'auto'      : $auto,
            'script': $('#$widget_id').closest('form').attr('action')+'/upload',
            'folder': '/',
            'multi': $multi,
            'displayData': '$display_data',
            'fileDataName': '$widget_id',
            'simUploadLimit': $sim_upload_limit
          });
        });
        //]]>
      </script>
EOF;
    return $output;
  }
}