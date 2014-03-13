<?php namespace MOK\ImageUpload;

interface ImageManipulationInterface
{

  public function make($path);

  public function makeFromBase64($mime, $base64);

  public function save($path, $quality=90);

  public function crop($w, $h, $x, $y);

  public function resize($w, $h);

  public function getWidth();

  public function getHeight();

  public function getRawData();

  public function getPath();

  public function getExt();

  public function getMime();
}
