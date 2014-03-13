<?php
/**
 * Author:  Mark O'Keeffe

 * Date:    01/10/13
 *
 * [Free Stuff World] VImageUpload.php
 */

class ImageUpload extends CWidget {

  /**
   * Run the widget
   */
  public function run()
  {
    // Publish the widget's assets
    $this->registerClientScript();

    $this->render('modal');
  }

  /**
   * Publish assets (re-publish on every request if YII_DEBUG is true)
   *
   * @throws CHttpException
   */
  protected function registerClientScript()
  {
    $cs=Yii::app()->clientScript;
    // Set the assets directory for this extension
    $assets = dirname(__FILE__).DS.'..'.DS.'public'.DS.'assets';
    // Set the directory to the Javascript behaviours
    $behaviours = $assets.DS.'js'.DS.'behaviors'.DS;

    // Is the assets directory valid?
    if (is_dir($assets)) {

      // Create a base URL for these assets using asset manager
      $baseUrl = Yii::app()->assetManager->publish($assets, false, -1, YII_DEBUG);

      $cs->registerCssFile($baseUrl.'/css/jquery.Jcrop.css');
      $cs->registerCssFile($baseUrl.'/css/v.image-upload.css');
      $cs->registerScriptFile($baseUrl.'/js/functions.js', CClientScript::POS_END);
      $cs->registerScriptFile($baseUrl.'/js/jquery.Jcrop.js', CClientScript::POS_END);

      $cs->registerCss('ImageUpload', "
        .jcrop-vline,
        .jcrop-hline {
          background: white url('".Yii::app()->createAbsoluteUrl($baseUrl.'/css/Jcrop.gif')."') top left repeat;
        }
       ");

      // Process the behaviours directory
      foreach (glob($behaviours.'*') as $jsFile) {
        $jsFile = str_replace($behaviours, '', $jsFile);
        $cs->registerScriptFile($baseUrl.'/js/behaviors/'.$jsFile, CClientScript::POS_END);
      }

    } else {
      throw new CHttpException(500, __CLASS__ . ' - Error: Couldn\'t find assets to publish.');
    }

  }

}
