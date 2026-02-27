/**
 * 2024 ALCALINK E-COMMERCE & SEO, S.L.L.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @author ALCALINK E-COMMERCE & SEO, S.L.L. <info@alcalink.com>
 * @copyright  2024 ALCALINK E-COMMERCE & SEO, S.L.L.
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * Registered Trademark & Property of ALCALINK E-COMMERCE & SEO, S.L.L.
*/
var inputb = "search_idprod";
var avpm_aplicacion_name = "compras";
var avpm_aplicacion = 0;
var sendDataAjax;
var video = document.getElementById('tokenphoto');
var ajaxSendingBlock = {};
var sendingsTimes = {};
var cacheAjax = {};
var blockpageloading = false;
var AfterdondeObject = false;

$(document).ready(function() {
    $(document).ajaxError(function(event, request, settings) {
        if (request.status == 403 || request.status == 402 || request.status == 500 || request.status == 404) {
            alert("Error requesting page " + settings.url + "");
        }

    });
    $(document).on('click', '.alca-toggle', function() {
        t = $(this).attr('data-target');
        if ($(t).is(':visible')) {
            $(t).hide();
        } else {
            $(t).show();
        }
    })

    $(document).on('click', '.AfterdondeObject', function() {
        $(AfterdondeObject).val($(this).html());
    });


    $(document).on('click', '.minimize', function() {
        $(this).parent().removeClass('maximized');
        $(this).parent().removeClass('minimized');
        height = $(this).parent().height();
        width = $(this).parent().width();
        height_original = parseInt($(this).parent().attr('data-height'));
        width_original = parseInt($(this).parent().attr('data-width'));
        if ($(this).parent().attr('data-height') == undefined || $(this).parent().attr('data-height') == 'undefined') {
            $(this).parent().attr('data-height', height);
            $(this).parent().attr('data-width', width);
            height_original = height;
            width_original = width;
        }
        width = width_original;
        if (height_original < height) {
            height = height_original;
        } else {
            height = 22;
        }
        $(this).parent().height(height);
        $(this).parent().width(width);
    })

    $(document).on('click', '.maximize', function() {
        $(this).parent().removeClass('maximized');
        $(this).parent().removeClass('minimized');
        height = $(this).parent().height();
        width = $(this).parent().width();
        height_original = parseInt($(this).parent().attr('data-height'));
        width_original = parseInt($(this).parent().attr('data-width'));
        if ($(this).parent().attr('data-height') == undefined || $(this).parent().attr('data-height') == 'undefined') {
            $(this).parent().attr('data-height', height);
            $(this).parent().attr('data-width', width);
            height_original = height;
            width_original = width;
        }
        if (height_original > height) {
            height = height_original * 1;
            width = width_original * 1;
        } else {
            $(this).parent().addClass('maximized');
            height = height_original * 3;
            width = width_original * 3;
        }
        $(this).parent().height(height);
        $(this).parent().width(width);
    })

    $(document).on('dblclick', 'img', function() {
        if ($('#imageQueryBig').length == 0) {
            style = "position: absolute; width: 100vw; z-index: 99999; left: 0px; display: none;"
            html = '<div id="imageQueryBig" style="' + style + '"><div class="closeparent buttonclose col-md-12"></div><img class="col-md-12" /></div>';
            $('body').append(html);
        }
        src = $(this).attr('src');
        $('#imageQueryBig img').first().attr('src', src);
        $('#imageQueryBig').show();
    })

    $(document).on('click', '#processCustomerUpload', function(e) {
        procesarXML(0);
    })

    //  document.g etElementById("data_third").className += " tagheadervisible";
    $('#CSV').addClass('tagheadervisible');
    alto = screen.height;
    if ($("#flexgrid").length > 0) {
        document.getElementById("flexgrid").style.height = (alto - 600) + "px";
    }
    $('.dropzone').each(function() {
        uploadfileform(this);
    })
    $(document).on("click", ".alertdelete", function(event) {
        texto = 'Are you sure you want to delete?';
        rr = typeof($(this).attr('data-text'));
        if (rr != undefined && rr != 'undefined')
            texto = $(this).attr('data-text');
        r = confirm(texto);
        if (r == true) {
            return true;
        } else {
            event.stopPropagation();
            event.preventDefault();
            event.stopImmediatePropagation();
            return false;
        }

    });
    $(document).on('click', '[type="checkbox"]', function() {
        valor = $(this).val();
        /*
        if (valor == 'undefined' || valor == undefined || valor == false || valor == 'false') {
            $(this).val('true');
        } else {
            $(this).val('false'); 
        }
        */
    })
    $(document).on('click', '.alca-traslate', function() {
        from = $(this).attr('data-from');
        to = $(this).attr('data-to');
        $(this).remove();
        r = $(from).html();

        $(to).html(r);
        $(to).find('.hideimportant').removeClass('hideimportant');
        $(to).parents().show();
        $(from).remove();
    })

    $(document).on('click', '.alca-copy', function() {
        from = $(this).attr('data-from');
        to = $(this).attr('data-to');
        r = $(from).html();
        $(to).html(r);
        $(to).find('.hideimportant').removeClass('hideimportant');
        $(to).parents().show();
    })

    $('.hoverInformativoClose').on('click', function() {
        $('#hoverInformativo').fadeOut();
    })

    $('#search_idprod').keypress(function(event) {
        xhttp2 = new XMLHttpRequest();
        xhttp2.onreadystatechange = function() {
            if (xhttp2.readyState == 4 && xhttp2.status == 200) {
                //  console.log(xhttp2.responseText);
                productinsertinformationgrid(xhttp2.responseText);
            }
        };
        str = document.getElementById("search_idprod").value;

        //     str = $('#search_idprod').attr('value');


        if ((event.keyCode < 48 && event.keyCode > 57) || (event.keyCode < 65 && event.keyCode > 90) || (event.keyCode < 97 && event.keyCode > 122)) {

        } else {
            data = str + String.fromCharCode(event.keyCode);

            col = document.getElementById("grid" + gridXpositionCell).getAttribute('name');
            row = gridYpositionCell;


            //y = gridYpositionCell;

            //     xhttp2.open("GET", "funciones/searchproduct.php?" + "value=" + data + "&col=" + col+"&row="+row, true);
            //            xhttp2.send();

        }

    });

    $(document).on('keyup', function(event) {
        if ((event.keyCode > 36 && event.keyCode < 41)) {
            $('.removewhenkeypress').remove();
        }
    });

    $(document).on('click', function(event) {
        z = $(this.activeElement).parents('.removewhenkeypress');
        if ($(this.activeElement).prop("tagName") != 'BODY' && $(this).prop("tagName") != 'BODY' && $(z).prop("tagName") != 'BODY' && $(z).length == 0 && !$(this).hasClass('removewhenkeypress')) {
            $('.removewhenkeypress').remove();
        }
    });
    /*
        $('#head_data_name1').keypress(function (event) {
            xhttp2 = new XMLHttpRequest();
            xhttp2.onreadystatechange = function () {
                if (xhttp2.readyState == 4 && xhttp2.status == 200) {

            document.getElementById("search_by_name").innerHTML = xhttp2.responseText;

            document.getElementById("search_by_name").style.display = "block";
            document.getElementById("search_by_name").style.top = document.getElementById("head_data_name").style.height;
                    productinsertinformationgrid(xhttp2.responseText);
                }
            };
            if ((event.keyCode < 48 && event.keyCode > 57) || (event.keyCode < 65 && event.keyCode > 90) || (event.keyCode < 97 && event.keyCode > 122)) {

            } else {
                str = document.getElementById("head_data_name").value;
                str = $('#head_data_name').attr('value');

                data = str + String.fromCharCode(event.keyCode);
                //y = gridYpositionCell;
                xhttp2.open("GET", "funciones/searchsociete.php?" + "nom=" + data, true);
                xhttp2.send();
            }
        });
         */
    $(document).on('input', '[data-keyuppercase]', function(e) {
        $(this).val($(this).val().toUpperCase());
    });
    $(document).on('click', '.tab', function() {
        $(this).parent().find('.tab').removeClass('active');
        $(this).addClass('active');
        t = $(this).attr('target');
        $(t).parent().find('.tab-panel').addClass('hide');
        $(t).parent().find('.tab-panel').css('display', '');
        $(t).removeClass('hide');
    })

    $(document).on('click', '[data-click-text]', function() {
        text = $(this).attr('data-click-text')
        $(this).html(text);
    })

    $(document).on('click', '#alc_modules .btn-save-general, #alc_modules .alca-save', function(event) {
        if ($(this).hasClass('alca-newwindow')) {
            href = $(this).attr('href');
            var w = window.open(href, '_blank');
            return false;
        }

        if ($(this).attr('alca-newwindow') == 'true') {
            href = $(this).attr('href');
            var w = window.open(href, '_blank');
            return false;
        }
        if ($(':focus').attr('alca-noEnter') == 'true') {
            event.preventDefault();
            event.stopPropagation();
            return false;
        }
        event.stopPropagation();
        event.preventDefault();
        extravalues = [];
        if ($(this).hasClass('showConsole'))
            openConsole();
        executeAjaxCall(this, extravalues);
    })




    $(document).on('scroll', function(event) {
        alcaScroll();
    })

    $(document).on('mousewheel', function(event, delta) {
        if (delta == 1 || delta == -1) {
            alcaScroll();;
        }

    });

    $(document).on('click', '.alca-open-scroll', function(event) {
        objecto = this;
        bloquear = 0;
        $(objecto).parents().each(function() {

            zz = $(this).is(':visible');
            if ($(this).is(':visible') == false) {
                bloquear = 1;
                return;
            }
        });
        if (bloquear == 1)
            return;
        ptop = $(objecto).offset().top - ($(window).scrollTop() + 1500);
        extravalues = [];
        var d = new Date();
        var n = d.getTime();
        ajaxObjectTarget = 'ajaxObjectTarget' + n;
        extravalues['ajaxObjectTarget'] = '.' + ajaxObjectTarget;
        extravalues['extendlist'] = true;
        if (ptop < ($(window).height() * 2)) {
            $(objecto).addClass(ajaxObjectTarget);
            $(objecto).addClass('alca-loading');
            $(objecto).removeClass('alca-open-scroll');
            executeAjaxCall(objecto, extravalues);
        }
    });
    /*
    $(document).on('change', '.alca-keypress' ,function(event){
            event.stopPropagation();
            event.preventDefault();
            extravalues = [];
            if ($(this).hasClass('showConsole'))
                openConsole();
            executeAjaxCall(this, extravalues);
    })
    */
    /* */
    $(document).on('change', '.alca-change', function(event) {
        event.stopPropagation();
        event.preventDefault();
        extravalues = [];
        if ($(this).hasClass('showConsole'))
            openConsole();
        executeAjaxCall(this, extravalues);
    })


    $(document).on('keyup', '.alca-save-input', function(event) {
        if (event.keyCode == 13) {
            event.stopPropagation();
            event.preventDefault();
            extravalues = [];
            if ($(this).hasClass('showConsole'))
                openConsole();
            executeAjaxCall(this, extravalues);
        }
    })

    $(document).on('keyup', function(e) {
        if (e.keyCode > 0 && e.ctrlKey) {
            $('[data-keyup="' + e.keyCode + '"]').trigger('click');
        }
    });

    var keypressAjaxSending = {};
    $(document).on('input', '.alca-keypress', function(event) {
        if ($(this).attr('blockobject') != 'undefined' && $(this).attr('blockobject') != undefined) {
            $($(this).attr('blockobject')).attr('disabled', 'disabled');
        }
        if ($(this).attr('alca-noEnter') == 'true') {
            event.preventDefault();
            event.stopPropagation();
        }
        if (event.keyCode != 13) {
            extravalues = [];
            extravalues[$(this).attr('name')] = $(this).val();
            if ($(this).hasClass('showConsole'))
                openConsole();
            if (typeof(keypressAjaxSending[$(this).attr('id')]) != undefined && typeof(keypressAjaxSending[$(this).attr('id')]) != 'undefined') {
                clearInterval(keypressAjaxSending[$(this).attr('id')]);
            }
            keypressAjaxSending[$(this).attr('id')] = setTimeout(function(ob, extravalues) {
                executeAjaxCall(ob, extravalues);
            }, 300, this, extravalues)
        }
    })

    $(document).on('click', '.removeparent', function() {
        $(this).parent().remove();
    });

    $(document).on('click', '.closeparent', function() {
        $(this).parent().hide();
        $(this).parents('.panelcloseable').first().hide();
    });
    $(document).on('click', '.closepanel', function() {
        $(this).parents('.panelcloseable').first().hide();
    });

    $(document).on('click', '[data-triggerClick]', function() {
        o = $(this).attr('data-triggerClick');
        $(o).trigger('click');
    });


    reloadjavascript();
    /**/
    //var video = document.getElementById('tokenphoto');
    /*
    video = document.getElementById('tokenphoto');
    if(navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
        navigator.mediaDevices.getUserMedia({ 
            video: true, 
            deviceId: typeof(videoSource) != 'undefined' ? { exact: videoSource } : undefined,
        }).then(function(stream) {
            if (video != null) {
                video.srcObject = stream;
                video.play();
            }
        });
    }
    */
    forcetrigger();
});

