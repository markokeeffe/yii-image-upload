/**
 * Author:  Mark O'Keeffe

 * Date:    25/09/13
 *
 * [Laravel Workbench]
 */
/**
 * Author:  Mark O'Keeffe

 * Date:    13/09/13
 *
 * [Laravel Workbench]
 */
MOK.Behaviors.loadImageURL = function($elem){

  var go = function ($input) {
    // Get any other data from the form e.g. alt text, caption, alignment
    MOK.Functions.addFormData($input.closest('form'));
    // Load the image from a remote URL into the uploader
    MOK.Functions.loadImageURL($input);
  };

  if ($elem.prop('tagName') == 'BUTTON') {
    // Submit the URL on click of the button
    $elem.off().on('click', function(){
      go($($elem.data('selector')));
    });
  } else if ($elem.prop('tagName') == 'INPUT') {
    // Submit the URL when the user presses enter
    $elem.keyup(function(event){
      if(event.keyCode == 13){
        go($elem);
      }
    });
  }

};
