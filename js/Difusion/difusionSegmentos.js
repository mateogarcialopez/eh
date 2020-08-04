export function initConsultaSegmentosGraph(data) {
  // grafico resultados

  var pieColors = (function () {
    var colors = [],
      base = Highcharts.getOptions().colors[0],
      i;

    for (i = 0; i < 10; i += 1) {
      // Start out with a darkened base color (negative brighten), and end
      // up with a much brighter color
      colors.push(Highcharts.Color(base).brighten((i - 3) / 7).get());
    }
    return colors;
  }());

  function save_chart(chart, filename) {
    var render_width = 1000;
    var render_height = render_width * chart.chartHeight / chart.chartWidth

    var svg = chart.getSVG({
      exporting: {
        sourceWidth: chart.chartWidth,
        sourceHeight: chart.chartHeight
      }
    });

    var canvas = document.createElement('canvas');
    canvas.height = render_height;
    canvas.width = render_width;

    var image = new Image;
    image.onload = function () {
      canvas.getContext('2d').drawImage(this, 0, 0, render_width, render_height);
      var data = canvas.toDataURL("image/png")
      download(data, filename + '.png');
    };
    image.src = 'data:image/svg+xml;base64,' + window.btoa(svg);
  }

  function download(data, filename) {
    var a = document.createElement('a');
    a.download = filename;
    a.href = data
    document.body.appendChild(a);
    a.click();
    a.remove();
  }
  $(function () {

    $('#ingresoMenus').highcharts({
      chart: {
        plotBackgroundColor: null,
        plotBorderWidth: null,
        plotShadow: false,
        type: 'pie'
      },
      title: {
        text: ''
      },
      exporting: {
        enabled: false
      },
      tooltip: {
        pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
      },
      plotOptions: {
        pie: {
          allowPointSelect: true,
          cursor: 'pointer',
          colors: pieColors,
          fontSize: '4px',
          dataLabels: {
            enabled: true,
            format: '<b>{point.name}</b><br>{point.percentage:.1f} %',
            distance: -50,
            style: {
              fontSize: 8
            },
            filter: {
              property: 'percentage',
              operator: '>',
              value: 4
            }
          }
        }
      },
      series: [{
        name: 'Share',
        data: [{
          y: data.menuConsultas,
          name: "Temas de Idnteres"
        },
        {
          y: data.menuFactura,
          name: "Factura"
        },
        {
          y: data.menuPagoEnLinea,
          name: "Pago Linea"
        },
        {
          y: data.menuPuntosDeAtencion,
          name: "Puntos de atencion"
        },
        {
          y: data.menu_asesor,
          name: "Asesor"
        },
        {
          y: data.otros,
          name: "Otros"
        }
        ]
      }]
    });

    $('#save_btna').click(function () {
      save_chart($('#ingresoMenus').highcharts(), 'chart');
    });

  });
}