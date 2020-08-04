import {
    initDifusionComparacionTipificacion
} from './difusionComparacionTipificacion.js';


$(document).ready(function () {

    validarSesion();

    $('.tooltipped').tooltip();
    $('.sidenav').sidenav();
    $('#logoutButton').click(function () {
        logout()
    });
    $('#logoutLateral').click(function () {
        logout()
    });

});

function preloaderOn() {

    if (document.querySelector('#preloader')) {
        document.querySelector('#preloader').classList.remove("hide_preloader");
        document.querySelector('#preloader').classList.add("show_preloader");
    } else {
        var d1 = document.querySelector('body');
        d1.insertAdjacentHTML('beforeend', '<div  id="preloader" class="position-fixed" style="top:0px; z-index:99999999999999999999999;"><div class="position-fixed backdrop_preload w-100 h-100vh flex-center"><div  class="avatar sombra  animated infinite heartBeat"><img class="logo_circulo" src="./img/electrohuila_logo.png"></div><div class="text-white texto_cargando">Cargando...</div></div></div>')

    }
}

function preloaderOff() {

    if (document.querySelector('#preloader')) {
        document.querySelector('#preloader').classList.remove("show_preloader");
        document.querySelector('#preloader').classList.add("hide_preloader");

    }
}

function downloadsAttachments() {

    var tabs = $('.tabs');
    var selector = $('.tabs').find('a').length;
    //var selector = $(".tabs").find(".selector");
    var activeItem = tabs.find('.active');
    var activeWidth = activeItem.innerWidth();
    $(".selector").css({
        "left": activeItem.position.left + "px",
        "width": activeWidth + "px"
    });

    $(".tabs").on("click", "a", function (e) {
        e.preventDefault();
        $('.tabs a').removeClass("active");
        $(this).addClass('active');
        var activeWidth = $(this).innerWidth();
        var itemPos = $(this).position();
        $(".selector").css({
            "left": itemPos.left + "px",
            "width": activeWidth + "px"
        });
        if (e.target.text == 'Chatbot') {
            $("#Chatbot").css({
                "display": "block"
            });
            $("#Difusion").css({
                "display": "none"
            });
        } else {
            $("#Chatbot").css({
                "display": "none"
            });
            $("#Difusion").css({
                "display": "block"
            });
        }
    });
    downloadsAttachmentsButtons();

    var btnAbrirPopup = document.getElementById('btn-abrir-popupDownloads'),
        overlay = document.getElementById('overlayDownloads'),
        popup = document.getElementById('popupDownloads'),
        btnCerrarPopup = document.getElementById('btn-cerrar-popupDownloads');

    if (btnAbrirPopup) {

        btnAbrirPopup.addEventListener('click', function () {
            overlay.classList.add('active');
            popup.classList.add('active');
            
        });
    }
    if (btnCerrarPopup) {

        btnCerrarPopup.addEventListener('click', function (e) {
            e.preventDefault();
            overlay.classList.remove('active');
            popup.classList.remove('active');
        });
    }

}

