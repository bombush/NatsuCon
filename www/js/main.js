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

    if(typeof(qq) !== 'undefined' && $("#fine-uploader-gallery").length > 0){
    
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
    }
    
    /*
     *@TODO: take data from data attributes
     */
    $('.datepicker').datepicker({
        dateFormat: 'dd.mm.yy'
    });

    //DatePicker.init(window);
    
    if($("#upload-demo").length > 0){
        demoBasic();
    }


});


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


function demoBasic() {
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

CroppieOverlay = new function(){

    var _clear = function() {
        throw new Error('CroppieOverlay.clear not implemented');
    }

    var _openPopup = function(content) {
        function openNewPopup() {
            $.magnificPopup.open({
                items: {
                    src: content
                },
                callbacks: {
                    'afterClose': function () {
                        $.magnificPopup.instance.content = null;
                    }
                }
            });

            return function () {
                $.magnificPopup.close();
            }
        }

        function openInExistingPopup() {
            var $content = $(content);
            $($.magnificPopup.instance.content).children().each(function(){
                $(this).hide();
            });

            $($.magnificPopup.instance.content).append($content);

            return function() {
                $content.remove();
                $($.magnificPopup.instance.content).children().each(function(){
                    $(this).show();
                });
            };
        }

        if(typeof($.magnificPopup.instance.content) == 'undefined' || $.magnificPopup.instance.content === null ) {
            return openNewPopup();
        } else {
            return openInExistingPopup();
        }
    }

    var _openEditor = function(file, x, y) {
        var $croppieHtml = $('<div class="croppie_canvas"><div class="croppie-wrap"></div><a class="btn js-croppie-commit">Potvrdit</a></div>');
        var $croppieElement = $croppieHtml.find('.croppie-wrap');
        var $commitButton = $croppieHtml.find('.js-croppie-commit');

        var closePopupCallback = _openPopup($croppieHtml);


        var $uploadCrop = $croppieElement.croppie({
            viewport: {
                width: x,
                height: y,
                type: 'square'
            },
            boundary: {
                width: 1200,
                height: 500
            }
        });

        var reader = new FileReader();
        reader.onload = function (e) {
            $uploadCrop.croppie('bind', {url: e.target.result});
        };
        reader.readAsDataURL(file);


        var deferred = $.Deferred();
        var promise = deferred.promise();

        $commitButton.on('click', function(event){
            event.preventDefault();
            event.stopPropagation();
            $uploadCrop.croppie('result', {
                type: 'canvas',
                size: 'viewport'

            }).then(function(base64){
                deferred.resolve(base64);
                closePopupCallback();
            });
        });

        return promise;
    }
    /**
     * @param size {x: y:}
     * @private
     */
    var _open = function(x, y){
        function filenameFromPath(path) {
            var split = path.split('\\');
            return split[split.length - 1];
        }

        var deferred = $.Deferred();
        var promise = deferred.promise();

        $('<input type="file" name="temp_photo">').on('change', function (e) {
            if (e.target.files.length == 0) {
                _clear();
                deferred.resolve();
            } else {
                var promise = _openEditor(e.target.files[0], x, y);
                promise.done(function(base64) {
                    deferred.resolve(base64, filenameFromPath(e.target.value));
                });
            }
        })
        .click();

        return promise
    };

    return { open : _open};
};

$(function () {
    ProgramEditGrid = new function () {
        var $wrap = $('.program-grid');
        var columns = $wrap.find('tbody tr').find('td').length;

        // expand grid row and load program edit form
        $wrap.on('click', 'tr.grid-row', function () {
                var $programTr = $(this);

                var $next = $programTr.next();
                if($next.is('.js-program-form-row')) {
                    $next.slideUp().remove();
                    return;
                }

                var $editTr = $('<tr class="js-program-form-row">');
                var $editTd = $('<td>').attr('colspan', columns);


                // load form
                var $req = $.ajax({
                    method : 'GET',
                    data : {'program_id' : $programTr.data('id') },
                    url: $wrap.data('load-url')
                });

                $req.done(function(response){
                    var $formWrapped = $(response).find('.program-edit-form-wrap');
                    var $form = $formWrapped.find('.js-program-edit-form');
                    $editTd.append($formWrapped);
//                    DatePicker.init($form);

                    $editTr
                        .hide()
                        .append($editTd);
                    $editTr
                        .insertAfter($programTr)
                        .slideDown(2000);

                    function formSuccess() {
                        var promise = reloadWithModifiers();
                        promise.success(function () {
                            $(html, body).animate({
                                //@TODO: afterscroll
                                //scrollTop: $programTr
                            });
                            $programTr;
                        });
                    }
                    ProgramEditForm.init($form, formSuccess);
                });


            }
        );

        /// bind filters
        $wrap.on('keyup', '.filter input', function(){
            $filterVal = $(this).val();
            $.ajax()
        });

        $wrap.on('click', '.js-new', function(event){
            event.preventDefault();

            var req = $.ajax({
                method : 'GET',
                url : $(this).attr('href'),
            });

            req.done(function(resp) {
                $overlayWrap = $('<div class="overlay-wrap"></div>');
                $wrappedForm = $(resp).find('.program-edit-form-wrap');

                $overlayWrap.append($wrappedForm);

                $.magnificPopup.open({
                    items: {
                        src: $overlayWrap
                    },
                    callbacks : {
                        'afterClose' : function(){
                            $.magnificPopup.instance.content = null;
                        }
                    }
                });
                ProgramEditForm.init($wrappedForm.find('form'));
                /*$wrappedForm.find('input[type="submit"]').click(function(){
                    $(this).closest('form').submit();
                });*/

                //show in overlay
            });
        });

        var $grid = $('.program-grid');
        var $gridoForm = $('form.grido');
        $gridoForm.on('submit', function(event){
            /*event.preventDefault();

            $(this).ajaxSubmit({
                success: function(response){
                    var $responseGrid = $(response).find('#grid');
                    var $currentGrid = $grid.find('#grid');
                    //$currentGrid.remove();

                    $currentGrid.replaceWith($responseGrid);
                }
            });*/
        });

        $natsuGridWrap = $('.js-natsu-grid-wrap');

        function getCurrentGrid() {
            return findGrid.bind(null, $natsuGridWrap);
        }

        function findGrid(context) {
            return $(context).find('.js-results');
        }

        function replaceCurrentGrid() {
            return {
                'with' : function($newGrid){
                    getCurrentGrid()().replaceWith($newGrid);
                }
            };
        }

        function reloadWithModifiers()
        {
            var defer = $.Deferred();
            $natsuGridWrap.find('form[name="source-modifiers"]')
                .ajaxSubmit({
                    success: function (response) {
                        replaceCurrentGrid().with(findGrid(response));
                        defer.resolve(true);
                    }
                });

            var promise = defer.promise();
            return promise;
        }

        $natsuGridWrap.on('keyup', '.filters input', function(){
            reloadWithModifiers();
        });

        // generator to cycle ASC, DESC, none
        var orderCycle = (function() {
            var _orderCycle = ['ASC', 'DESC', ''];
            var _currentProgression = -1;

            var _next = function () {
                if (_currentProgression + 1 >= _orderCycle.length)
                    _currentProgression = 0;
                else
                    _currentProgression++;

                return _orderCycle[_currentProgression];
            }

            return {
                next : _next
            };
        })();

        $natsuGridWrap.on('click', '.js-natsu-grid-sort', function(event){
            event.preventDefault();

            $natsuGridWrap.find('.js-natsu-grid-sort-input').val($(this).data('key') + ':' + orderCycle.next());
            reloadWithModifiers();
        });


        $(function () {
            var $timeFromElement = $natsuGridWrap.find('[name="natsu_filter[timeFrom]"]');
            var $timeToElement = $natsuGridWrap.find('[name="natsu_filter[timeTo]"]');
            $timeFromElement.periodpicker({
                end: $timeToElement,

                timepicker: true,
                showTimepickerInputs: true,
                timepickerOptions: {
                    dragAndDrop: true,
                    mouseWheel: true,
                    hours: true,
                    minutes: true,
                    seconds: false,
                    ampm: false,
                    steps: [1, 30, 2, 1]
                },
                cells: [1, 1],
                //withoutBottomPanel: true,
                yearsLine: false,

                formatDateTime: 'YYYY-MM-DD HH:mm:ss ',

                okButton: true,
                onOkButtonClick: function (param) {
                    var val = $timeFromElement.periodpicker('valueStringStrong');
                    var expl = val.split(' -');
                    $timeFromElement.val(expl[0]);
                    $timeToElement.val(expl[1]);

                    reloadWithModifiers();
                },
            });
        });

        return {
            reload : reloadWithModifiers
        };
    }
})

window.FormImageInput = new function(){

    var _yieldInput = (function() {
        var currentNumber = -1;

        return function() {
            currentNumber ++;
            return $('<input type="hidden" name="b64upload_' + currentNumber + '">');
        }
    })();

    var _createMetaInputs = function(newInput, triggerInput, filename) {
        var metaArrayName = $(newInput).attr('name') + '_meta';

        var $mimeInput = $('<input type="text" name="' + metaArrayName + '[mime]">');
        $mimeInput = $mimeInput.val($(triggerInput).data('mime'));

        var $filenameInput = $('<input type="text" name="' + metaArrayName + '[filename]">');
        $filenameInput.val(filename);

        return [$mimeInput[0], $filenameInput[0]];
    }

    var _addToForm = function (form, input) {
        $(form).append(input);
    }

    var _base64ToImg = function(base64) {
        var img = document.createElement('img');
        img.src = base64;

        return img;
    }

    var _addAction = function(form, action, value)
    {
        var actionInput = $('<input type="text" name="attachmentActions[' + action + '][]" value="' + value + '">');
        $(form).append(actionInput);
        return actionInput;
    }

    var _addBase64 = function(form, triggerInput, b64Image, filename) {
        var $form = $(form);
        var $triggerInput = $(triggerInput);

        var $newInput = $(_yieldInput());
        $newInput = $newInput
            .hide()
            .appendTo($form)
            .val(b64Image);

        var metaInputs = _createMetaInputs($newInput, $triggerInput, filename);
        metaInputs.forEach(_addToForm.bind(null, $form));

        return $newInput;
    }

    function _init(form) {
        var $form = $(form);

        $form.on('click', '.js-image',  function(){
            var $triggerInput = $(this);
            var action = $triggerInput.data('action');

            if(action == 'add') {

                $sizes = $triggerInput.data('size').split('x');
                var imagePromise = CroppieOverlay.open($sizes[0], $sizes[1]);

                imagePromise.done(function (b64Image, filename) {
                    var newInput = _addBase64($form, $triggerInput, b64Image, filename);

                    $newImageInput = $triggerInput.clone();
                    $newImageInput.find('img').remove();
                    $newImageInput.append(_base64ToImg(b64Image));
                    $newImageInput.insertBefore($triggerInput);

                    $newImageInput.attr('data-action', 'none');
                    $newImageInput.data('action', 'none');

                    if($triggerInput.data('mime') == 'HEADIMAGE') {
                        $triggerInput.remove();
                    }

                    _addAction(form, 'add', $(newInput).attr('name'));
                });

            } else if (action == 'remove') {
                if ($triggerInput.data('mime') != 'HEADIMAGE')
                    $(this).remove();
                else {
                    $(this).find('img').remove();
                    $(this).data('action','add');
                }

                if(0 != $triggerInput.data('attachment-id'))
                    _addAction(form, 'remove', $triggerInput.data('attachment-id'));

            } else if(action == 'edit') {
                $sizes = $triggerInput.data('size').split('x');
                var imagePromise = CroppieOverlay.open($sizes[0], $sizes[1]);

                imagePromise.done(function (b64Image) {
                    var newInput = _addBase64($form, $triggerInput, b64Image);
                    $triggerInput.find('img').remove();
                    $triggerInput.append(_base64ToImg(b64Image));
                    _addAction(form, 'edit', $triggerInput.data('attachment-id') + ":" + $(newInput).attr('name'));
                });
            }

        });
    }

    return {
        init : _init
    }
}

/**
 * ProgramEditForm - edit/add single program record
 *
 * init: ProgramEditForm.init($form, [onSuccess])
 * attachEventHandlerOnSuccess: handler accepts arguments (HtmlElement|jQuery form, Object submitResponse)
 *
 * @type {{init, attachEventHandlerOnSuccess}}
 */
window.ProgramEditForm = (function(){

    //handled onsuccess events
    var _OnSuccess = {
        ON_SUCCESS_ATTRIBUTE : 'programEditForm-onsuccess',

        initOnSuccessArray : function (form) {
            $(form).data(_OnSuccess.ON_SUCCESS_ATTRIBUTE, []);
        },
        attachEventHandlerOnSuccess : function (form, handler) {

            $(form).data(_OnSuccess.ON_SUCCESS_ATTRIBUTE).push(handler);
        },

        callOnSuccessHandlers : function (form, arg) {
            var onSuccessHandlers = $(form).data(_OnSuccess.ON_SUCCESS_ATTRIBUTE);
            for (var i in onSuccessHandlers)
                onSuccessHandlers[i]($(form), response);
        }
    };

    var _OnSubmit = {
        submitted : function(e) {
            e.preventDefault();
            $form.ajaxSubmit({
                success: function (response) {
                    // @TODO: desynchronize
                    var response = JSON.parse(response);
                    if (response.success == true) {
                        $.magnificPopup.close();
                        _OnSuccess.callOnSuccessHandlers($form, response);

                    } else {
                        alert('Neznámá chyba');
                    }
                }
            });
        }
    };

    var _initPeriodpicker = function(form){
        var $form = $(form);

        $form.find('input.js-period-start').periodpicker({
            norange: true,
            timepicker: true,
            showTimepickerInputs: true,
            timepickerOptions: {
                dragAndDrop: true,
                mouseWheel: true,
                hours: true,
                minutes: true,
                seconds: false,
                ampm: false,
                steps: [1, 30, 2, 1],
                twelveHoursFormat: false
            },
            //formatDate: 'DD.MM.YYYY',
            cells: [1, 1],
            withoutBottomPanel: true,
            yearsLine: false,

            formatDateTime: 'YYYY-MM-DD HH:mm:ss'
        });

        $form.find('input.js-period-end').periodpicker({
            norange: true,
            timepicker: true,
            showTimepickerInputs: true,
            timepickerOptions: {
                dragAndDrop: true,
                mouseWheel: true,
                hours: true,
                minutes: true,
                seconds: false,
                ampm: true
            },
            //formatDate: 'DD.MM.YYYY',
            cells: [1, 1],
            withoutBottomPanel: true,
            yearsLine: false,

            formatDateTime: 'YYYY-MM-DD HH:mm:ss'
        });
    }

    /**
     *
     * @param form
     * @param onSuccessHandler Callback accepting arguments (HtmlElement|jQuery form, Object submitResponse)
     * @private
     */
    var _init = function(form, onSuccessHandler) {
        if($(form).prop('tagName').toLowerCase() != 'form')
            throw new Error('The selected element is not a FORM');

        var $form = $(form);
        _OnSuccess.initOnSuccessArray($form);
        FormImageInput.init($form);
        $form.on('submit', _OnSubmit.submitted);
        _initPeriodpicker($form);

        if(typeof(onSuccessHandler) != 'undefined')
            _OnSuccess.attachEventHandlerOnSuccess($form, onSuccessHandler);
    }

    return {
        init : _init
    }
})();

window.DatePicker = new function(){
    var _init = function(context) {
        $(context).find('.datepicker').datepicker({
            dateFormat: 'dd.mm.yy'
        });
        /*
         *@TODO: take data from data attributes
         */

        $(context).find('.datetimepicker').datetimepicker(
            {
                dateFormat: 'dd.mm.yy',
                yearRange: '2010:2020',
                stepMinute: 5
            });
    }

    return {
        init : _init,
        registerOnSuccess : _registerOnSuccess
    }
}

