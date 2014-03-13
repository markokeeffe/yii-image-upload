<?php namespace MOK\ImageUpload;

class InterventionImageManipulation extends \CApplicationComponent implements ImageManipulationInterface {

  /**
   * The Intervention Image class
   *
   * @var \Intervention\Image\Image
   */
  public $i;

  /**
   * Make a new image instance
   *
   * @param $path
   *
   * @return \Intervention\Image\Image
   */
  public function make($path)
  {
    // Return the instance
    return $this->i = $this->i->make($path);
  }

  /**
   * Make an image instance from base64 encoded string
   *
   * @param $mime
   * @param $base64
   *
   * @throws \Exception
   * @return \Intervention\Image\Image
   */
  public function makeFromBase64($mime, $base64)
  {

    $data = base64_decode($base64);
    return $this->i = $this->i->make($data);

  }

  /**
   * Save the image instance to file
   *
   * @param     $path
   * @param int $quality
   *
   * @return \Intervention\Image\Image
   */
  public function save($path, $quality=90)
  {
    $this->i = $this->i->save($path, $quality);
    chmod($path, 0777);
    return $this->i;
  }

  /**
   * Perform a crop with the specified dimensions
   *
   * @param int $w
   * @param int $h
   * @param int $x
   * @param int $y
   *
   * @return \Intervention\Image\Image
   */
  public function crop($w, $h, $x=0, $y=0)
  {
    return $this->i = $this->i->crop($w, $h, $x, $y);
  }

  /**
   * Perform a resize with the specified dimensions
   *
   * @param int $w
   * @param int $h
   *
   * @return \Intervention\Image\Image
   */
  public function resize($w, $h)
  {
    return $this->i = $this->i->resize($w, $h);
  }

  /**
   * Return the width of the current image instance
   *
   * @return int
   */
  public function getWidth()
  {
    return $this->i->width;
  }

  /**
   * Return the height of the current image instance
   *
   * @return int
   */
  public function getHeight()
  {
    return $this->i->height;
  }

  /**
   * Return the raw base64 encoded image data
   */
  public function getRawData()
  {
    return 'data:'.$this->i->mime.';base64,'.base64_encode((string) $this->i);
  }

  public function getPath()
  {
    return $this->i->dirname.$this->i->filename;
  }

  public function getExt()
  {
    return $this->i->extension;
  }

  public function getMime()
  {
    return $this->i->mime;
  }


}