function downloadsAttachmentsButtons() {

    var close = document.getElementsByClassName("closebtn");
    var i;
    if (close.length > 0) {

        for (i = 0; i < close.length; i++) {
            close[i].onclick = function () {
                var div = this.parentElement;
                $(".alerta").addClass("animated zoomOut");
                setTimeout(function () {
                    //div.style.display = "none";
                    $(".alerta").removeClass("animated zoomOut");
                    div.style.display = "none";
                    $(".cargaLoaderPopup").hide();
                }, 300);
            }
        }

    }

    $("#niu_senderid_link").click(function (e) {

        $(".cargaLoaderPopup").show();
        $("#niu_senderid_link").addClass("buttonDownload2");
        $("#niu_senderid_link").removeClass("buttonDownload");
        var month = document.getElementById("month_start");
        var year = document.getElementById("year_start");
        generateFileExcel(month.value, year.value, 'NiuSenderId');

    });
    $("#cal_negativas").click(function (e) {

        $(".cargaLoaderPopup").show();
        $("#cal_negativas").addClass("buttonDownload2");
        $("#cal_negativas").removeClass("buttonDownload");
        var month = document.getElementById("month_start");
        var year = document.getElementById("year_start");
        generateFileExcel(month.value, year.value, 'calnegativas');

    });
    $("#consultas_usuarios").click(function (e) {

        $(".cargaLoaderPopup").show();
        $("#consultas_usuarios").addClass("buttonDownload2");
        $("#consultas_usuarios").removeClass("buttonDownload");
        var month = document.getElementById("month_start");
        var year = document.getElementById("year_start");
        generateFileExcel(month.value, year.value, 'consultas_usuarios');

    });
    $("#acuse_recibo_difusion").click(function (e) {

        $(".cargaLoaderPopup").show();
        $("#acuse_recibo_difusion").addClass("buttonDownload2");
        $("#acuse_recibo_difusion").removeClass("buttonDownload");
        var month = document.getElementById("month_start");
        var year = document.getElementById("year_start");
        generateFileExcel(month.value, year.value, 'AcuseReciboDifusion');

    });
    $("#switches_inexistentes").click(function (e) {

        $(".cargaLoaderPopup").show();
        $("#switches_inexistentes").addClass("buttonDownload2");
        $("#switches_inexistentes").removeClass("buttonDownload");
        var month = document.getElementById("month_start");
        var year = document.getElementById("year_start");
        generateFileExcel(month.value, year.value, 'switches_inexistentes');

    });

}

function generateFileExcel(month, year, type) {

    var data = {
        month: month,
        year: year,
        type: type
    }
    $.ajax({
        type: "post",
        url: "server/downloads/downloadsAttachments.php",
        data: data,
        dataType: "json",
        success: function (response) {
            if (response == 'ok') {
                switch (type) {
                    case 'NiuSenderId':
                        var download = document.getElementById('niu_senderid_download');
                        download.click();
                        break;
                    case 'calnegativas':
                        var download = document.getElementById('cal_negativas_download');
                        download.click();
                        break;
                    case 'AcuseReciboDifusion':
                        var download = document.getElementById('acuse_recibo_difusion_download');
                        download.click();
                        break;
                    case 'switches_inexistentes':
                        var download = document.getElementById('switches_inexistentes_download');
                        download.click();
                        break;
                    case 'consultas_usuarios':
                        var download = document.getElementById('consultas_usuarios_download');
                        download.click();
                        break;

                    default:
                        break;
                }
                $(".cargaLoaderPopup").hide();

            } else if (response == 'No hay datos') {
                $(".alert").css({
                    "display": "block"
                });
            }
        }
    });
}

/* PLUGIN FILTRO */
function initFilter() {

    var start2 = moment().subtract(29, 'days');
    var end2 = moment();

    var start = moment().subtract(29, 'days');
    var end = moment();

    var f = new Date();
    var fecha = "0" + (parseInt(f.getMonth()) + 1) + "/" + f.getFullYear();

    $('#filtroMes').monthpicker();
    $('#filtroMes').val(fecha);

    cargarDatos('', '', '', '', '', '', parseInt(moment().format('YYYY')), true);
    cargarDatos('', '', '', '', f.getMonth() + 1, f.getFullYear(), '', true);
    cb(start, end);
    cb2(start2, end2);

    //Funciones para la carga inicial fechainicio, fechafin, fechainicio2, fechafin2, ano, flag, reglas
    $('.yearpicker-year').click(function (e) {
        cargarDatos('', '', '', '', '', '', parseInt(e.target.innerHTML), true);
    });

    $('tr.mtz-monthpicker td').click(function (e) {

        cargarDatos('', '', '', '', parseInt(e.target.dataset.month, 10) , parseInt($('.mtz-monthpicker-year option:selected').text()), '', true);
    });

    function cb(start, end) {
        $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
        cargarDatos(start.format('YYYY-MM-DD HH:mm'), end.format('YYYY-MM-DD HH:mm'), '', '', '', '', '', false);
    }

    function cb2(start2, end2) {
        $('#reportrange2 span').html(start2.format('MMMM D, YYYY') + ' - ' + end2.format('MMMM D, YYYY'));
        cargarDatos('', '', start2.format('YYYY-MM-DD HH:mm'), end2.format('YYYY-MM-DD HH:mm'), '', '', '', false);
    }

    //funciones para la carga personalizada
    function loadDate(start, end) {
        $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
        cargarDatos(start.format('YYYY-MM-DD HH:mm'), end.format('YYYY-MM-DD HH:mm'), '', '', '', '', '', true);
    }

    function loadDate2(start2, end2) {
        $('#reportrange2 span').html(start2.format('MMMM D, YYYY') + ' - ' + end2.format('MMMM D, YYYY'));
        cargarDatos('', '', start2.format('YYYY-MM-DD HH:mm'), end2.format('YYYY-MM-DD HH:mm'), '', '', '', true);
    }

    $('#reportrange2').daterangepicker({

        startDate: start2,
        endDate: end2,
        ranges: jsonHorarios('ranges'),
        "locale": jsonHorarios('locale'),
    }, loadDate2);

    $('#reportrange').daterangepicker({

        startDate: start,
        endDate: end,
        ranges: jsonHorarios('ranges'),
        "locale": jsonHorarios('locale'),
    }, loadDate);

}

