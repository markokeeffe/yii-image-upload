<?php
/**
 * Author:  Mark O'Keeffe

 * Date:    01/10/13
 *
 * [Free Stuff World] ImageUploadController.php
 */

class ImageUploadController extends CExtController {

  /**
   * @var \MOK\ImageUpload\Repositories\DatabaseRepository
   */
  protected $db;

  /**
   * @var \MOK\ImageUpload\Images
   */
  protected $images;

  public function init()
  {
    // Get the DB repo
    $this->db = Yii::app()->DatabaseRepository;
    // Get the VImage class
    $this->images = Yii::app()->Images;
  }

  /**
   * Load the image upload form
   *
   * @throws CHttpException
   */
  public function actionForm($imageable_type, $imageable_id=null, $insert_into=null)
  {

    // Set up some HTML5 'data-' attributes for the uploader form
    $formData = array(
      'data-imageable-type' => $imageable_type,
    );
    if ($imageable_id) {
      $formData['data-imageable-id'] = $imageable_id;
    }
    if ($insert_into) {
      $formData['data-insert-into'] = $insert_into;
    }

    $modal = array(
      'heading' => 'Upload Image',
      'body' => $this->renderPartial('_uploadForm', compact('formData', 'insert_into'), true),
    );

    $this->ajaxReturn('modal', $modal);
  }

  /**
   * Upload an image file and save it in the appropriate directory
   *
   * @throws CHttpException
   */
  public function actionUpload()
  {
    // Check that a file has been uploaded, and that the crop dimensions exist
    if (!isset($_FILES['image'])) {
      throw new CHttpException(400, 'Unable to upload. No image provided.');
    }

    // Get the file object for the uploaded image
    $file = CUploadedFile::getInstanceByName('image');

    $imagePath = $file->getTempName();
    $ext = $file->getExtensionName();
    $mime = $file->getType();

    $this->processImage($imagePath, $ext, $mime);
  }

  /**
   * Download an image file from a URL and save it in the appropriate directory
   *
   * @throws CHttpException
   */
  public function actionDownload()
  {
    // Check that a file has been uploaded, and that the crop dimensions exist
    if (!isset($_POST['url']) || !$url = $_POST['url']) {
      throw new CHttpException(400, 'Unable to download. No image URL provided.');
    }

    try {
      if (preg_match('/^data:image\/([^;]+);base64,(.+)/', $url, $matches)) {
        $image = $this->images->downloadBase64($matches[1], $matches[2]);
      } else {
        $image = $this->images->downloadFromUrl($url);
      }

    } catch (Exception $e) {
      throw new CHttpException(500, 'Unable to download. '.$e->getMessage());
    }

    $this->processImage($image['path'], $image['ext'], $image['mime']);
  }

  /**
   * Save a specified focal point x & y co-ords to the 'images' table in the DB
   *
   * @param $id
   *
   * @throws CHttpException
   */
  public function actionFocalPoint($id)
  {
    // Get the content type name
    if (!isset($_POST['focalPoint']) || !$focalPoint = $_POST['focalPoint']) {
      throw new CHttpException(400, 'Unable to save focal point. No co-ordinates provided');
    }

    $image = $this->db->update($id, array(
      'focal_point' => $focalPoint,
    ));

    $this->ajaxReturn('success', array(
      'id' => $id,
      'src' => $this->images->getOriginalUrl($image, true),
    ));

  }

  /**
   * Crop, resize and save an image file
   *
   * @param $imagePath
   * @param $ext
   * @param $mime
   *
   * @throws CHttpException
   */
  public function processImage($imagePath, $ext, $mime)
  {
    // Get the content type name
    if (!isset($_POST['imageableType']) || !$imageableType = $_POST['imageableType']) {
      throw new CHttpException(400, 'Unable to upload. Invalid image owner class.');
    }

    // Get the crop dimensions from the POSTed data
    if (!isset($_POST['crop']) || !$crop = $_POST['crop']) {
      throw new CHttpException(400, 'Unable to upload. No co-ordinates provided.');
    }

    // Build image processing data
    $data = array(
      'ext' => $ext,
      'mime' => $mime,
      'imageable_type' => $imageableType,
    );

    // Has image meta data been posted?
    if (isset($_POST['meta']) && is_array($_POST['meta'])) {
      // Merge the image data array with the posted meta data
      $data = array_merge($data, $_POST['meta']);
    }

    // Has an image owner ID been provided? Set it
    if (isset($_POST['imageableId'])) {
      $data['imageable_id'] = $_POST['imageableId'];
    }

    $data['local_sizes'] = '';
    $data['remote_sizes'] = '';

    // Has a size been specified? Add it to the save data
    $size =  (isset($_POST['size']) ? $_POST['size'] : '');

    // Has an image ID been submitted? Does an image exist with that ID?
    if (isset($_POST['imageId'])) {
      // Delete the image and we will create a new one in its place
      $this->db->delete($_POST['imageId']);
    }

    // Add the image to the database, saving the extension and 'imageable type'
    $image = $this->db->add($data);

    // Build a save path for this image file
    $savePath = $this->images->getSavePath(
      strtolower($imageableType), // Directory name e.g. 'user'
      $image->id, // Numerical ID (used as image name)
      $ext // File extension (jpg|gif|png)
    );

    // Save the uploaded file to the save path, cropping & resizing if necessary
    $this->images->saveUploaded(
      $savePath,
      $imagePath,
      $crop,
      $size
    );

    // Get the public URL to the image file with a cache busting var
    $publicUrl = $this->images->getOriginalUrl($image, true);

    // Return the ID and public image URL to the browser to set the focal point
    $params = array(
      'id' => $image->id,
      'src' => $publicUrl,
    );

    // Do we need to insert the image into a TinyMCE editor window?
    if (isset($_POST['insertInto'])) {
      $params['src'] = $this->images->getUrl($image, $size);
      $params['insertInto'] = $data['imageable_type'].'_'.$_POST['insertInto'];
      $params['alt'] = $data['alt'];
      $params['align'] = $data['align'];
    }

    // Is the size of the image fixed? No need for a focal point
    if (isset($_POST['size'])) {
      $this->db->update($image->id, compact('size'));
      $this->ajaxReturn('success', $params);
    }

    $modal = array(
      'heading' => 'Set Focal Point',
      'body' => $this->renderPartial('_focalPoint', $params, true),
    );

    $this->ajaxReturn('modal', $modal);
  }

  /**
   * Return a 500 error, or a JSON string to the browser,
   * depending on the return type
   *
   * @param string $type The return type (success, error, ...)
   * @param string $output The output content
   *
   */
  private function ajaxReturn($type, $output)
  {

    if ($type == 'error') {
      header('HTTP/1.1 500 Magic AJAX Error 4000');
      echo (is_string($output) ? $output : print_r($output, true));
    } else {
      header('Cache-Control: no-cache, must-revalidate');
      header("Content-type: application/json");

      echo json_encode(array(
        'type' => $type,
        'msg' => $output,
      ));
    }

    Yii::app()->end();

  }

}
