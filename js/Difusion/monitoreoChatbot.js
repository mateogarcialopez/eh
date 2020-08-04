import {
    initDifusionHoraPromedioGraph
} from './difusionHoraProm.js';
import {
    initDifusionDiaPromedioGraph
} from './difusionDiaProm.js';
import {
    initConsultaSegmentosGraph
} from './difusionSegmentos.js';
import {
    initSankeyGraph
} from './sankeyDifusion.js';
import {
    initUsoMesTendenciasGraph
} from './usoMesTendencias.js';
import {
    initHeatMap
} from './heatMapUsuarios.js';
import {
    initCalificacionGraph
} from './calificacion.js';
import {
    initDifusionFaqGraph
}from './polar_area_chart.js'

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

function tabsPass() {
    $('.labelTab1').on('click', function (e) {
        $("#labelTab1").css({
            "display": "block"
        });
        $("#labelTab3").css({
            "display": "none"
        });
    });
    $('.labelTab3').on('click', function (e) {
        $("#labelTab1").css({
            "display": "none"
        });
        $("#labelTab3").css({
            "display": "block"
        });
    });
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
                    case 'usuariosSegmentos':
                        var download = document.getElementById('segmentos_usuarios_download');
                        download.click();
                        break;
                    case 'segmentoDifusion':
                        var download = document.getElementById('segmento_difusion_download');
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

    var start3 = moment().subtract(29, 'days');
    var end3 = moment();

    var start = moment().subtract(29, 'days');
    var end = moment();

    cb3(start3, end3);
    cb(start, end);

    //Funciones para la carga inicial fechainicio, fechafin, fechainicio2, fechafin2, fechainicio3, fechafin3, ano, flag, reglas
    function cb(start, end) {
        $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
        cargarDatos(start.format('YYYY-MM-DD HH:mm'), end.format('YYYY-MM-DD HH:mm'), '', '', '', '', '', true, '');
    }

    function cb3(start3, end3) {
        $('#reportrange4 span').html(start3.format('MMMM D, YYYY') + ' - ' + end3.format('MMMM D, YYYY'));
        cargarDatos('', '', '', '', start3.format('YYYY-MM-DD HH:mm'), end3.format('YYYY-MM-DD HH:mm'), '', false, '');
    }

    //funciones para la carga personalizada
    function loadDate(start, end) {
        $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
        cargarDatos(start.format('YYYY-MM-DD HH:mm'), end.format('YYYY-MM-DD HH:mm'), '', '', '', '', '', true, '');
    }

    function loadDate3(start3, end3) {
        $('#reportrange4 span').html(start3.format('MMMM D, YYYY') + ' - ' + end3.format('MMMM D, YYYY'));
        cargarDatos('', '', '', '', start3.format('YYYY-MM-DD HH:mm'), end3.format('YYYY-MM-DD HH:mm'), '', true, '');
    }

    $('#reportrange4').daterangepicker({

        startDate: start3,
        endDate: end3,
        ranges: jsonHorarios('ranges'),
        "locale": jsonHorarios('locale'),
    }, loadDate3);

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
                    tabsPass();
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

function cargarDatos(fechainicio, fechafin, fechainicio2, fechafin2, fechainicio3, fechafin3, ano, flag, reglas) {
    $('.criterioBusqueda').empty();
    //$(".cargaLoader").show();
    preloaderOn();
    var data = {
        fechaInicio: fechainicio,
        fechaFin: fechafin,
        fechaInicio2: fechainicio2,
        fechaFin2: fechafin2,
        fechaInicio3: fechainicio3,
        fechaFin3: fechafin3,
        ano: ano,
        reglas: reglas
    }
    $.ajax({
        type: "post",
        url: "server/chatbot/graphChatbot.php",
        data: data,
        dataType: "json",
        success: function (response) {
            //llenar cada tabla e iniciar cada graph
            if (fechainicio3 != '' && fechafin3 != '') {
                llenarTablasTendencias(response);
                if (flag) {
                    preloaderOff();
                    //$(".cargaLoader").hide();
                }
            } else if (fechainicio != '' && fechafin != '') {
                llenarTablas(response);
                if (flag) {
                    preloaderOff();
                    //$(".cargaLoader").hide();
                }
            }
        }
    });
}

function odometers(id, number) {
    var el = document.querySelector('#' + id);

    var od = new Odometer({
        el: el,
        value: 0,

        // Any option (other than auto and selector) can be passed in here
        format: '',
        theme: 'default'
    });

    od.update(number)
    // or
    //el.innerHTML = 555
}


function llenarTablas(response) {


    // KPI CANTIDAD DE REGISTROS DE CORREO ELECTRONICO
    odometers('cantidad_registros_scada', response.res_kpi.scada.n);

    // KPI CANTIDAD DE EVENTOS SCADA APERTURA CIERRE
    odometers('cantidad_eventos_scada', response.res_kpi.eventosScada.n);

    let porcentajesMenu = getPorcentaje([response.res_kpi.temasInteres, response.res_kpi.menuFactura, response.res_kpi.menuPagoEnLinea, response.res_kpi.menuPuntosDeAtencion, response.res_kpi.consultar_asesor, response.res_kpi.otros]);

    //temas de interes(menus principales)
    $('#cantidad_temasInteres').text(response.res_kpi.temasInteres);
    $('#cantidad_Factura').text(response.res_kpi.menuFactura);
    $('#cantidad_linea').text(response.res_kpi.menuPagoEnLinea);
    $('#cantidad_atencion').text(response.res_kpi.menuPuntosDeAtencion);
    $('#cantidad_Asesor').text(response.res_kpi.consultar_asesor);
    $('#cantidad_otrosMenu').text(response.res_kpi.otros);

    $('#porcentaje_temasInteres').text(porcentajesMenu[0]);
    $('#porcentaje_Factura').text(porcentajesMenu[1]);
    $('#porcentaje_linea').text(porcentajesMenu[2]);
    $('#porcentaje_atencion').text(porcentajesMenu[3]);
    $('#porcentaje_Asesor').text(porcentajesMenu[4]);
    $('#porcentaje_otrosMenu').text(porcentajesMenu[5]);


    initConsultaSegmentosGraph(response.res_kpi.interaccionMuenus);

    let porcentajesCalificacion = getPorcentaje([response.res_kpi.Bueno, response.res_kpi.Regular, response.res_kpi.Malo]);

    //calificaciones
    $('#cantidad_bueno').text(response.res_kpi.Bueno);
    $('#cantidad_regular').text(response.res_kpi.Regular);
    $('#cantidad_malo').text(response.res_kpi.Malo);

    $('#porcentaje_bueno').text(porcentajesCalificacion[0]);
    $('#porcentaje_regular').text(porcentajesCalificacion[1]);
    $('#porcentaje_malo').text(porcentajesCalificacion[2]);

    initCalificacionGraph(response.res_kpi.calificaciones);

    initSankeyGraph(response.res_resultados.sankeyDifusion);

    var horasLunes = jsonDias();
    var horasMartes = jsonDias();
    var horasMiercoles = jsonDias();
    var horasJueves = jsonDias();
    var horasViernes = jsonDias();
    var horasSabado = jsonDias();
    var horasDomingo = jsonDias();

    response.res_busqueda.promedioHoraDia.forEach(element => {

        var hora = element.FECHA.split(' ');
        var fecha = new Date(hora[0]);

        hora = hora[1].split(':');

        if (fecha.getDay() == 0) {
            horasLunes[hora[0]] += 1;
        } else if (fecha.getDay() == 1) {
            horasMartes[hora[0]] += 1;
        } else if (fecha.getDay() == 2) {
            horasMiercoles[hora[0]] += 1;
        } else if (fecha.getDay() == 3) {
            horasJueves[hora[0]] += 1;
        } else if (fecha.getDay() == 4) {
            horasViernes[hora[0]] += 1;
        } else if (fecha.getDay() == 5) {
            horasSabado[hora[0]] += 1;
        } else if (fecha.getDay() == 6) {
            horasDomingo[hora[0]] += 1;
        }
        

    });

    var diasCantidad = {
        0: horasDomingo,
        1: horasLunes,
        2: horasMartes,
        3: horasMiercoles,
        4: horasJueves,
        5: horasViernes,
        6: horasSabado
    };

    initHeatMap(diasCantidad);

}

function jsonDias() {
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
        '23': 0,
    };
}

