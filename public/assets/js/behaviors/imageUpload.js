/**
 * Author:  Mark O'Keeffe

 * Date:    13/09/13
 *
 * [Laravel Workbench]
 */
MOK.Behaviors.imageUpload = function($link){

  // Reset or create the ImageUpload object
  MOK.ImageUpload = {};

  /**
   * Fire the image uploader when the image upload link is clicked
   */
  $link.click(function(){

    // Reset or create the ImageUpload parameters object
    MOK.ImageUpload.params = {};

    // Add the 'data-' attribute values from the upload link to the params object
    $.each($link.data(), function(key, val){
      MOK.ImageUpload.params[key] = val;
    });

    // Request the image uploader modal via AJAX
    $.ajax({
      url: $link.attr('href'),
      success: function (rtn) {
        if (rtn.type === 'modal') {
          MOK.ImageUpload.modal(rtn.msg);
        }
      }
    });

    return false;
  });

};