function alcaScroll() {
    $('.alca-open-scroll').each(function() {
        objecto = this;
        bloquear = 0;
        $(objecto).parents().each(function() {

            zz = $(this).is(':visible');
            if ($(this).is(':visible') == false) {
                bloquear = 1;
                return;
            }
        });
        if (bloquear == 1)
            return;
        elementWindow = window;
        if ($(this).parents('.ajax-scroll-block').length > 0) {
            elementWindow = $(this).parents('.ajax-scroll-block').first();
        }
        ptop = $(objecto).offset().top - ($(elementWindow).scrollTop() + 1500);
        extravalues = [];
        var d = new Date();
        var n = d.getTime();
        ajaxObjectTarget = 'ajaxObjectTarget' + n;
        extravalues['ajaxObjectTarget'] = '.' + ajaxObjectTarget;
        extravalues['extendlist'] = true;
        if (ptop < ($(window).height() * 4)) {
            $(objecto).addClass(ajaxObjectTarget);
            $(objecto).addClass('alca-loading');
            $(objecto).removeClass('alca-open-scroll');
            executeAjaxCall(objecto, extravalues);
        }
    });
}

function openConsole() {
    $('#adpmConsole').show();
    $('#adpmConsole > div').hide();
    $('#adpmConsole .closeparent').show();
    $('#adpmConsoleContent').html('');
}

