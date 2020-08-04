export function initCalificacionGraph(data) {
  // grafico resultados2 para muchas respuestas o ninguna respuesta

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

    var ctx = document.getElementById("calificacion_container");
    $('#calificacion_container').highcharts({

      chart: {
        plotBackgroundColor: null,
        plotBorderWidth: 0,
        plotShadow: false
      },
      title: {
        text: 'ChatBot',
        align: 'center',
        verticalAlign: 'middle',
        y: 40
      },
      tooltip: {
        pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
      },
      plotOptions: {
        pie: {
          dataLabels: {
            enabled: true,
            distance: -10,
            style: {
              fontWeight: 'bold',
              color: 'white'
            }
          },
          startAngle: -90,
          endAngle: 90,
          center: ['50%', '75%'],
          size: '110%'
        }
      },
      series: [{
        type: 'pie',
        name: 'Porcentaje',
        innerSize: '50%',
        data: [

          ['Bueno', data.Bueno],
          ['Regular', data.Regular],
          ['Malo', data.Malo]

        ]
      }]
    });

    $('#save_btnco').click(function () {
      save_chart($('#calificacion_container').highcharts(), 'chart');
    });

  });
}