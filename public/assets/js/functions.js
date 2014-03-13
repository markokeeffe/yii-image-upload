/**
 * Author:  Mark O'Keeffe

 * Date:    24/09/13
 *
 * [Laravel Workbench]
 */

/**
 * Add any image meta data from the form to the POST data
 * @param $form
 */
MOK.Functions.addFormData = function($form) {

  var $fields = $form.find('[data-image-meta="1"]');

  if ($fields.length) {

    MOK.ImageUpload.params.meta = {};

    $fields.each(function(){
      var $this = $(this);
      MOK.ImageUpload.params.meta[$this.attr('name')] = $this.val();
    });

  }

};

/**
 * Load the image data using the FileReader API and display it for cropping
 */
MOK.Functions.loadImageFile = function(input){

  // Get the file object
  var $input = $(input),
    file = input.files[0],
    reader,
    source,
    $img;

  // Check the MIME type of the file to ensure it is an image
  if(!file.type.match(/image\.*/)){
    this.resetForm();
    alert('Invalid file type. Please upload image files only.');
    return;
  }

  // Read the file data and show a preview of the image for cropping
  if(window.FileReader){
    reader = new FileReader();
    reader.onloadend = function (e) {

      source = e.target.result;
      $img = $('<img/>', {
        src: source,
        alt: 'Set Crop',
        "data-behavior": 'crop resizeModal',
        "data-action": $(input).data('action')
      });

      MOK.ImageUpload.modal({
        heading: 'Choose Crop',
        body: $img
      });

    };
    // Create URL data from the file for uploading
    reader.readAsDataURL(file);
  }

  // Create a FormData object to add the file upload to
  MOK.ImageUpload.formdata = false;
  if(window.FormData){
    MOK.ImageUpload.formdata = new FormData();
    MOK.ImageUpload.formdata.append('image', file);

    // Set the 'imageable_type' (the class that owns the image)
    MOK.ImageUpload.formdata.append('imageableType', $input.data('imageableType'));

    // If provided, set the 'imageable_id' (The ID of the image owner)
    if (undefined !== $input.data('imageableId')) {
      MOK.ImageUpload.formdata.append('imageableId', $input.data('imageableId'));
    }
    // If provided, set the 'insert_into' (The name of an area to insert a saved image)
    if (undefined !== $input.data('insertInto')) {
      MOK.ImageUpload.formdata.append('insertInto', $input.data('insertInto'));
    }
    if (undefined !== MOK.ImageUpload.params.imageId) {
      MOK.ImageUpload.formdata.append('imageId', MOK.ImageUpload.params.imageId);
    }
    if (undefined !== MOK.ImageUpload.params.size) {
      MOK.ImageUpload.formdata.append('size', MOK.ImageUpload.params.size);
    }
  }
};

/**
 * Download an image from a URL to crop and resize
 *
 * @param $input
 */
MOK.Functions.loadImageURL = function($input){

  // Get the URL from the text input
  var url = $input.val(),
    $img;

  // Set up the formdata object for the AJAX submit of the crop later
  MOK.ImageUpload.formdata = {
    url: url,
    imageableType: $input.data('imageableType')
  };

  if (undefined !== $input.data('imageableId')) {
    MOK.ImageUpload.formdata.imageableId = $input.data('imageableId');
  }
  if (undefined !== $input.data('insertInto')) {
    MOK.ImageUpload.formdata.insertInto = $input.data('insertInto');
  }
  if (undefined !== MOK.ImageUpload.params.imageId) {
    MOK.ImageUpload.formdata.imageId = MOK.ImageUpload.params.imageId;
  }
  if (undefined !== MOK.ImageUpload.params.size) {
    MOK.ImageUpload.formdata.size = MOK.ImageUpload.params.size;
  }

  // Attempt to load an image from the given URL
  $img = $('<img/>', {
    src: url,
    alt: 'Set Crop',
    "data-behavior": 'crop resizeModal',
    "data-action": $input.data('action'),
    load: function(){
      // Image has successfully loaded, open a modal with
      // a clone of the image for cropping
      MOK.ImageUpload.modal({
        heading: 'Choose Crop',
        body: $img.clone()
      });
    },
    error: function(){
      // The URL has not loaded a valid image, show an error
      MOK.ImageUpload.modal({
        heading: 'Invalid Image',
        body: '<strong>You have attempted to download an invalid image. Please try again.</strong>'
      });
    }
  });

};

/**
 * Save the selected crop dimensions by POSTing the
 * image data and crop co-ordinates to the server
 */