function imprimir(documento) {
    console.log(documento);
    var ventimp = window.open(' ', 'popimpr');
    ventimp.document.write(documento);
    ventimp.document.close();
    ventimp.print();
    ventimp.close();
}



function executeAjaxCall(object, extravalues) {
    sendDataAjax = setTimeout(function(object, extravalues) {
        if ($.active > 3 && 1 == 2) {
            alert('El sistema está saturado, por favor espere 1 minuto para continuar');
            return false;
        }
        if (object.tagName == 'FORM') {
            form = object;
        } else {
            form = $(object).parents('form');
        }
        if ($(object).hasClass('forced')) {
            form = object;
        }
        if (form.length == 0) {
            values = {};
        } else {
            values = {};
            inputs = form;
            $(inputs).find("input,select,text,textarea").each(function() {
                if ($(this).attr('type') != 'submit') {
                    if (this.tagName == 'textarea') {
                        a = this.tagName;
                        aa = $(this).html();
                    }
                    if ((($(this).attr('type') != 'checkbox' && $(this).attr('type') != 'radio') || $(this).is(':checked'))) {
                        if (this.tagName == 'textarea') {
                            a = this.tagName;
                            aa = $(this).html();
                        }
                        if (this.name.search(/\[\]/) > 0) //search for [] in name
                        {
                            /*
                            if (typeof values[this.name] != "undefined") {
                                values[this.name] = values[this.name].concat([$(this).val()])
                            } else {
                                if (typeof values[this.name] != "undefined") values[this.name] = [$(this).val()];
                            }
                            */
                            name_a = this.name.replace('[]', '');
                            name_i = 0;
                            if (typeof values[name_a] != "undefined") {
                                name_i = values[name_a].length;
                            } else {
                                values[name_a] = [];
                            }
                            values[name_a][name_i] = $(this).val();
                        } else {
                            if (this.name != "") values[this.name] = $(this).val();
                        }
                    }
                } else {
                    if (object == this) {
                        if (this.name != "") values[this.name] = $(this).val();
                    }
                }
            });
        }
        if (typeof(extravalues) != 'object') {
            if (!(extravalues instanceof FormData)) {
                for (i in extravalues) {
                    values[i] = extravalues[i];
                }
            }
        } else {
            for (i in extravalues) {
                values[i] = extravalues[i];
            }
        }
        if (typeof(globalExtraAjaxValues) != undefined && typeof(globalExtraAjaxValues) != 'undefined') {
            for (i in globalExtraAjaxValues) {
                values[i] = globalExtraAjaxValues[i];
            }
        }
        if ($(object).attr('id') != undefined)
            values[$(object).attr('id')] = $(object).val();
        datas = values;
        if (typeof(globalExtraAjaxValues) != undefined && typeof(globalExtraAjaxValues) != 'undefined')
            url = url_ajax_adpm;
        href = $(form).attr('action');
        if (href)
            url = href;
        href = $(object).attr('href');
        if (href)
            url = href;
        datas['urlajaxcalled'] = url;
        if ($(object).attr('data-target') != undefined)
            datas['ajaxObjectTargetAdd'] = $(object).attr('data-target');
        if ($(form).attr('data-target') != undefined) {
            $v = 1;
            if (typeof(datas['ajaxObjectTarget']) != 'undefined' && typeof(datas['ajaxObjectTarget']) != undefined) {
                if (datas['ajaxObjectTarget'].indexOf('ajaxObjectTarget') > 0) {
                    $v = 0;
                }
            }
            if ($v == 1)
                datas['ajaxObjectTargetAdd'] = $(form).attr('data-target');
        }

        if (typeof(gridYpositionCell) != undefined && typeof(gridYpositionCell) != 'undefined') {
            datas['gridYpositionCell'] = gridYpositionCell;
            datas['gridXpositionCell'] = gridXpositionCell;
        }
        if ($(form).attr('data-sendHtml') == 'true') {
            f = $(form).clone();
            $(f).find('button').remove();
            $(f).find('a').remove();
            datas['sendHtml'] = $(f).html();
        }
        if (blockpageloading == true)
            return false;
        $.fancybox.show();
        if ($(object).hasClass('blockpageloading')) {
            $('#mainbody').addClass('blockpageloading');
            blockpageloading = true;
        }


        methodtype = "POST";

        if ($(object).hasClass('cacheAjax')) {
            if (typeof(cacheAjax[url]) != 'undefined' && typeof(cacheAjax[url]) != undefined) {
                executeAjaxResponse(cacheAjax[url], '', '');
                return true;
            }
        }
        if (typeof(inputs) != 'undefined' && typeof(inputs) != 'undefined') {
            if ($(inputs).find('input').first().attr('type') == 'file') {
                extravalues = new FormData();
                img = $(inputs).find('input').first()[0].files[0];

                extravalues.append('file', img);
            }
        }

        f = typeof(extravalues);
        if (typeof(extravalues) == 'object') {
            if (extravalues instanceof FormData) {
                valores = extravalues;
                for (i in datas) {
                    valores.append(i, datas[i]);
                }
                datas = valores;
                $.ajax({
                    url: url + "&ajaxaction=true",

                    type: "post",
                    dataType: "html",
                    data: datas,
                    cache: false,
                    contentType: false,
                    processData: false,
                    context: { url: url, obj: object }
                }).done(function(response, data, data2) {
                    if ($(this.obj).hasClass('cacheAjax')) {
                        cacheAjax[this.url] = response;
                    }
                    if ($(this.obj).attr('blockobject') != 'undefined' && $(this.obj).attr('blockobject') != undefined) {
                        $($(object).attr('blockobject')).removeAttr('disabled');
                    }

                    $('#mainbody').removeClass('blockpageloading');
                    blockpageloading = false;
                    executeAjaxResponse(response, data, data2);
                });
                return true;
            }
        }

        if ($(object).attr('data-method') == 'GET') {
            methodtype = 'GET';
            $.ajax({
                url: url + "&ajaxaction=true",
                type: methodtype,
                context: document.body,
            }).done(function(response, data, data2) {
                $.fancybox.hide();
                v = response['Global Quote'];
                v = v['05. price'];
                v = (v / 0.2870) / 1.279;
                v = Math.round(v * 100) / 100;
                $('#goldfinanzalprice').html(v);
            }).error(function(xhr, ajaxOptions, thrownError) {
                alert(xhr.status);
                alert(thrownError);
            });


        } else {
            $.ajax({
                url: url + "&ajaxaction=true",
                type: methodtype,
                data: datas,
                context: { url: url, obj: object }
            }).done(function(response, data, data2) {
                if (response.indexOf('login_table_title') > 0) {
                    alert('Sesión caducada, reinicie el programa.');
                }
                if ($(this.obj).hasClass('cacheAjax')) {
                    cacheAjax[this.url] = response;
                }
                if ($(this.obj).attr('blockobject') != 'undefined' && $(this.obj).attr('blockobject') != undefined) {
                    $($(object).attr('blockobject')).removeAttr('disabled');
                }

                $('#mainbody').removeClass('blockpageloading');
                blockpageloading = false;
                executeAjaxResponse(response, data, data2, this);
            });
        }

    }, 10, object, extravalues);
}

