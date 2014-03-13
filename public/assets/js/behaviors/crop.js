/**
 * Author:  Mark O'Keeffe

 * Date:    13/09/13
 *
 * [Laravel Workbench]
 */
MOK.Behaviors.crop = function($image){

  // When the image has finished loading
  $image.off().on('load', function() {

    // Add the 'Save Crop' button to the modal
    MOK.Functions.addCropButton($image);

    // Initialize Jcrop on the image
    MOK.Jcrop = $.Jcrop($image);

    var opts = {
      onSelect: MOK.Functions.setCrop
    };

    // Has a specific image size been specified?
    if (undefined !== MOK.ImageUpload.params.size) {
      // Get the width and height values to lock the aspect ratio
      var sizes = MOK.ImageUpload.params.size.split('x');
      opts.aspectRatio = (sizes[0] / sizes[1]);
    }

    // Set the Jcrop options
    MOK.Jcrop.setOptions(opts);

    // Set the Jcrop selected area
    MOK.Jcrop.setSelect([
      0,
      0,
      $image[0].width,
      $image[0].height
    ]);

    // Set the dimensions of the selected crop area in the 'item'
    MOK.Functions.setCrop(MOK.Jcrop.tellSelect());

  });

};
