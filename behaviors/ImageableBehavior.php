<?php
/**
 * Author:  Mark O'Keeffe
 * Company: Veneficus Ltd.
 * Date:    03/10/13
 *
 * [Yii Workbench] ImageableBehavior.php
 */

class ImageableBehavior  extends CActiveRecordBehavior {

  /**
   * Return the related model with 'imageable_type' and 'imageable_id'
   *
   * @return bool
   */
  public function getImageable()
  {
    if (!$this->owner->imageable_type || !class_exists($this->owner->imageable_type)) {
      return false;
    }
    $class = new $this->owner->imageable_type;
    return $class->findByPk($this->owner->imageable_id);
  }

}