function executeAjaxResponse(response, data, data2, objeto = false) {
    $.fancybox.hide();
    n = typeof(response);
    if (n == 'undefined' || n == undefined)
        return false;
    if (n == 'string') {
        nn = response.trim().trim().trim().trim().trim().trim().trim();
        if (nn == 'null')
            return false;
    }
    respuesta = jQuery.parseJSON(response);

    if (typeof(respuesta.showSuccessMessage) != 'undefined') {
        for (i in respuesta.showSuccessMessage) {
            showSuccessMessage(respuesta.showSuccessMessage[i]);
        }
    }
    if (typeof(respuesta.showErrorMessage) != 'undefined') {
        for (i in respuesta.showErrorMessage) {
            showErrorMessage(respuesta.showErrorMessage[i]);
        }
    }
    if (typeof(respuesta.showNoticeMessage) != 'undefined') {
        for (i in respuesta.showNoticeMessage) {
            showNoticeMessage(respuesta.showNoticeMessage[i]);
        }
    }

    if (typeof(respuesta.modal) != 'undefined') {
        for (i in respuesta.modal) {
            h = $('#alcaModal').find('.modal-body').first().html(respuesta.modal[i]);
            $('#alcaModal').modal();
        }
    }
    
    if (typeof(respuesta.loadurl) != 'undefined') {
        window.open(respuesta.loadurl, '_self');
    }
    if (typeof(respuesta.callajax) != 'undefined') {
        setTimeout(function(url) {
            extravalues = [];
            ob = $('<a class="alca-save forced" href="' + url + '" ></a>');
            executeAjaxCall(ob, extravalues);
        }, 50, respuesta.callajax);
    }
    if (typeof(respuesta.callajaxs) != 'undefined') {
        setTimeout(function(callajaxs) {
            for (i in callajaxs) {
                extravalues = [];
                ob = $('<a class="alca-save forced" href="' + callajaxs[i] + '" ></a>');
                executeAjaxCall(ob, extravalues);
            }
        }, 50, respuesta.callajaxs);
    }
    if (typeof(respuesta.scriptVars) != 'undefined') {
        for (i in respuesta.scriptVars) {
            if ($(respuesta.scriptVars[i]).length > 0) {
                if (typeof(respuesta.scriptVars[i]) == 'object') {
                    var scriptStr = "var " + i + "=" + JSON.stringify(respuesta.scriptVars[i]) + ";";
                } else {
                    var scriptStr = "var " + i + "= \"" + respuesta.scriptVars[i] + "\"";
                }


                var node_scriptCode = document.createTextNode(scriptStr)
                var node_script = document.createElement("script");
                node_script.type = "text/javascript"
                node_script.appendChild(node_scriptCode);

                var node_head = document.getElementsByTagName("head")[0]
                node_head.appendChild(node_script);
            }
        }
    }

    if (typeof(respuesta.executeFunction) != 'undefined') {
        if (respuesta.executeFunction == 'setSearchList')
            setSearchList(response, data, data2);
        return;
    }

    if (typeof(respuesta.showobjects) != 'undefined') {
        for (i in respuesta.showobjects) {
            if ($(respuesta.showobjects[i]).length > 0) {
                $(respuesta.showobjects[i]).show();
            }
        }
    }
    if (typeof(respuesta.disableobject) != 'undefined') {
        for (i in respuesta.disableobject) {
            if ($(respuesta.disableobject[i]).length > 0) {
                $(respuesta.disableobject[i]).attr('disabled', 'disabled');
            }
        }
    }
    if (typeof(respuesta.enableobject) != 'undefined') {
        for (i in respuesta.enableobject) {
            if ($(respuesta.enableobject[i]).length > 0) {
                $(respuesta.enableobject[i]).removeAttr('disabled');
            }
        }
    }

    if (typeof(respuesta.alert) != 'undefined')
        alert(respuesta.alert);
    if (typeof(respuesta.donde) != 'undefined' && typeof(respuesta.texto) != 'undefined') {
        $(respuesta['donde']).html(respuesta['texto']);
        $(respuesta['donde']).show();
        $(respuesta['donde']).parent().show();
        $(respuesta['donde']).parents('.showajax').first().show();
        $(respuesta['donde']).parents('.console').show();
        if ($(respuesta['donde']).parents('.tab-panel').length > 0) {
            $(respuesta['donde']).parents('.tab-panel').first().parent().find('.tab-panel').addClass('hide');
            $(respuesta['donde']).parents('.tab-panel').first().removeClass('hide');
        }

    }
    if (typeof(respuesta.dondeReplace) != 'undefined') {
        $(respuesta['dondeReplace']).replaceWith(respuesta['texto']);
    }
    if (typeof(respuesta.donde2) != 'undefined') {
        $(respuesta['donde2']).html(respuesta['texto2']);
    }
    if (typeof(respuesta.dondeAdd) != 'undefined') {
        $(respuesta['dondeAdd']).append(respuesta['texto']);
        $(respuesta['dondeAdd']).show();
    }
    if (typeof(respuesta.togglethis) != 'undefined') {
        if (objeto.obj != false) {
            if ($(objeto.obj).length > 0) {
                $(objeto.obj).parent().find('.togglethis').remove();
                if (respuesta.togglethis != 'false') {
                    o = 'object' + (new Date()).getTime();
                    h = $(objeto.obj).outerHeight();
                    w = $(objeto.obj).width();
                    r = $('<div class="togglethis displayclick" style="width:30px"><i class="fas fa-sort-down" style="text-align: center;line-height:' + h + 'px;width: 30px;height:' + h + 'px;display:table"></i><div style="right:0px;margin:0px;width:' + w + 'px" class="menutoggle" id="' + o + '">' + respuesta.togglethis + '</div></div>');

                    x = $(objeto.obj).offset().left + $(objeto.obj).width() - 34; //$(this).offset().top - $(this).parent().offset().top + 50;
                    x = w;
                    y = 0; //$(this).offset().left;
                    r = $(r);
                    $(r).show();
                    $(r).css('z-index', '9');
                    $(r).css('padding', '0');
                    //$(r).css('position', 'absolute');
                    $(r).css('top', y);
                    $(r).css('left', x);
                    $(r).css('margin', '0px');
                    $(r).css('margin-top', (h * -1) + 'px');
                    $(r).css('margin-left', '-7px');

                    $(objeto.obj).after(r);
                    $(r).show();
                    setTimeout(function(x, o) {
                        w = $(o).width() + x;
                        w2 = $(window).width();
                        if (w > w2) {
                            $(o).css('left', 'auto');
                            $(o).css('right', 20);
                        }
                    }, 1, x, r);
                }
            }
        }
    }
    if (typeof(respuesta.Afterdonde) != 'undefined') {
        if (respuesta['Afterdonde'] == 'this')
            respuesta['Afterdonde'] = objeto.obj;
        AfterdondeObject = objeto.obj;
        $('.hoverpanel').remove();
        r = $(respuesta['texto']);
        $(r).addClass('hoverpanel');

        x = $(respuesta['Afterdonde']).offset().left; //$(this).offset().top - $(this).parent().offset().top + 50;
        y = $(respuesta['Afterdonde']).offset().top + $(respuesta['Afterdonde']).height() + 3; //$(this).offset().left;
        o = $(r);
        $(o).show();
        $(o).css('position', 'absolute');
        $(o).css('top', y);
        $(o).css('left', x);
        $(o).css('margin', '0px');
        $('body').before(r);
        $(respuesta['Afterdonde']).show();
        setTimeout(function(x, o) {
            w = $(o).width() + x;
            w2 = $(window).width();
            if (w > w2) {
                $(o).css('left', 'auto');
                $(o).css('right', 20);
            }
        }, 1, x, o);
    }

    if (typeof(respuesta.textosAdd) != 'undefined') {
        for (i in respuesta.textosAdd) {
            if ($(i).length > 0) {
                if ($(i).is(':visible')) {
                    $('html, body').animate({ scrollTop: ($(i).offset().top - 200) }, 400);
                }
                zz = $(i)[0].tagName;
                if ($(i)[0].tagName == 'INPUT') {
                    $(i).val(respuesta.textosAdd[i]);
                } else {
                    if (typeof(respuesta.resultsget) != 'undefined') {
                        resulta = $('<div></div>');
                        $(respuesta.textosAdd[i]).find(respuesta.resultsget).each(function() {
                            $(this).addClass('col-md-6');
                            $(resulta).append(this);
                        });
                        respuesta.textosAdd[i] = $(resulta).html();
                    }
                    $(i).append(respuesta.textosAdd[i]);
                    $(i).show();
                    $(i).parent().show();
                    $(i).parent().parent().show();
                    $(i).parents('.console').show();
                    if ($(i).parents('.tab-pane').length > 0) {
                        $(i).parents('.tab-pane').each(function() {
                            $('[href="#' + $(this).attr('id') + '"]').trigger('click');
                        });
                    }
                }
                $(respuesta['donde']).show();
                $(respuesta['donde']).parent().show();
            }
        }
    }

    if (typeof(respuesta.textos) != 'undefined') {
        for (i in respuesta.textos) {
            if ($(i).length > 0) {
                if ($(i).is(':visible')) {
                    $('html, body').animate({ scrollTop: ($(i).offset().top - 200) }, 400);
                }
                zz = $(i)[0].tagName;
                if ($(i)[0].tagName == 'INPUT') {
                    $(i).val(respuesta.textos[i]);
                } else {
                    if (typeof(respuesta.resultsget) != 'undefined') {
                        resulta = $('<div></div>');
                        $(respuesta.textos[i]).find(respuesta.resultsget).each(function() {
                            $(this).addClass('col-md-6');
                            $(resulta).append(this);
                        });
                        respuesta.textos[i] = $(resulta).html();
                    }
                    $(i).html(respuesta.textos[i]);
                    $(i).show();
                    $(i).parent().show();
                    $(i).parent().parent().show();
                    $(i).parents('.console').show();
                    if ($(i).parents('.tab-pane').length > 0) {
                        $(i).parents('.tab-pane').each(function() {
                            $('[href="#' + $(this).attr('id') + '"]').trigger('click');
                        });
                    }
                }
                $(respuesta['donde']).show();
                $(respuesta['donde']).parent().show();
            }
        }
    }
    if (typeof(respuesta.reemplaces) != 'undefined') {
        for (i in respuesta.reemplaces) {
            if ($(i).length > 0) {
                zz = $(i)[0].tagName;
                if ($(i)[0].tagName == 'INPUT') {
                    $(i).val(respuesta.reemplaces[i]);
                } else {
                    if (typeof(respuesta.resultsget) != 'undefined') {
                        resulta = $('<div></div>');
                        $(respuesta.reemplaces[i]).find(respuesta.resultsget).each(function() {
                            $(this).addClass('col-md-6');
                            $(resulta).append(this);
                        });
                        respuesta.reemplaces[i] = $(resulta).html();
                    }
                    $(i).replaceWith(respuesta.reemplaces[i]);
                    $(i).show();
                    $(i).parent().show();
                    $(i).parent().parent().show();
                    $(i).parents('.console').show();
                }
                $(respuesta['donde']).show();
                $(respuesta['donde']).parent().show();
            }
        }
    }

    if (typeof(respuesta.imprimir) != 'undefined')
        imprimir(respuesta.texto);
    if (respuesta.CONTINUE_SAVE) {
        object = $('<a href="' + respuesta.urlajaxcalled + '"></a>')
        extravalues = [];
        extravalues['CONTINUE_SAVE'] = respuesta.CONTINUE_SAVE;
        setTimeout(function(object, extravalues) {
            executeAjaxCall(object, extravalues);
        }, 1000, object, extravalues);
    }
    if (typeof(respuesta.refreshImages) != 'undefined') {
        for (i in respuesta.refreshImages) {
            if ($(respuesta.refreshImages[i]).length > 0) {
                $(respuesta.refreshImages[i]).show();
                $(respuesta.refreshImages[i]).find('img').each(function() {
                    refreshImg(this);
                });
            }
        }
    }
    if (typeof(respuesta.closeobject) != 'undefined')
        $(respuesta['closeobject']).hide();
    if (typeof(respuesta.closeobjects) != 'undefined') {
        for (i in respuesta.closeobjects) {
            if ($(respuesta.closeobjects[i]).length > 0) {
                $(respuesta.closeobjects[i]).hide();
            }
        }
    }
    if (typeof(respuesta.showobjects) != 'undefined') {
        for (i in respuesta.showobjects) {
            if ($(respuesta.showobjects[i]).length > 0) {
                $(respuesta.showobjects[i]).show();
            }
        }
    }
    if (typeof(respuesta.executeTrigger) != 'undefined') {
        for (i in respuesta.executeTrigger) {
            if ($(i).length > 0) {
                $(i).trigger(respuesta.executeTrigger[i]);
            }
        }
    }

    if (typeof(respuesta.sound) != 'undefined') {
        var context = new AudioContext();
        var o = context.createOscillator();
        o.frequency.setTargetAtTime(respuesta.sound, context.currentTime, 0);
        o.connect(context.destination);
        o.start(0);
        setTimeout(function() {
            o.stop(0);
        }, 500);
    }
    if (typeof(respuesta.focus) != 'undefined') {
        setTimeout(function(respuesta) {
            for (i in respuesta.focus) {
                $(respuesta.focus[i]).focus();
            }
        }, 50, respuesta)
    }

    forcetrigger();


    alcaScroll();
    reloadjavascript();
}