MOK.Functions.saveCrop = function($image) {

  // Set up some AJAX request settings
  var settings = {
    url: $image.data('action'),
    success: function(rtn) {
      if (rtn.type === 'modal') {
        MOK.ImageUpload.modal(rtn.msg);
      } else if (rtn.type === 'success') {
        MOK.ImageUpload.modalClose();
        MOK.Functions.complete(rtn.msg);
      }
    }
  };

  // Is 'formdata' a normal object?
  if ($.isPlainObject(MOK.ImageUpload.formdata)) {
    // Add the crop dimensions
    MOK.ImageUpload.formdata.crop = MOK.crop;
    // Add the data to the AJAX settings
    settings.data = MOK.ImageUpload.formdata;
    // Add any image meta data if there is any
    if (undefined !== MOK.ImageUpload.params.meta) {
      settings.data.meta = MOK.ImageUpload.params.meta;
    }
  } else {
    // Add the crop dimensions
    $.each(MOK.crop, function(i,elem){
      MOK.ImageUpload.formdata.append('crop['+i+']',elem);
    });

    // Add any image meta data if there is any
    if (undefined !== MOK.ImageUpload.params.meta) {
      $.each(MOK.ImageUpload.params.meta, function(i,elem){
        MOK.ImageUpload.formdata.append('meta['+i+']',elem);
      });
    }

    // Add the data to the AJAX settings
    settings.data = MOK.ImageUpload.formdata;

    // Set some extra settings to use the FormData object
    settings.processData = false;  // tell jQuery not to process the data
    settings.contentType = false;  // tell jQuery not to set contentType
  }

  $.ajax(settings);

};

/**
 * When the image loads for cropping, a 'Save Crop' button needs to be added
 *
 * @param $image
 */
MOK.Functions.addCropButton = function($image){
  // Create a button that can save the crop
  var $button = $('<button/>', {
    "class": 'btn btn-success'
  }).text('Save Crop');

  // Add the button to the modal
  $image.closest('.modal-body').prepend($button);

  $button.on('click', function(){
    MOK.Functions.saveCrop($image);
  });
};

// Action to perform when cropping region is selected
MOK.Functions.setCrop = function(c){
  MOK.crop = c;
};

/**
 * Save the focal point co-ordinates for a recently cropped image
 */
MOK.Functions.saveFocalPoint = function($image){
  $.ajax({
    type: 'POST',
    url: $image.data('action'),
    data: {
      focalPoint: MOK.ImageUpload.params.focalPoint
    },
    success: function(rtn){

      // The focal point is saved
      if (rtn.type == 'success') {

        MOK.Functions.complete(rtn.msg);

      }

      // Close the image uploader
      MOK.ImageUpload.modalClose();

    }
  });
};

/**
 * Function to perform when an image has been uploaded, cropped & saved
 *
 * Update the form where the image upload was initiated
 *
 * @param rtn
 */
MOK.Functions.complete = function(rtn){

  var $uploadedImg = MOK.Functions.createUploadedImg(rtn);

  // Do we need to insert into an editor window?
  if (undefined !== rtn.insertInto) {
    // Find the TinyMCE instance and insert the image HTML
    tinyMCE.get(rtn.insertInto).execCommand('mceInsertContent', false, $('<p>').append($uploadedImg).html());
  }

  // Update the form field for the image ID if there is one
  if (undefined !== MOK.ImageUpload.params.hiddenSelector) {
    var $hiddenField = $(MOK.ImageUpload.params.hiddenSelector);
    $hiddenField.val(rtn.id);
  }

  // Has a display container been specified to add the newly uploaded image to?
  if (undefined !== MOK.ImageUpload.params.displayContainer) {
    $(MOK.ImageUpload.params.displayContainer).append($uploadedImg);
  }

  // Are we uploading over an existing image?
  if (undefined !== MOK.ImageUpload.params.imageSelector) {
    // Update the original link's src
    $(MOK.ImageUpload.params.imageSelector).attr('src', rtn.src);
  }

  // Has an alert been specified?
  if (undefined !== MOK.ImageUpload.params.alert
    && undefined !== MOK.ImageUpload.params.alertContainer
    && undefined !== MOK.ImageUpload.params.alertClass) {
    // Create the alert
    var $alert = $('<div/>', {
      "class": MOK.ImageUpload.params.alertClass
    }).html(MOK.ImageUpload.params.alert);
    // Add the alert to the alert container
    $(MOK.ImageUpload.params.alertContainer).html($alert);
  }

};


/**
 * Create an HTML image element from the returned data after uploading,
 * cropping and resizing are complete
 *
 * @param rtn
 * @returns {*|jQuery|HTMLElement}
 */
MOK.Functions.createUploadedImg = function(rtn) {

  var $uploadedImg = $('<img/>', {
    src: rtn.src
  });

  // Set a display size on the image?
  if (undefined !== MOK.ImageUpload.params.displaySize) {
    var size = MOK.ImageUpload.params.displaySize.split('x');
    $uploadedImg.css({
      width: size[0],
      height: size[1]
    });
  }

  // Alt text provided?
  if (undefined !== rtn.alt) {
    $uploadedImg.attr('alt', rtn.alt);
  }

  // Alignment class provided?
  if (undefined !== rtn.align) {
    $uploadedImg.addClass('align'+rtn.align);
  }

  return $uploadedImg;

};
