/**
 * Author:  Mark O'Keeffe

 * Date:    13/09/13
 *
 * [Laravel Workbench]
 */
MOK.Behaviors.loadImageFile = function($input){

  // When the file input changes, render the image data for cropping
  $input.off().on('change', function(){
    // Get any other data from the form e.g. alt text, caption, alignment
    MOK.Functions.addFormData($input.closest('form'));
    // Load the image from file into the uploader
    MOK.Functions.loadImageFile(this);
  });

};
