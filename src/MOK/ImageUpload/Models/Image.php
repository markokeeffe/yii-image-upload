<?php namespace MOK\ImageUpload\Models;

use CActiveRecord;

/**
 * Image Model
 *
 * This is the model class for table "images".
 *
 * Columns for 'images':
 * @property integer $id
 * @property string $imageable_type
 * @property string $imageable_id
 * @property string $ext
 * @property string $mime
 * @property string $focal_point
 * @property string $local_sizes
 * @property string $remote_sizes
 * @property string $alt
 * @property string $caption
 * @property string $align
 * @property integer $status
 * @property string $created_at
 * @property string $updated_at
 */
class Image extends CActiveRecord {

  /**
   * Returns the static model of the specified AR class.
   * @param string $className active record class name.
   * @return Image the static model class
   */
  public static function model($className=__CLASS__)
  {
    return parent::model($className);
  }

  /**
   * @return string the associated database table name
   */
  public function tableName()
  {
    return 'images';
  }

  /**
   * @return array validation rules for model attributes.
   */
  public function rules()
  {
    // NOTE: you should only define rules for those attributes that
    // will receive user inputs.
    return array(
      array('ext, mime', 'required'),
      array('status', 'numerical', 'integerOnly'=>true),
      array('imageable_type, alt', 'length', 'max'=>255),
      array('imageable_id', 'length', 'max'=>10),
      array('ext', 'length', 'max'=>3),
      array('align', 'length', 'max'=>6),
      array('mime', 'length', 'max'=>16),
      array('focal_point', 'length', 'max'=>11),
      array('local_sizes, remote_sizes, caption', 'length', 'max'=>1023),
      array('created_at, updated_at', 'safe'),
    );
  }

  /**
   * Set the timestamps before validation fires
   *
   * @return bool
   */
  public function beforeValidate()
  {
    if ($this->scenario === 'insert') {
      $this->created_at = date('Y-m-d H:i:s');
    }
    $this->updated_at = date('Y-m-d H:i:s');
    return parent::beforeValidate();
  }
}
