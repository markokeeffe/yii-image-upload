<?php
/**
 * Author:  Mark O'Keeffe

 * Date:    02/10/13
 *
 * [Free Stuff World] _focalPoint.php
 */
?>
<div class="row">
  <div class="col-md-12 focal-button-container">
    <button class="btn btn-success" id="VImageUploader-set-focal" style="display: none">Set Focal Point</button>
  </div>
</div>


<div class="row">
  <div class="col-md-12 focal-container">
    <img src="<?php echo $src; ?>" alt="Set focal point"
         data-behavior="focalPoint resizeModal"
         data-button="#VImageUploader-set-focal"
         data-action="<?php echo $this->createUrl('vimage/focalPoint', array(
          'id' => $id,
        )); ?>"/>
  </div>
</div>