function jsonHorarios(type) {
    if (type == 'locale') {

        return {
            "format": "DD/MM/YYYY",
            "separator": " - ",
            "applyLabel": "Aplicar",
            "cancelLabel": "Cancelar",
            "fromLabel": "De",
            "toLabel": "a",
            "customRangeLabel": "Personalizado",
            "weekLabel": "S",
            "daysOfWeek": [
                "Do",
                "Lu",
                "Ma",
                "Mi",
                "Ju",
                "Vi",
                "Sa"
            ],
            "monthNames": [
                "Enero",
                "Febrero",
                "Marzo",
                "Abril",
                "Mayo",
                "Junio",
                "Julio",
                "Agosto",
                "Septiembre",
                "Octubre",
                "Noviembre",
                "Diciembre"
            ],
            "firstDay": 1
        };
    } else if (type == 'ranges') {
        return {
            'Hoy': [moment(), moment()],
            'Ayer': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Últimos 7 días': [moment().subtract(6, 'days'), moment()],
            'Últimos 30 dias': [moment().subtract(29, 'days'), moment()],
            'Este mes': [moment().startOf('month'), moment().endOf('month')],
            'Último mes': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        };
    }

}


function validarSesion() {

    var data = {
        idusuario: localStorage.getItem('idusuario')
    }
    $.ajax({
        type: "post",
        url: "server/validateSession.php",
        data: data,
        dataType: "json",
        success: function (response) {
            if (!response) {
                Swal.fire({
                    type: 'error',
                    title: 'Error',
                    text: 'Debes ingresar con tus credenciales',
                }).then(() => {
                    window.location.href = "login.html"
                })
            } else {
                if (response != "monitoreo") {
                    Swal.fire({
                        type: 'error',
                        title: 'Error',
                        text: 'No tienes permiso para ingresar a esta sección',
                    }).then(() => {
                        window.location.href = "login.html"
                    })
                } else {
                    initFilter();
                    downloadsAttachments();
                }
            }
        }
    });
}

function logout() {
    $.ajax({
        type: "post",
        url: "server/logout.php",
        dataType: "json",
        success: function (response) {
            Swal.fire({
                type: 'success',
                title: 'Sesión Cerrada',
                text: 'Sesión cerrada con éxito',
            }).then(() => {
                window.location.href = "login.html"
            })
        }
    });
}

function cargarDatos(fechainicio, fechafin, fechainicio2, fechafin2, mes, ano2, ano, flag) {
   
    preloaderOn();
    var data = {
        fechaInicio: fechainicio,
        fechaFin: fechafin,
        fechaInicio2: fechainicio2,
        fechaFin2: fechafin2,
        ano: ano,
        mes: mes,
        ano2: ano2
    }
    $.ajax({
        type: "post",
        url: "server/tipificacion/graphTipificacion.php",
        data: data,
        dataType: "json",
        success: function (response) {
            //llenar cada tabla e iniciar cada graph
            if (ano != '') {
                llenarporMes(response);
                if (flag) {
                    preloaderOff();
                }
            }
            if (mes != '' && ano2 != '') {
                llenarporDia(response);
                if (flag) {
                    preloaderOff();
                }
            }
            if (fechainicio2 != '' && fechafin2 != '') {
                llenarporHora(response);
                if (flag) {
                    preloaderOff();
                }
            }
            if (fechainicio != '' && fechafin != '') {
                llenarporSemana(response);
                if (flag) {
                    preloaderOff();
                }
            }

        }
    });
}