function forcetrigger() {
    setTimeout(function() {
        $('.forcetrigger').each(function() {
            $(this).removeClass('forcetrigger');
            if ($(this).attr('data-wait') != 'undefined' && $(this).attr('data-wait') != undefined) {
                t = parseInt($(this).attr('data-wait'));
                if (typeof(sendingsTimes[$(this).attr('id')]) != 'undefined') {
                    clearInterval(sendingsTimes[$(this).attr('id')]);
                }
                sendingsTimes[$(this).attr('id')] = setTimeout(function(obj) {
                    $(obj).trigger('click');
                }, t, this);

            } else {
                $(this).trigger('click');
            }
        })
        $('[data-imUnique]').each(function() {
            cl = $(this).attr('data-imUnique');
            $('.' + cl).removeClass(cl);
            $(this).addClass(cl);
        })
    }, 50)
}

function refreshImg(img) {
    // the core of answer is 2 lines below
    var dummy = '?dummy=';
    $(img).attr('src', $(img).attr('src').split(dummy)[0] + dummy + (new Date()).getTime());

    // remove call on production
    if (typeof(updateImgVisualizer) != undefined && typeof(updateImgVisualizer) != 'undefined')
        updateImgVisualizer();
};

$(document).on('click', '.selectorquery', function() {
    target = $(this).attr('href');
    value = $(this).attr('value');
    $(target).val(value);
    $(this).parent().find('.selectorquery').removeClass('selected');
    $(this).addClass('selected');
});