function llenarTablasTendencias(response) {

    //haigchar
    //apexchar
    //canvasjs
    //d3

    initUsoMesTendenciasGraph(response.res_busqueda.consultasXmes.meses);

    var sumaHoras = {
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
        '23': 0,
        '24': 0
    };
    var horasPromedios = {
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
        '23': 0,
        '24': 0
    };
    var diasCantidad = {
        0: 0,
        1: 0,
        2: 0,
        3: 0,
        4: 0,
        5: 0,
        6: 0
    };
    var diasSuma = {
        0: 0,
        1: 0,
        2: 0,
        3: 0,
        4: 0,
        5: 0,
        6: 0
    };


    response.res_busqueda.promedioHora.forEach(element => {

        var hora = element.FECHA.split(' ');
        var fecha = new Date(hora[0]);

        hora = hora[1].split(':');

        //horasPromedios[hora[0]] += element.CANTIDAD_DIFUNDIDA;
        sumaHoras[hora[0]] += 1;

        diasCantidad[fecha.getDay()] += 1;
        //diasSuma[fecha.getDay()] += element.CANTIDAD_DIFUNDIDA;

    });

    var dataHoras = new Array();
    var dataDias = new Array();
    dataHoras.push(horasPromedios);
    dataHoras.push(sumaHoras);

    dataDias.push(diasSuma);
    dataDias.push(diasCantidad);

    initDifusionHoraPromedioGraph(dataHoras);

    initDifusionDiaPromedioGraph(dataDias);

    initDifusionFaqGraph(response.res_usuarios.ConsultasFaq);



}

function llenarTipificacion(response) {

    var diasCantidadLucy = {
        0: 0,
        1: 0,
        2: 0,
        3: 0,
        4: 0,
        5: 0,
        6: 0
    };
    var diasCantidadDifusion = diasCantidadLucy;
    var diasCantidadLlamadas = diasCantidadLucy;

    response.res_tipificacion.consultas_lucy.forEach(element => {

        var hora = element.FECHA_RESULTADO.split(' ');
        var fecha = new Date(hora[0]);

        diasCantidadLucy[fecha.getDay()] += 1;

    });
    initDifusionComparacionTipificacion(diasCantidadLucy, 'tipificacionConsultasLucy', '#8e5ea2');

    response.res_tipificacion.mensajes_difusion.forEach(element => {

        var hora = element.FECHA_ENVIO_APERTURA.split(' ');
        var fecha = new Date(hora[0]);

        diasCantidadDifusion[fecha.getDay()] += 1;

    });
    initDifusionComparacionTipificacion(diasCantidadDifusion, 'tipificacionDifusion', '#A29E5E');

    response.res_tipificacion.llamadas.forEach(element => {

        var fecha = new Date(element.Fecha);

        diasCantidadLlamadas[fecha.getDay()] += 1;

    });
    initDifusionComparacionTipificacion(diasCantidadLlamadas, 'tipificacionLlamadas', '#77E17F');

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