function llenarporMes(response) {

    var mesCantidadLucy = response.res_tipificacion.consultas_lucy_ano;
    var mesCantidadDifusion = response.res_tipificacion.mensajes_difusion_ano;
    var mesCantidadLlamadas = response.res_tipificacion.llamadas_ano;

    var meses = new Array();
    var mesesLucy = new Array();
    var mesesDifusion = new Array();
    var mesesLlamadas = new Array();
    var barlucy = {};
    var barDifusion = {};
    var barLlamadas = {};

    mesCantidadLucy.forEach(element => {
        if (barlucy[element._id] === undefined) {
            barlucy[element._id] = element.cantidad;
        } else {
            barlucy[element._id] += element.cantidad;
        }

        var mes = jsonMeses(element._id);
        if (!meses.includes(mes)) {
            meses.push(mes);
            mesesLucy.push(element._id);
        }
    });

    mesCantidadDifusion.forEach(element => {
        if (barDifusion[element._id] === undefined) {
            barDifusion[element._id] = element.cantidad;
        } else {
            barDifusion[element._id] += element.cantidad;
        }

        var mes = jsonMeses(element._id);
        if (!meses.includes(mes)) {
            meses.push(mes);
            mesesDifusion.push(element._id);
        }
    });


    mesCantidadLlamadas.forEach(element => {
        if (barLlamadas[element._id] === undefined) {
            barLlamadas[element._id] = element.cantidad;
        } else {
            barLlamadas[element._id] += element.cantidad;
        }
        var mes = jsonMeses(element._id);
        if (!meses.includes(mes)) {
            meses.push(mes);
            mesesLlamadas.push(element._id);
        }
    });

    if (mesesLucy.length > mesesDifusion.length && mesesLucy.length > mesesLlamadas.length) {

        for (let i = 0; i < mesesLucy.length; i++) {

            if (barDifusion[mesesLucy[i]] === undefined) {
                barDifusion[mesesLucy[i]] = 0;
            }
            if (barLlamadas[mesesLucy[i]] === undefined) {
                barLlamadas[mesesLucy[i]] = 0;
            }

        }

    } else if (mesesDifusion.length > mesesLucy.length && mesesDifusion.length > mesesLlamadas.length) {

        for (let i = 0; i < mesesDifusion.length; i++) {

            if (barlucy[mesesDifusion[i]] === undefined) {
                barlucy[mesesDifusion[i]] = 0;
            }
            if (barLlamadas[mesesDifusion[i]] === undefined) {
                barLlamadas[mesesDifusion[i]] = 0;
            }

        }
    } else if (mesesLlamadas.length > mesesLucy.length && mesesLlamadas.length > mesesDifusion.length) {

        for (let i = 0; i < mesesLlamadas.length; i++) {

            if (barlucy[mesesLlamadas[i]] === undefined) {
                barlucy[mesesLlamadas[i]] = 0;
            }
            if (barDifusion[mesesLlamadas[i]] === undefined) {
                barDifusion[mesesLlamadas[i]] = 0;
            }

        }
    }

    barlucy = OrdenarPorClave(barlucy);
    barDifusion = OrdenarPorClave(barDifusion);
    barLlamadas = OrdenarPorClave(barLlamadas);
    var resutadoLucy = new Array();
    var resutadoDifusion = new Array();
    var resutadoLlamadas = new Array();

    for (var i in barlucy) {
        resutadoLucy.push(barlucy[i]);
    }
    for (var i in barDifusion) {
        resutadoDifusion.push(barDifusion[i]);
    }
    for (var i in barLlamadas) {
        resutadoLlamadas.push(barLlamadas[i]);
    }

    document.getElementById('tipificacionConsultasAno').innerHTML = '';
    initDifusionComparacionTipificacion(resutadoLucy, resutadoDifusion, resutadoLlamadas, meses, '.tipificacionConsultasAno');

}

