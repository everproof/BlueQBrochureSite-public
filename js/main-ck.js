jQuery(function(e){"use strict";function t(){var t=[],n=[],r=e(window).scrollTop(),i=200,s=500;e(".navbar-collapse").find(".scroll a").each(function(){t.push(e(e(this).attr("href")).offset().top);n.push(e(e(this).attr("href")).offset().top+e(e(this).attr("href")).height())});e.each(t,function(n){r>t[n]-i&&e(".navbar-collapse li.scroll").removeClass("active").eq(n).addClass("active")})}function s(){var e=new google.maps.LatLng(r,i),t={zoom:14,scrollwheel:!1,center:e},n=new google.maps.Map(document.getElementById("google-map"),t),s=new google.maps.Marker({position:e,map:n})}e(window).scroll(function(e){t()});e(".navbar-collapse ul li a").on("click",function(){e("html, body").animate({scrollTop:e(this.hash).offset().top-70},1e3);Modernizr.touch&&e(".navbar-toggle").click();return!1});e("#tohash").on("click",function(){e("html, body").animate({scrollTop:e(this.prop("hash")).offset().top-5},1e3);return!1});e(".accordion-toggle").on("click",function(){e(this).closest(".panel-group").children().each(function(){e(this).find(">.panel-heading").removeClass("active")});e(this).closest(".panel-heading").toggleClass("active")});e(document).ready(function(){function a(e){i=e;f();l()}function f(){n=e("<div>",{id:"progressBar"});r=e("<div>",{id:"bar"});n.append(r).appendTo(i)}function l(){u=0;s=!1;o=setInterval(c,10)}function c(){if(s===!1){u+=1/t;r.css({width:u+"%"});u>=100&&i.trigger("owl.next")}}function h(){s=!0}function p(){clearTimeout(o);l()}var t=7,n,r,i,s,o,u;e("#main-slider").find(".owl-carousel").owlCarousel({slideSpeed:500,paginationSpeed:500,singleItem:!0,navigation:!0,navigationText:["<i class='fa fa-angle-left'></i>","<i class='fa fa-angle-right'></i>"],afterInit:a,afterMove:p,startDragging:h,transitionStyle:"fadeUp"})});(new WOW).init();smoothScroll.init();e(window).load(function(){var t=e(".portfolio-filter >li>a"),n=e(".portfolio-items");n.isotope({itemSelector:".portfolio-item",layoutMode:"fitRows"});t.on("click",function(){t.removeClass("active");e(this).addClass("active");var r=e(this).attr("data-filter");n.isotope({filter:r});return!1})});e(document).ready(function(){e(".progress-bar").bind("inview",function(t,n,r,i){if(n){e(this).css("width",e(this).data("width")+"%");e(this).unbind("inview")}});e.fn.animateNumbers=function(t,n,r,i){return this.each(function(){var s=e(this),o=parseInt(s.text().replace(/,/g,""));n=n===undefined?!0:n;e({value:o}).animate({value:t},{duration:r==undefined?1e3:r,easing:i==undefined?"swing":i,step:function(){s.text(Math.floor(this.value));n&&s.text(s.text().replace(/(\d)(?=(\d\d\d)+(?!\d))/g,"$1,"))},complete:function(){if(parseInt(s.text())!==t){s.text(t);n&&s.text(s.text().replace(/(\d)(?=(\d\d\d)+(?!\d))/g,"$1,"))}}})})};e(".animated-number").bind("inview",function(t,n,r,i){var s=e(this);if(n){s.animateNumbers(s.data("digit"),!1,s.data("duration"));s.unbind("inview")}})});var n=e("#main-contact-form");n.submit(function(t){t.preventDefault();var n=e('<div class="form_status"></div>'),r=e("#main-contact-form");e.ajax({dataType:"html",type:"POST",data:r.serialize(),url:e(this).attr("action"),beforeSend:function(){r.before(n.html('<p><i class="fa fa-spinner fa-spin"></i> Email is sending...</p>').fadeIn())}}).done(function(e){n.html('<p class="alert alert-success">Thank you for contacting us. We will be in touch shortly.</p>');r.slideUp()})});e("a[rel^='prettyPhoto']").prettyPhoto({social_tools:!1});var r=e("#google-map").data("latitude"),i=e("#google-map").data("longitude");google.maps.event.addDomListener(window,"load",s)});