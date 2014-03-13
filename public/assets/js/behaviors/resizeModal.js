/**
 * Author:  Mark O'Keeffe

 * Date:    13/09/13
 *
 * [Laravel Workbench]
 */
MOK.Behaviors.resizeModal = function($elem){

  $elem.on('load', function() {

    var $modal = $(this).closest('.modal-dialog'),
      modalPadding =
        parseInt($modal.css('padding-left')) +
          parseInt($modal.css('padding-right')),
      $modalBody = $modal.find('.modal-body'),
      bodyPadding =
        parseInt($modalBody.css('padding-left')) +
          parseInt($modalBody.css('padding-right')),
      modalW = $modal.width(),
      elemW = $elem.width();

    if (elemW > modalW) {
      $modal.animate({
        width: (elemW + modalPadding + bodyPadding)
      });
    }

  });
};
