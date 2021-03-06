// Avoid `console` errors in browsers that lack a console.
// @link https://raw.github.com/h5bp/html5-boilerplate/master/js/plugins.js
(function() {
    var method;
    var noop = function () {};
    var methods = [
        'assert', 'clear', 'count', 'debug', 'dir', 'dirxml', 'error',
        'exception', 'group', 'groupCollapsed', 'groupEnd', 'info', 'log',
        'markTimeline', 'profile', 'profileEnd', 'table', 'time', 'timeEnd',
        'timeStamp', 'trace', 'warn'
    ];
    var length = methods.length;
    var console = (window.console = window.console || {});

    while (length--) {
        method = methods[length];

        // Only stub undefined methods.
        if (!console[method]) {
            console[method] = noop;
        }
    }
}());

(function($){

  var $neu = {

    values: {
      carouselDelay: 6000,
      autoRotate: true
    },
    init: function() {

      // JavaScript is on; let's make it known
      $("html.no-js").removeClass("no-js").addClass("js");

      $(".region-navigation .container-inline select option[value='/']").empty();

      $neu.initSideNavigation();

      $neu.initAccordions();
      //$neu.initMegaMenu();

      if($(".carousel").length > 0){
        $neu.initCarousels();
      }

      // Use event delegation
      $neu.delegateClickActions();

      var tabContainers = $('div.tabBox > div');
      tabContainers.filter(':first').addClass('selected');
      $('div.tabBox ul.tabNav a').click(function () {
        tabContainers.removeClass('selected');
        tabContainers.filter(this.hash).addClass('selected');
        $('div.tabBox ul.tabNav a').removeClass('selected');
        $(this).addClass('selected');
        return false;
      }).filter(':first').click();

      $(".typicalTable tbody tr:odd").addClass("odd");



      $(".region-navigation .container-inline select option:first").addClass("hideOption");
      /*$(".region-navigation .container-inline select option[value='/']").attr("disabled","disabled");*/

      //Selected dropdown menu opens up a page
      $(".jump-menu-wrapper select").bind("change", function(){
        var url = $(this).val();
        if(url){
          if (!url.match(/^http/)) {
            url = "/" + url;
          }
          window.location =  url;
        }
        return false;
      });

      $('#larger, #smaller').click(function(){
        var currFontSize;
        var stringEnding;
        var finalNum;

        if(this.id === 'larger') {
          $("p, ul.fancyList01, td, ol, div.resizable").each(function(i){
            currFontSize = $(this).css('fontSize');
            finalNum = parseFloat(currFontSize, 10);
            stringEnding = currFontSize.slice(-2);
            $(this).css('fontSize', finalNum*1.1+stringEnding);
          });
        }
        else if (this.id === 'smaller'){
          $("p, ul.fancyList01, td, ol, div.resizable").each(function(i){
            currFontSize = $(this).css('fontSize');
            finalNum = parseFloat(currFontSize, 10);
            stringEnding = currFontSize.slice(-2);
            $(this).css('fontSize', finalNum/1.1+stringEnding);
          });
        }
      });
      $neu.initModernizr();
    },
    initModernizr: function() {
      if(!Modernizr.svg && $('img[src$="svg"]').length > 0){
          var src = $('img[src$="svg"]').attr('src');
          src = src.replace('svg','png');
          $('img[src$="svg"]').attr('src',src);
      }
      var themepath = '/sites/all/themes/nulib';
      if(!Modernizr.placeholder){
        Modernizr.load({
           load: themepath + '/js/jquery.textPlaceholder.min.js',
           complete: function(){
              $('body').prepend('<style>.text-placeholder {color: #333 !important}</style>');
              $("[placeholder]").textPlaceholder();
            }
        });
      }
    },
    initAccordions: function() {
      $(".accordion > li").each(function(i, el) {
        if(!$(this).hasClass("open")){
          $(this).addClass("closed");
        }
      });
    },
    initSideNavigation: function() {
      $("#columnOne ul .active").parent("ul").closest("li").addClass("containsActive").addClass("open").parent("ul").closest("li").addClass("containsActive").addClass("open");
      $("#columnOne .expandable").not(".containsActive").addClass("closed");
      $("#columnOne .expandable.active-trail").removeClass("closed");
      $("#columnOne .hoverable").not(".containsActive").addClass("closed");

      $("#columnOne .hoverable").hover(function() {
        $(this).not(".containsActive").removeClass("closed").addClass("open");
      }, function() {
        $(this).not(".containsActive").removeClass("open").addClass("closed");
      });
    },
    initCarousels: function() {
      if(($(".carouselTrack .item").length === 1) || ($(".carouselTrack .item").length === 0)){
        return false;
      }

      $neu.rotateCarousel($(".carouselNav a").first());
      $neu.autoRotateCarousel();
    },
    accordionToggle: function(link) {
      if(link.parent("li").hasClass("closed")) {
        link.next(".detail").css("display","none");
        link.parent("li").removeClass("closed").addClass("open");
        link.next(".detail").slideDown();
      } else {
        link.next(".detail").slideUp(function() {
          link.parent("li").removeClass("open").addClass("closed");
          link.next(".detail").css("display","block");
        });
      }
    },
    autoRotateCarousel: function() {
      if($neu.values.autoRotate) {
        $(".carousel").delay($neu.values.carouselDelay).queue(function() {
          $(".carousel").each(function(i, el) {

            $(".verticalCenter").vAlign();

            el = $(el);
            var listItems = el.find(".carouselNav li");
            var nextIndex = listItems.index($(".carouselNav .active")) + 1;
            if(nextIndex >= listItems.length) {
              $neu.rotateCarousel(listItems.eq(0).children("a"));
            } else {
              $neu.rotateCarousel(listItems.eq(nextIndex).children("a"));
            }
          });
          $(this).clearQueue();
          $neu.autoRotateCarousel();
        });
      }
    },
    rotateCarousel: function(link) {

      var carouselTrack = link.closest(".carousel").children(".carouselTrack");
      var index = link.closest(".carouselNav").find("a").index(link);

      var classes = carouselTrack.attr("class").split(" ");
      for(var i=1;i<classes.length;i++) { carouselTrack.removeClass(classes[i]); }
      //carouselTrack.addClass("carouselPos" + (index + 1));

      if($("#shoCar").length > 0) {
        carouselTrack.animate({right: 431*index}, {duration: "slow"});
      } else if($("#dmdsCar").length > 0 || $("#landCar").length > 0) {
        carouselTrack.animate({right: 654*index}, {duration: "slow"});
      } else if($("#exhCar").length > 0){
        carouselTrack.animate({right: 639*index}, {duration: "slow"});
      } else if($("#homeCar").length > 0) {
        carouselTrack.animate({right: 348*index}, {duration: "slow"});
      }

      //if($(this).click) {
        link.closest(".carouselNav").find(".active").removeClass("active");
        link.parent("li").addClass("active");
      //};

    },
    toggleSideNav: function(span) {
      if(span.parent("li").hasClass("closed")) {
        span.parent("li").removeClass("closed").addClass("open");
      } else {
        span.parent("li").removeClass("open").addClass("closed");
      }
    },
    delegateClickActions: function() {
      $("body").delegate("a[href], .accordion .opener, ul .expandable .toggle", "click", function(evt) {
      var followLink = true;
      if($(this).closest(".accordion .opener").length > 0) { followLink = $neu.accordionToggle($(this)); return false; }
      if($(this).closest(".carouselNav a").length > 0) {
        $(".verticalCenter").vAlign();
        $(".carousel").clearQueue();

        $neu.values.autoRotate = false;
        followLink = $neu.rotateCarousel($(this));
        return false;
      }
      if($(this).hasClass("toggle")) { followLink = $neu.toggleSideNav($(this)); }
      $(this).blur();
      });
    }
  };

  // scoping global
  window.$neu = $neu;

  // on dom ready
  $(function() { $neu.init(); });

})
(jQuery);

(function ($) {
// VERTICALLY ALIGN FUNCTION
$.fn.vAlign = function() {
  return this.each(function(i){
    var ah = $(this).height();
    var ph = $(this).parents("div.carouselTrack").height();
    var mh = Math.ceil((ph - ah) / 2);
      if(mh>0) {
        $(this).css('margin-top', mh);
      } else {
        $(this).css('margin-top', 0);
      }
    });
  };
})(jQuery);

//Redirect mobile site if sreenwidth is less than or equal to 699.
if ((screen.width <= 699) && (document.location.href === "http://library.northeastern.edu/")){
  document.location = "http://m.library.northeastern.edu";
}