function uploadfileform(elemento) {
    $.fn.dropzone.uploadStarted = function(fileIndex, file) {


        $('#hoverInformativo').fadeOut();
        if (typeof(gridYpositionCell) != undefined && typeof(gridYpositionCell) != 'undefined') {
            file.name = gridYpositionCell + '.jpg';
        } else {
            file.name = 'file' + '.jpg';
        }

        var infoDiv = $(".dropzone-info");
        infoDiv.attr("id", "dropzone-info" + fileIndex);
        infoDiv.html("upload started: " + file.fileName);

        var progressDiv = $("<div></div>");
        progressDiv.css({
            'background-color': 'orange',
            'height': '20px',
            'width': '0%'
        });
        progressDiv.attr("id", "dropzone-speed" + fileIndex);

        var fileDiv = $("<div>Subiendo archivos</div>");
        fileDiv.addClass("dropzone-info");
        fileDiv.css({
            'border': 'thin solid black',
            'margin': '5px'
        });
        fileDiv.append(infoDiv);
        fileDiv.append(progressDiv);

        $("#dropzone-info").after(fileDiv);
    };
    $.fn.dropzone.uploadFinished = function(fileIndex, file, duration) {
        $(".dropzone-info").html("upload finished: " + file.fileName + " (" + getReadableFileSizeString(file.fileSize) + ") in " + (getReadableDurationString(duration)));
        $("#dropzone-speed" + fileIndex).css({
            'width': '100%',
            'background-color': 'green'
        });
    };
    $.fn.dropzone.fileUploadProgressUpdated = function(fileIndex, file, newProgress) {
        $("#dropzone-speed" + fileIndex).css("width", newProgress + "%");
    };
    $.fn.dropzone.fileUploadSpeedUpdated = function(fileIndex, file, KBperSecond) {
        var dive = $("#dropzone-speed" + fileIndex);

        dive.html(getReadableSpeedString(KBperSecond));
    };
    $.fn.dropzone.newFilesDropped = function() {
        $(".dropzone-info").remove();
    };
    $(elemento).first().dropzone({
        url: url_upload + "grid/ajaxSearchProduct.php?" + "&operacion=uploadphoto",
        printLogs: true,
        uploadRateRefreshTime: 500,
        numConcurrentUploads: 2
    });

}
/*
$(document).on('mouseover', '.alca-date', function() {
    //$('.alca-date').datepicker( "destroy" ); 
    if (!$(this).hasClass('ready')) {
        $(this).addClass('ready');
        $(this).datepicker({
                    dateFormat: 'dd/mm/yy',
                    autoclose: true,
                    todayHighlight: true,
                    buttonImageOnly: true,
                    onClose: function( selectedDate ) {  
                        $(this).removeClass('ready');
                        $( this ).datepicker( "destroy" );  
                    }
        });
    }
});
*/
$(document).on('focus', '.alca-date', function() {
    id = $(this).attr('data-id2');
    $(this).attr('id', id);
})