function llenarporHora(response) {

    var horasCantidadLucy = response.res_tipificacion.consultas_lucy_hora;
    var horasCantidadDifusion = response.res_tipificacion.mensajes_difusion_hora;
    var horasCantidadLlamadas = response.res_tipificacion.llamadas_hora;

    var horasLucy = jsonHoras();
    var horasDifusion = jsonHoras();
    var horasTipificacion = jsonHoras();

    horasCantidadLucy.forEach(element => {
        horasLucy[element._id] += element.cantidad;
    });
    horasCantidadDifusion.forEach(element => {
        horasDifusion[element._id] += element.cantidad;
    });
    horasCantidadLlamadas.forEach(element => {
        horasTipificacion[element._id] += element.cantidad;
    });

    var horasLucyCompleto = [horasLucy['00'], horasLucy['01'], horasLucy['02'], horasLucy['03'], horasLucy['04'], horasLucy['05'], horasLucy['06'], horasLucy['07'], horasLucy['08'], horasLucy['09'], horasLucy['10'], horasLucy['11'], horasLucy['12'], horasLucy['13'], horasLucy['14'], horasLucy['15'], horasLucy['16'], horasLucy['17'], horasLucy['18'], horasLucy['19'], horasLucy['20'], horasLucy['21'], horasLucy['22'], horasLucy['23']];
    var horasDifusionCompleto = [horasDifusion['00'], horasDifusion['01'], horasDifusion['02'], horasDifusion['03'], horasDifusion['04'], horasDifusion['05'], horasDifusion['06'], horasDifusion['07'], horasDifusion['08'], horasDifusion['09'], horasDifusion['10'], horasDifusion['11'], horasDifusion['12'], horasDifusion['13'], horasDifusion['14'], horasDifusion['15'], horasDifusion['16'], horasDifusion['17'], horasDifusion['18'], horasDifusion['19'], horasDifusion['20'], horasDifusion['21'], horasDifusion['22'], horasDifusion['23']];
    var horasTipificacionCompleto = [horasTipificacion['00'], horasTipificacion['01'], horasTipificacion['02'], horasTipificacion['03'], horasTipificacion['04'], horasTipificacion['05'], horasTipificacion['06'], horasTipificacion['07'], horasTipificacion['08'], horasTipificacion['09'], horasTipificacion['10'], horasTipificacion['11'], horasTipificacion['12'], horasTipificacion['13'], horasTipificacion['14'], horasTipificacion['15'], horasTipificacion['16'], horasTipificacion['17'], horasTipificacion['18'], horasTipificacion['19'], horasTipificacion['20'], horasTipificacion['21'], horasTipificacion['22'], horasTipificacion['23']];

    var categories = ['00', '01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20', '21', '22', '23'];

    document.getElementById('tipificacionConsultasHora').innerHTML = '';
    initDifusionComparacionTipificacion(horasLucyCompleto, horasDifusionCompleto, horasTipificacionCompleto, categories, '.tipificacionConsultasHora');

}

function llenarporSemana(response) {

    var diasCantidadLucy = response.res_tipificacion.consultas_lucy_dia;
    var diasCantidadDifusion = response.res_tipificacion.mensajes_difusion_dia;
    var diasCantidadLlamadas = response.res_tipificacion.llamadas_dia;

    var diasLucy = jsonDias();
    var diasDifusion = jsonDias();
    var diasTipificacion = jsonDias();

    diasCantidadLucy.forEach(element => {
        diasLucy[element._id] += element.cantidad;
    });
    diasCantidadDifusion.forEach(element => {
        diasDifusion[element._id] += element.cantidad;
    });
    diasCantidadLlamadas.forEach(element => {
        diasTipificacion[element._id] += element.cantidad;
    });

    var diasLucyCompleto = [diasLucy[1], diasLucy[2], diasLucy[3], diasLucy[4], diasLucy[5], diasLucy[6], diasLucy[7]];
    var diasDifusionCompleto = [diasDifusion[1], diasDifusion[2], diasDifusion[3], diasDifusion[4], diasDifusion[5], diasDifusion[6], diasDifusion[7]];
    var diasTipificacionCompleto = [diasTipificacion[1], diasTipificacion[2], diasTipificacion[3], diasTipificacion[4], diasTipificacion[5], diasTipificacion[6], diasTipificacion[7]];

    var categories = ['Domingo', 'Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sábado']

    document.getElementById('tipificacionConsultasSemana').innerHTML = '';
    initDifusionComparacionTipificacion(diasLucyCompleto, diasDifusionCompleto, diasTipificacionCompleto, categories, '.tipificacionConsultasSemana');

}

