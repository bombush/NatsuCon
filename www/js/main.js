$(function(){

    tinyMCE.init({
    selector: ".mceEditor",
    entity_encoding: "raw",
    plugins: "code,image,link,media,table,imagetools,visualchars" 
    });

    $(".box_skitter_large").skitter();
    
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
		image: {
			tError: '<a href="%url%">The image #%curr%</a> could not be loaded.',
			titleSrc: function(item) {
				return item.el.attr('title');
			}
		}
	});
    
    $('.datepicker').datepicker();



});