function reloadjavascript() {
    $('.alca-dropzone').each(function() {
        $(this).removeClass('alca-dropzone');
        $(this).addClass('alca-dropzone-loaded');
        uploadfileform(this);

    })
    $('.fileuploadAvpm').each(function() {
        $(this).removeClass('fileuploadAvpm');
        uploadfileform(this);
    })
    $(".classfortooltip").each(function() {
        if (!$(this).hasClass('ready')) {
            $(this).tooltip({
                show: { collision: "flipfit", effect: 'toggle', delay: 1 },
                hide: { delay: 1 },
                tooltipClass: "mytooltip",
                content: function() {
                    return $(this).prop('title'); /* To force to get title as is */
                }
            });
            $(this).addClass('ready');
        }
    })

    /*    */
    $('.alca-date').each(function() {
        if (!$(this).hasClass('ready')) {
            id = $(this).attr('id');
            if ($(this).val() == '') {
                $(this).css('text-align', 'right');
                var date = new Date();
                f = date.getDate() + '/' + (date.getMonth() + 1) + '/' + date.getFullYear();
                realv = $(this).val();
                if (realv != f) {
                    $(this).val(f);
                    $(this).trigger('change');
                }
            }
            $(this).addClass('ready');
            clase = 'dateready' + (new Date()).getTime();
            $(this).addClass(clase);
            $(this).attr('id', clase);
            $(this).attr('data-id', id);
            $(this).attr('data-id2', clase);
            $('.' + clase).datepicker({
                dateFormat: 'dd/mm/yy',
                autoclose: true,
                todayHighlight: true,
                buttonImageOnly: true,
                onClose: function(selectedDate) {
                    id = $(this).attr('data-id');
                    $(this).attr('id', id);
                    //$( clase ).datepicker( "option", "minDate", selectedDate );  
                },
                onSelect: function(selectedDate) {
                    // $( clase ).datepicker( "option", "minDate", selectedDate );  
                    id = $(this).attr('data-id2');
                    $('#' + id).trigger('change');
                    $(this).attr('id', id);
                },
            });
            $(this).attr('id', id);
        }
    });

    $('.sortable').each(function() {
        $(this).sortable();
        $(this).on('mousemove', function(event) {
            obj = $(this).find('li');
            i = 0;
            variado = 0;
            e = {};
            e['accion'] = 'sort';
            $(obj).each(function() {
                pos = $(this).attr('data-position');
                id = $(this).attr('data-id');
                e[id] = i;
                $(this).attr('data-position', i);
                if (parseInt(pos) != i) {
                    variado = 1;
                }
                i += 1;
            });
            if (variado == 1) {
                setTimeout(function(o, e) {
                    executeAjaxCall(o, e);
                }, 50, this, e)
            }
        });
        //$( this ).disableSelection();
        $(this).removeClass('sortable');
    });
}


