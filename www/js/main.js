$(function(){

    if($(".wysiwyg").length > 0){
    tinyMCE.init({
    selector: ".wysiwyg",
    entity_encoding: "raw",
    plugins: "code,image,link,media,table,imagetools,visualchars,autoresize",
    relative_urls: false,
    file_browser_callback: function(field_name, url, type, win) {
        if(type=='image') $('#my_form input').click();
    }
    });
}
    


    //$(".box_skitter_large").skitter();
  //  $(".slippry-default").slippry();
    
    $(".toggle-menu").click(function(){$(".nav-bar-fixed").toggle(); return false; });
    
    $('.popup-gallery').magnificPopup({
		delegate: 'a',
		type: 'image',
		tLoading: '...',
		mainClass: 'mfp-img-mobile',
		gallery: {
			enabled: true,
			navigateByImgClick: true,
			preload: [0,1] // Will preload 0 - before current, and 1 after the current image
		},
                 youtube: {
      index: 'youtube.com/', // String that detects type of video (in this case YouTube). Simply via url.indexOf(index).

      id: 'v=', // String that splits URL in a two parts, second part should be %id%
      // Or null - full URL will be returned
      // Or a function that should return %id%, for example:
      // id: function(url) { return 'parsed id'; }

      src: '//www.youtube.com/embed/%id%?autoplay=1' // URL that will be set as a source for iframe.
    },
		image: {
			tError: '<a href="%url%">The image #%curr%</a> could not be loaded.',
			titleSrc: function(item) {
				return item.el.attr('title');
			}
		}
	});

    
    var galleryUploader = new qq.FineUploader({
    element: document.getElementById("fine-uploader-gallery"),
    template: 'qq-template-gallery',
    request: {
        endpoint: 'upload?do=save',
        method: 'POST' // Only for the gh-pages demo website due to Github Pages limitations
    },
    validation: {
        allowedExtensions: ['jpeg', 'jpg', 'gif', 'png']
    },
    debug: true
});
    
    /*
     *@TODO: take data from data attributes
     */
    $('.datepicker').datepicker({
        dateFormat: 'dd.mm.yy'
    });

    $('.datetimepicker').datetimepicker(
    {
        dateFormat: 'dd.mm.yy',
        yearRange: '2010:2020',
        stepMinute: 5
    });
    
    if($("#upload-demo").length > 0){
        demoBasic();
    }


});



function demoBasic(){
   var $uploadCrop; 
   $uploadCrop = $('#upload-demo').croppie({
        viewport: {
            width: 1200,
            height: 200,
            type: 'square'
        },
        boundary: {
            width: 1200,
            height: 500
        }
    });

    $uploadCrop.croppie('bind', {
			url: $("#upload-demo").attr("data-url")
			
		});

    $('.upload-result').on('click', function (ev) {
        $uploadCrop.croppie('result', {
            type: 'canvas',
            size: 'viewport'
        }).then(function (resp) {
            $('#imagebase64').val(resp);
            $('#form').submit();
        });
    });



}

(function () {
    angular.module('app', []).directive('countdown', [
        'Util',
        '$interval',
        function (Util, $interval) {
            return {
                restrict: 'A',
                scope: { date: '@' },
                link: function (scope, element) {
                    var future;
                    future = new Date(scope.date);
                    $interval(function () {
                        var diff;
                        diff = Math.floor((future.getTime() - new Date().getTime()) / 1000);
                        return element.text(Util.dhms(diff));
                    }, 1000);
                }
            };
        }
    ]).factory('Util', [function () {
            return {
                dhms: function (t) {
                    var days, hours, minutes, seconds;
                    days = Math.floor(t / 86400);
                    t -= days * 86400;
                    hours = Math.floor(t / 3600) % 24;
                    if(hours < 10){
                        hours = "0" + hours;
                    }
                    t -= hours * 3600;
                    minutes = Math.floor(t / 60) % 60;
                     if(minutes < 10){
                        minutes = "0" + minutes;
                    }
                    t -= minutes * 60;
                    seconds = t % 60;
                    if(seconds < 10){
                        seconds = "0" + seconds;
                    }
                    return [
                        days + 'd',
                        hours + 'h',
                        minutes + 'm',
                        seconds + 's'
                    ].join(' ');
                }
            };
        }]);
}.call(this));


function readFile(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();          
            reader.onload = function (e) {
                $uploadCrop.croppie('bind', {
                    url: e.target.result
                });
                $('.upload-demo').addClass('ready');
            }           
            reader.readAsDataURL(input.files[0]);
        }
    }