function llenarporDia(response) {

    var lucyMesAno = new Array();
    var difusionMesAno = new Array();
    var tipificacionMesAno = new Array();
    var categories = new Array();

    response.res_tipificacion.consultas_lucy_mesAno.forEach(element => {
        lucyMesAno[element._id] = element.suma;
        if (categories[element._id] === undefined) {
            categories[element._id] = element._id;
        }
    });
    response.res_tipificacion.mensajes_difusion_mesAno.forEach(element => {
        difusionMesAno[element._id] = element.suma;
        if (categories[element._id] === undefined) {
            categories[element._id] = element._id;
        }
    });
    response.res_tipificacion.llamadas_mesAno.forEach(element => {
        tipificacionMesAno[element._id] = element.suma;
        if (categories[element._id] === undefined) {
            categories[element._id] = element._id;
        }
    });

    var lucyMesAnoDef = lucyMesAno.filter(function (el) {
        return el != null;
    });
    var difusionMesAnoDef = difusionMesAno.filter(function (el) {
        return el != null;
    });
    var tipificacionMesAnoDef = tipificacionMesAno.filter(function (el) {
        return el != null;
    });
    var categoriesDef = categories.filter(function (el) {
        return el != null;
    });
    document.getElementById('tipificacionConsultasDia').innerHTML = '';
    initDifusionComparacionTipificacion(lucyMesAnoDef, difusionMesAnoDef, tipificacionMesAnoDef, categoriesDef, '.tipificacionConsultasDia');

}

function OrdenarPorClave(arr) {
    // Inicializamos los arrays
    var sortedKeys = new Array();
    var sortedObj = {};

    // Separamos la clave en un solo array
    for (var i in arr) {
        sortedKeys.push(i);
    }
    // Ordenamos dicha clave
    sortedKeys.sort();

    // Reconstruimos el array asociativo con la clave ordenada
    for (var i in sortedKeys) {
        sortedObj[sortedKeys[i]] = arr[sortedKeys[i]];
    }
    return sortedObj;
}

function jsonMeses(data) {

    switch (data) {
        case '01':
            return 'Enero';
            break;
        case '02':
            return 'Febrero';
            break;
        case '03':
            return 'Marzo';
            break;
        case '04':
            return 'Abril';
            break;
        case '05':
            return 'Mayo';
            break;
        case '06':
            return 'Junio';
            break;
        case '07':
            return 'Julio';
            break;
        case '08':
            return 'Agosto';
            break;
        case '09':
            return 'Septiembre';
            break;
        case '10':
            return 'Octubre';
            break;
        case '11':
            return 'Noviembre';
            break;
        case '12':
            return 'Diciembre';
            break;

    }
}

function jsonDias() {
    return {
        1: 0,
        2: 0,
        3: 0,
        4: 0,
        5: 0,
        6: 0,
        7: 0
    };
}

function jsonHoras() {
    return {
        '00': 0,
        '01': 0,
        '02': 0,
        '03': 0,
        '04': 0,
        '05': 0,
        '06': 0,
        '07': 0,
        '08': 0,
        '09': 0,
        '10': 0,
        '11': 0,
        '12': 0,
        '13': 0,
        '14': 0,
        '15': 0,
        '16': 0,
        '17': 0,
        '18': 0,
        '19': 0,
        '20': 0,
        '21': 0,
        '22': 0,
        '23': 0
    };
}

function getPorcentaje(arrayEntrada) {
    let suma = 0;

    arrayEntrada.forEach(element => {
        suma += element;
    });

    let arregloRespuesta = [];
    arrayEntrada.forEach(element => {
        //arregloRespuesta.push((((element / suma * 100)/100).toFixed(3)*100) + "%");
        arregloRespuesta.push((((element / suma * 100)).toFixed(2)));
    });
    return arregloRespuesta;
}

function sortCriterios(a, b) {
    a = a.y;
    b = b.y;
    if (a < b) {
        return 1;
    } else if (a > b) {
        return -1;
    }
    return 0;
}

function firstUpper(string) {
    return string.charAt(0).toUpperCase() + string.slice(1).toLowerCase();
}