$(document).on('click', '.alca-close', function(e) {
    if (e.target != this)
        return false;
    $(this).fadeOut(150);
});

console.log('fancy');


$.fancybox = function() {}

$.fancybox.show = function() {
    if ($('#fancybox').length == 0) {
        f = $('<div  id="fancybox"></div>');
        $('body').append(f);
    }
    $('#fancybox').show();
}

$.fancybox.hide = function() {
    $('#fancybox').hide();
}


$(document).on('contextmenu', '[data-contextmenu]', function(event) {
    oj = $(this).attr('data-contextmenu');
    $elemento = $(this).attr('id').replace('grid', '').split('-');
    yy = $elemento[1];
    document.getElementById("gridX").textContent = yy;
    gridYpositionCell = yy;

    event.preventDefault();
    event.stopPropagation();
    y = event.pageY; //$(this).offset().top - $(this).parent().offset().top + 50;
    x = event.pageX; //$(this).offset().left;
    o = $(oj);
    $(o).show();
    $(o).css('position', 'absolute');
    $(o).css('top', y);
    $(o).css('left', x);
});


$(document).on('mouseleave', '.contextmenu', function() {
    $(this).hide();
})

$(document).on('mouseleave', '.removeunhover', function() {
    $(this).remove();
})



/*

$(document).on('mouseenter', '.hoverphoto', function() {
    a = $(this);
    $(a).find('.hoverphoto').removeClass('hoverphoto');
    o = $('<div class="removeunhover" style="position:absolute">' + $(a)[0].outerHTML + '</div>')
    y = event.pageY -100 ;//$(this).offset().top - $(this).parent().offset().top + 50;
    x = event.pageX - 100 ;//$(this).offset().left;
    $(o).show();
    $(o).css('position', 'fixed');
    $(o).css('z-index:99999999999');
    $(o).css('top', y);
    $(o).css('left', x);
    $('body').append(o);
})

*/



$(document).on('click', '.alca-openpage', function(event) {
    event.stopPropagation();
    event.preventDefault();
    href = $(this).attr('href');
    $.ajax({
        url: href + "&ajaxaction=true",
        type: "POST",
        data: datas,
        context: document.body
    }).done(function(response, data, data2) {
        $('#mensajeHoverTextContent').html(response);
    });
});

$(document).on('input', 'form input, form textarea', function(event) {
    $(this).parents('form').first().find('.alca-disable-when-write').addClass('buttondisabled');
    $(this).parents('form').first().find('.alca-enable-when-write').addClass('buttonenabled');
})

$(document).on('change', 'select', function(event) {
    $(this).parents('form').first().find('.alca-disable-when-write').addClass('buttondisabled');
    $(this).parents('form').first().find('.alca-enable-when-write').addClass('buttonenabled');
})

$(document).on('input', 'form .alca-disable-when-write, form .alca-enable-when-write', function(event) {
    $(this).parents('form').first().find('.alca-disable-when-write').removeClass('buttondisabled');
    $(this).parents('form').first().find('.alca-enable-when-write').removeClass('buttonenabled');
})

$(document).on('mousewheel', '.alca-scroll-adjust', function(e) {
    n = $(this).children('div').length;
    hg = $(this).height();
    c = 0;
    obp = $(this).parent();
    ob = $(this);
    s = 0;
    $(this).children('div').each(function() {
        h = $(this).offset().top - $(obp).offset().top;

        if (e.deltaY < 0) {
            if (h > 0 && s == 0) {
                s = $(this).height() * c;
                setTimeout(function(ob, s) {
                    $(ob).scrollTop(s);
                }, 200, obp, s)
            }
        } else {
            if (h < 0) {
                s = $(this).height() * c;
                setTimeout(function(ob, s) {
                    $(ob).scrollTop(s);
                }, 200, obp, s)
            }
        }
        c += 1;
    });
})