<?php
/**
 * Author:  Mark O'Keeffe

 * Date:    02/10/13
 *
 * [Free Stuff World] _uploadForm.php
 */

echo CHtml::form('', 'post', array(
  'id' => 'VImageUploaderForm',
  'role' => 'form',
  'enctype' => 'multipart/form-data',
  'class' => 'form-horizontal',
  'data-behavior' => 'hitTarget',
  'data-listener' => 'submit',
  'data-action' => 'click',
  'data-selector' => '#VimageUploader-url-button',
)); ?>

  <?php if ($insert_into) : ?>
    <div class="form-group">
      <label class="control-label col-md-3" for="VImageUploader-alt">Alt</label>
      <div class="col-md-8">
        <?php echo CHtml::textField('alt', '', array(
          'id' => 'VImageUploader-alt',
          'class' => 'form-control',
          'data-image-meta' => '1',
        )); ?>
      </div>

    </div>
    <div class="form-group">
      <label class="control-label col-md-3" for="VImageUploader-caption">Caption</label>
      <div class="col-md-8">
        <?php echo CHtml::textField('caption', '', array(
          'id' => 'VImageUploader-caption',
          'class' => 'form-control',
          'data-image-meta' => '1',
        )); ?>
      </div>

    </div>
    <div class="form-group">
      <label class="control-label col-md-3" for="VImageUploader-align">Alignment</label>
      <div class="col-md-8">
        <?php echo CHtml::dropDownList('align', '', array(
          'left' => 'Left',
          'center' => 'Center',
          'right' => 'Right',
        ), array(
          'id' => 'VImageUploader-align',
          'class' => 'form-control',
          'data-image-meta' => '1',
        )); ?>
      </div>

    </div>
  <?php endif; ?>

  <?php echo CHtml::fileField('imageFile', '', array_merge(array(
    'class' => 'file_upload hidden',
    'id' => 'VImageUploader-file',
    'data-behavior' => 'loadImageFile',
    'data-action' => $this->createUrl('vimage/upload'),
  ), $formData)); ?>

  <div class="form-group">
    <label class="col-md-3 control-label"
           for="VImageUploader-file-placeholder">Choose a file: </label>

    <div class="col-md-8">
      <div class="input-group">


        <?php echo CHtml::textField('', '', array(
          'id' => 'VImageUploader-file-placeholder',
          'class' => 'form-control file_placeholder',
          'data-behavior' => 'hitTarget',
          'data-listener' => 'focus',
          'data-action' => 'click',
          'data-selector' => '#VImageUploader-file',
        )); ?>
        <span class="input-group-btn">
        <?php echo CHtml::htmlButton('Upload', array(
          'class' => 'btn btn-default btn-md',
          'data-behavior' => 'hitTarget',
          'data-listener' => 'click',
          'data-action' => 'click',
          'data-selector' => '#VImageUploader-file',
        )); ?>
      </span>
      </div>
    </div>

  </div>
  <div class="form-group">
    <label class="col-md-3 control-label"
           for="VImageUploader-url">Paste a URL: </label>

    <div class="col-md-8">
      <div class="input-group">

        <?php echo CHtml::textField('url', '', array_merge(array(
          'id' => 'VImageUploader-url',
          'class' => 'form-control',
          'data-behavior' => 'loadImageURL',
          'data-action' => $this->createUrl('vimage/download'),
        ), $formData)); ?>
        <span class="input-group-btn">
        <?php echo CHtml::htmlButton('Download', array(
          'class' => 'btn btn-default btn-md',
          'data-behavior' => 'loadImageURL',
          'data-selector' => '#VImageUploader-url',
        )); ?>
      </span>
      </div>
    </div>

  </div>

  <button type="submit" class="btn btn-success modal-footer-btn">Go!</button>

<?php echo CHtml::endForm(); ?>
