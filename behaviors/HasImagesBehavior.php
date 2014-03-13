<?php
/**
 * Author:  Mark O'Keeffe
 * Company: Veneficus Ltd.
 * Date:    02/10/13
 *
 * [Free Stuff World] HasImagesBehavior.php
 */

class HasImagesBehavior extends CActiveRecordBehavior {

  /**
   * Image model instances belonging to the owner of this behaviour
   *
   * @var array
   */
  private $_images;

  /**
   * Get the first image belonging to the owner of this behaviour
   *
   * @return bool|Image
   */
  public function getPrimaryImage()
  {
    $images = $this->getImages();
    if (isset($images[0])) {
      return $images[0];
    }
    if (isset($this->owner->image_id) && $this->owner->image_id) {
      return $this->getImage($this->owner->image_id);
    }
    return false;
  }

  /**
   * Get the images belonging to the owner of this behaviour
   *
   * @return array|Image[]
   */
  public function getImages()
  {
    if (is_null($this->_images)) {
      $images = array();
      if ($this->owner->id) {
        $images = Image::model()->findAll(array(
          'condition' => 'imageable_type = :imageable_type AND imageable_id = :imageable_id',
          'params' => array(
            ':imageable_type' => get_class($this->owner),
            ':imageable_id' => $this->owner->id,
          ),
          'order' => 'id DESC',
        ));
      }
      $this->_images = $images;
    }

    return $this->_images;

  }

  public function getImage($id)
  {
    return Image::model()->findByPk($id);
  }

  /**
   * Add an image to the owner of this behaviour by finding a saved image
   * with the owner's temporary '$image_id' attribute and updating it with
   * the ID of the owner. E.g. Content with ID of 24.
   */
  public function addImage()
  {
    // Find the image using the ID
    if ($image = Image::model()->findByPk($this->owner->image_id)) {
      // Update it so it belongs to this content
      $image->imageable_id = $this->owner->id;
      $image->validate();
      $image->save(false, array(
        'imageable_id',
      ));
    }
  }

}