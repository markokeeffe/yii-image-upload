/**
 * Author:  Mark O'Keeffe

 * Date:    13/09/13
 *
 * [Laravel Workbench]
 */
MOK.Behaviors.focalPoint = function($image){

  var $button = $($image.data('button'));

  $image.addClass('setting-focal-point');
  $image.on('click', function(e){

    var $container = $image.closest('.focal-container'),
      posX = Math.round($(this).offset().left),
      posY = Math.round($(this).offset().top),
      left = Math.round(e.pageX - posX),
      top = Math.round(e.pageY - posY),
      $focalPoint = $('<div/>', {
        'class': 'focal-point',
        'style': 'left:'+(e.pageX - posX)+'px; top:'+(e.pageY - posY)+'px;'
      });

    MOK.ImageUpload.params.focalPoint = left+','+top;

    $container.find('.focal-point').remove();
    $container.append($focalPoint);

    $button.show();

  });

  // Save the focal point when the user clicks the button
  $button.off().on('click', function(){
    MOK.Functions.saveFocalPoint($image);
  });

};
