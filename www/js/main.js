$(function(){


    if($(".wysiwyg").length > 0){
        initTinyMce();
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

var initTinyMce = (function() {
    var inited = false;

    return function() {
        if(!inited) {
            tinyMCE.init({
                selector: "textarea",
                entity_encoding: "raw",
                plugins: "code,image,link,media,table,imagetools,visualchars,autoresize",
                relative_urls: false,
                file_browser_callback: function (field_name, url, type, win) {
                    if (type == 'image') $('#my_form input').click();
                }
            });
            inited = true;
        }
    }
})();

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


/**
 * Select file and open in overlay for editing
 *
 * @type {CroppieOverlay}
 */
CroppieOverlay = new function(){

    // entity to return
    var _CroppedImage = {
        original : null,
        cropped: null
    }

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
        var resultObject = Object.create(CroppieOverlay.CroppedImage);

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
            resultObject.original = e.target.result;

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
                resultObject.cropped = base64;

                deferred.resolve(resultObject);
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
                promise.done(function(resultObject) {
                    deferred.resolve(resultObject, filenameFromPath(e.target.value));
                });
            }
        })
        .click();

        return promise
    };

    return {
        open : _open,
        CroppedImage : _CroppedImage
    };
};

$(function () {
    ProgramEditGrid = new function () {
        var $wrap = $('.program-grid');
        var columns = $wrap.find('tbody tr').find('td').length;

        // expand grid row and load program edit form
        $wrap.on('click', 'tr.grid-row', function () {
                var $programTr = $(this);
                var programId = $programTr.data('id');
                var loadUrl = $wrap.data('load-url');

                var editFinishedPromise = OverlayManager.openProgramEditForm(loadUrl, {program_id : programId });
                editFinishedPromise.always(reloadWithModifiers);

                /*
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
                        .slideDown(2000, function(){
                            var programTrY = $programTr.offset().top;
                            var programTrHeight = $programTr.height();
                            var clientHeight = Math.max(document.documentElement.clientHeight, window.innerHeight || 0);
                            var scrollBottom = $(window).scrollTop() + clientHeight;

                            var scrollHeightDiff = scrollBottom - (programTrY + programTrHeight)
                            if(scrollHeightDiff < 0)
                                window.scrollBy(Math.abs(scrollHeightDiff), 0);
                        });

                    function formSuccess() {
                        $editTr.slideUp();
                        var promise = reloadWithModifiers();
                        promise.done(function () {
                            /*$(html, body).animate({
                                //@TODO: afterscroll
                                //scrollTop: $programTr
                            });
                            $programTr;*/
                        //});
                   // }
                   // ProgramEditForm.init($form, formSuccess);
                //});


            }
        );

        /// bind filters
        $wrap.on('keyup', '.filter input', function(){
            $filterVal = $(this).val();
            $.ajax()
        });

        $wrap.on('click', '.js-new', function(event){
            event.preventDefault();
            var loadUrl = $(this).attr('href');
            var finishedPromise = OverlayManager.openProgramEditForm(loadUrl);
            finishedPromise.always(ProgramEditGrid.reload);
        });

        $wrap.on('click', '.js-publish-section', function(event) {
            event.preventDefault();
            event.stopPropagation();

            var requesturl = $(this).attr('href');
            var confirm = window.confirm('Opravdu chcete zveřejnit všechen program?');

            if(!confirm)
                return;

            var request = $.ajax({
                method : 'GET',
                url : requesturl
            });
            request.done(function(res){
                if(res.status == true)
                    alert('OK');
                else
                    alert('Fail');
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
                        var newGrid = findGrid(response);
                        replaceCurrentGrid().with(newGrid);
                        //ProgramEditGrid.init(newGrid);

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
                    twelveHoursFormat: false,
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

        var $mimeInput = $('<input type="hidden" name="' + metaArrayName + '[mime]">');
        $mimeInput = $mimeInput.val($(triggerInput).data('mime'));

        var $filenameInput = $('<input type="hidden" name="' + metaArrayName + '[filename]">');
        $filenameInput.val(filename);

        return [$mimeInput[0], $filenameInput[0]];
    };

    var _createThumbPrototypeInput = function(newInput, resultObject){
        var $input = $('<input type="hidden" name="' + $(newInput).attr('name') + '_thumbPrototype">')

        return $input;
    };

    var _addToForm = function (form, input) {
        $(form).append(input);
    };

    var _base64ToImg = function(base64) {
        var img = document.createElement('img');
        img.src = base64;

        return img;
    }

    var _addAction = function(form, action, value)
    {
        var actionInput = $('<input type="hidden" name="attachmentActions[' + action + '][]" value="' + value + '">');
        $(form).append(actionInput);
        return actionInput;
    }

    var _removeAction = function($form, action, value)
    {
        var $actionInput = $('input[name="attachmentActions[' + action + '][]"][value="' + value + '"]');
        if($actionInput.length == 0)
            throw new Error('Action input ' + action + ' with value ' + value + ' not found.');

        $actionInput.remove();
    }

    var _addHiddenInputs = function(form, triggerInput, resultObject, filename) {
        var $form = $(form);
        var $triggerInput = $(triggerInput);

        var $newInput = $(_yieldInput());
        $newInput = $newInput
            .hide()
            .appendTo($form)
            .val(resultObject.original);

        var $thumbPrototypeInput = $(_createThumbPrototypeInput($newInput));
        $thumbPrototypeInput.hide().appendTo($form).val(resultObject.cropped);

        //var $newFullInput = $();

        var metaInputs = _createMetaInputs($newInput, $triggerInput, filename);
        metaInputs.forEach(_addToForm.bind(null, $form));

        var outInputs = {
            base64Input : $newInput,
            thumbPrototypeInput : $thumbPrototypeInput,
            metaInputs : metaInputs
        };
        outInputs.removeAll = function(){
            $($thumbPrototypeInput).remove();
            $(outInputs.base64Input).remove();
            $(outInputs.metaInputs).each(function(){$(this).remove();});
        };

        return outInputs;
    }

    function _init(form) {
        var $form = $(form);

        $form.on('click', '.js-image',  function(){
            var $triggerInput = $(this);
            var action = $triggerInput.data('action');

            if(action == 'add') {

                $sizes = $triggerInput.data('size').split('x');
                var imagePromise = CroppieOverlay.open($sizes[0], $sizes[1]);

                imagePromise.done(function (resultObject, filename) {
                    var thumbPrototype = resultObject.cropped;
                    var hiddenInputs = _addHiddenInputs($form, $triggerInput, resultObject, filename);
                    var newInput = hiddenInputs.base64Input;

                    $newImageInput = $triggerInput.clone();
                    $newImageInput.find('img').remove();
                    $newImageInput.append(_base64ToImg(thumbPrototype));
                    $newImageInput.insertBefore($triggerInput);

                    $newImageInput.attr('data-action', 'remove');
                    $newImageInput.data('action', 'remove');
                    $newImageInput.attr('data-attachment-id', 0);
                    $newImageInput.data('attachment-id', 0);
                    $newImageInput.data('hiddenInputs', hiddenInputs);

                    if($triggerInput.data('mime') == 'HEADIMAGE') {
                        $triggerInput.remove();
                    }

                    _addAction(form, 'add', $(newInput).attr('name'));
                });

            } else if (action == 'remove') {
                if(0 != $triggerInput.data('attachment-id'))
                    _addAction(form, 'remove', $triggerInput.data('attachment-id'));
                else if($triggerInput.data('hiddenInputs')) {
                    var hiddenInputs = $triggerInput.data('hiddenInputs');
                    _removeAction(form, 'add', $(hiddenInputs.base64Input).attr('name'));
                    $triggerInput.data('hiddenInputs').removeAll();
                }

                if ($triggerInput.data('mime') != 'HEADIMAGE')
                    $(this).remove();
                else {
                    $(this).find('img').remove();
                    $(this).data('action', 'add');
                }

            } else if(action == 'edit') {
                $sizes = $triggerInput.data('size').split('x');
                var imagePromise = CroppieOverlay.open($sizes[0], $sizes[1]);

                imagePromise.done(function (resultObject) {
                    var thumbPrototype = resultObject.cropped;
                    var hiddenInputs = _addHiddenInputs($form, $triggerInput, resultObject);
                    var newInput = hiddenInputs.base64Input;
                    $triggerInput.find('img').remove();
                    $triggerInput.append(_base64ToImg(thumbPrototype));
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

    var _yieldTinyMceId = (function(){
        var currentId = -1;

        return function(){
            return ++currentId;
        }
    })();

    //handled onsuccess events
    var _OnSuccess = {
        ON_SUCCESS_ATTRIBUTE : 'programEditForm-onsuccess',

        initOnSuccessArray : function (form) {
            $(form).data(_OnSuccess.ON_SUCCESS_ATTRIBUTE, []);
        },
        attachEventHandlerOnSuccess : function (form, handler) {
            var onSuccessHandlers = $(form).data(_OnSuccess.ON_SUCCESS_ATTRIBUTE);
            $(form).data(_OnSuccess.ON_SUCCESS_ATTRIBUTE).push(handler);
        },

        callOnSuccessHandlers : function (form, response) {
            var onSuccessHandlers = $(form).data(_OnSuccess.ON_SUCCESS_ATTRIBUTE);
            for (var i in onSuccessHandlers)
                onSuccessHandlers[i]($(form), response);
        }
    };

    var _OnSubmit = {
        // form element on submit handler
        submitted : function(e) {
            var $form = $(this);

            e.preventDefault();
            $form.ajaxSubmit({
                beforeSubmit: function(formData, jqForm, options) {
                    var hasEditor = false;
                    formData.forEach(function(entry){
                        if(entry['type'] == 'textarea' && $(jqForm).find('[name="' + entry['name'] + '"]').is('.wysiwyg')) {
                            entry['value'] = tinymce.activeEditor.getContent();
                            hasEditor = true;
                        }
                    });
                    if(hasEditor) {

                    }
                },
                success: function (response) {
                    // @TODO: desynchronize
                    var response = JSON.parse(response);
                    if (response.success == true) {
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

        var $periodStartInput = $form.find('input.js-period-start');
        $periodStartInput.periodpicker({
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

        var $periodEndInput = form.find('input.js-period-end');
        var periodpickerSettingsEnd = {
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
        };
        $periodEndInput.periodpicker(periodpickerSettingsEnd);

        $periodStartInput.on('change', function(){
            function movePeriodEndInput(){
                var startVal = $this.val();
                var endVal =
                    moment(startVal, 'YYYY-MM-DD HH:mm:ss')
                        .add(1, 'hour')
                        .format('YYYY-MM-DD HH:mm:ss');

                $periodEndInput.periodpicker('destroy');
                var $periodEndInputClone = $periodEndInput.clone();
                $periodEndInputClone.val(endVal);
                $periodEndInputClone.attr('value', endVal);
                $periodEndInputClone.attr('data-timeto', endVal);
                $periodEndInputClone.data('timeto', endVal)

                $periodEndInput.replaceWith($periodEndInputClone);

                $periodEndInputClone.periodpicker(periodpickerSettingsEnd);

                $periodEndInput = $periodEndInputClone;
            }

            var $this = $(this);
            movePeriodEndInput();
        });
    };

    var _initButtonRemove = function(form) {
        var $form = $(form);
        $form.on('click', '.js-remove', function (event) {
            event.preventDefault();
            var href = $(this).attr('href');

            if (window.confirm('Opravdu si přejete nenávratně smazat tento program?')) {
                $.ajax({
                    url: href,
                    success: function (response) {
                        if(response.result)
                            _OnSuccess.callOnSuccessHandlers($form, response);
                        else
                            window.alert('Nastala neznámá chyba');
                    }
                });
            }
        });
    };

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
        _initButtonRemove($form);

        if(typeof(onSuccessHandler) != 'undefined')
            _OnSuccess.attachEventHandlerOnSuccess($form, onSuccessHandler);

        initTinyMce();

        $(form).find('textarea').each(function(){
            var tinyMceId = 'tinymce_' + _yieldTinyMceId();
            $(this).attr('id', tinyMceId);
            tinymce.execCommand('mceAddEditor', true, tinyMceId);
        });
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
        //registerOnSuccess : _registerOnSuccess
    }
}

window.OverlayManager = new function(){
    var _close = function() {
        $.magnificPopup.close();
    };

    var _openProgramEditForm = function(loadUrl, data) {
        var req = $.ajax({
            method: 'GET',
            url: loadUrl,
            data: (typeof(data) == 'undefined' ? {} : data)
        });

        var formDeferred = $.Deferred();
        req.done(function (resp) {
            $overlayWrap = $('<div class="overlay-wrap"></div>');
            $wrappedForm = $(resp).find('.program-edit-form-wrap');

            $overlayWrap.append($wrappedForm);

            $.magnificPopup.open({
                items: {
                    src: $overlayWrap
                },
                callbacks: {
                    'afterClose': function () {
                        $.magnificPopup.instance.content = null;
                    }
                }
            });
            ProgramEditForm.init($wrappedForm.find('form'), function(){
                formDeferred.resolve();
                _close();
            });
        });

        return formDeferred.promise();
    };

    return {
        openProgramEditForm : _openProgramEditForm
    };
};

window.Animation = {};
window.Animation.randomFadeIn = function(elements){
    $(elements).each(function () {
        var element = this;
        var timeout = Math.random() * 1000 + 1000;
        var tweenTime = Math.random() * 2;
        window.setTimeout(
            function () {
                TweenLite.to(element, tweenTime, {opacity: '1'});
            },
            timeout
        );
    });
}

$('.m-expander').click(function(){
    $($(this).data('expand')).slideToggle();
});

// onload
$(function(){
    $(document)
        .on('click', '.js-overlay-programEdit',
            function(e){
                var target = e.target;
                e.preventDefault();
                e.stopPropagation();

                OverlayManager.openProgramEditForm(
                    $(target).data('overlay-load-url')
                )
                .then(function () {
                    var reloadSelector = $(target).data('overlay-reload-selector')
                    if(reloadSelector) {
                        $.ajax({url: document.URL})
                            .then(function (result) {
                                $(reloadSelector).replaceWith($(result).find(reloadSelector));
                            });
                    }
                });
            }
        );

    //Animation.randomFadeIn($('.program-highlight-block .program-wrap'));
});
