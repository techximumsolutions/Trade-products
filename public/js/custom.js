function increaseValue() {
  var value = parseInt(document.getElementById('number').value, 10);
  value = isNaN(value) ? 0 : value;
  value++;
  document.getElementById('number').value = value;

}
function decreaseValue() {
  var value = parseInt(document.getElementById('number').value, 10);
  value = isNaN(value) ? 0 : value;
  value < 1 ? value = 1 : '';
  value--;
  document.getElementById('number').value = value;
}

jQuery('.starbtnfav').click(function() {

    jQuery(this).toggleClass('green');

});





 jQuery('.panel-collapse').on('show.bs.collapse', function () {

        jQuery(this).siblings('.panel-heading').addClass('active');

      });



      jQuery('.panel-collapse').on('hide.bs.collapse', function () {

        jQuery(this).siblings('.panel-heading').removeClass('active');

    });



 jQuery(document).on("click", '[data-toggle="lightbox"]', function(event) {

  event.preventDefault();

  jQuery(this).ekkoLightbox();

});
jQuery(document).ready(function() {

  jQuery("#readmore").click(function() {

    var elem = jQuery("#readmore").text();

    if (elem == "Read more") {

      //Stuff to do when btn is in the read more state

      jQuery("#readmore").text("Read less");

      jQuery("#moretext").slideDown();

    } else {

      //Stuff to do when btn is in the read less state

      jQuery("#readmore").text("Read more");

      jQuery("#moretext").slideUp();

    }

  });

});



      (function($) {
          var $main_nav = jQuery('#main-nav');
          var $toggle = jQuery('.toggle');
          var defaultData = {

            maxWidth: false,

            customToggle: $toggle,

            navTitle: 'Trade Products',

            levelTitles: true,

            pushContent: '#container',

            // insertClose: 2,

            closeLevels: false

          };



          // add new items to original nav

          $main_nav.find('li.add').children('a').on('click', function() {

            var $this = jQuery(this);

            var $li = $this.parent();

            var items = eval('(' + $this.attr('data-add') + ')');



            $li.before('<li class="new"><a>'+items[0]+'</a></li>');



            items.shift();



            if (!items.length) {

              $li.remove();

            }

            else {

              $this.attr('data-add', JSON.stringify(items));

            }



            Nav.update(true);

          });



          // call our plugin

          var Nav = $main_nav.hcOffcanvasNav(defaultData);



          // demo settings update



          const update = (settings) => {

            if (Nav.isOpen()) {

              Nav.on('close.once', function() {

                Nav.update(settings);

                Nav.open();

              });



              Nav.close();

            }

            else {

              Nav.update(settings);

            }

          };



          jQuery('.actions').find('a').on('click', function(e) {

            e.preventDefault();



            var $this = jQuery(this).addClass('active');

            var $siblings = $this.parent().siblings().children('a').removeClass('active');

            var settings = eval('(' + $this.data('demo') + ')');



            update(settings);

          });



          $('.actions').find('input').on('change', function() {

            var $this = jQuery(this);

            var settings = eval('(' + $this.data('demo') + ')');



            if ($this.is(':checked')) {

              update(settings);

            }

            else {

              var removeData = {};

              $.each(settings, function(index, value) {

                removeData[index] = false;

              });



              update(removeData);

            }

          });

        })(jQuery);









jQuery(document).ready(function(){

  jQuery(".closecart").click(function(){

    jQuery("#cartmd").removeClass("openblc");

  });

  jQuery(".opencart").click(function(){

    jQuery("#cartmd").addClass("openblc");

  });

});



jQuery(document).ready(function(){

  jQuery('.input-group input').focus(function(){

    me = jQuery(this) ;

    jQuery("label[for='"+me.attr('id')+"']").addClass("animate-label");

  }) ;

  jQuery('.input-group input').blur(function(){

    me = jQuery(this) ;

    if ( me.val() == ""){

      jQuery("label[for='"+me.attr('id')+"']").removeClass("animate-label");

    }

  }) ;

});



jQuery(document).ready(function(){

  jQuery('.input-group textarea').focus(function(){

    me = jQuery(this) ;

    jQuery("label[for='"+me.attr('id')+"']").addClass("animate-label");

  }) ;

  jQuery('.input-group textarea').blur(function(){

    me = jQuery(this) ;

    if ( me.val() == ""){

      jQuery("label[for='"+me.attr('id')+"']").removeClass("animate-label");

    }

  }) ;

});







jQuery(".cardblc1").click(function(){

  jQuery(".visamaster").show();

  jQuery(".paypal").hide();

});



jQuery(".cardblc2").click(function(){

  jQuery(".visamaster").hide();

  jQuery(".paypal").show();

});







jQuery(".forgot").click(function(){

  jQuery("#loginform").hide();

  jQuery("#paswform").show();

});



jQuery(".loginback").click(function(){

  jQuery("#loginform").show();

  jQuery("#paswform").hide();

});











