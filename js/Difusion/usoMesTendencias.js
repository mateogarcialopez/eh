export function initUsoMesTendenciasGraph(data) {

  var k;
  var dataPoint = new Array();
  var label = new Array();
  for (k in data) {
    if (data.hasOwnProperty(k)) {
      var obj = [];
      switch (k) {
        case '0':
          dataPoint.push(data[k]);
          label.push("Enero");
          break;
        case '1':
          dataPoint.push(data[k]);
          label.push("Febrero");
          break;
        case '2':
          dataPoint.push(data[k]);
          label.push("Marzo");
          break;
        case '3':
          dataPoint.push(data[k]);
          label.push("Abril");
          break;
        case '4':
          dataPoint.push(data[k]);
          label.push("Mayo");
          break;
        case '5':
          dataPoint.push(data[k]);
          label.push("Junio");
          break;
        case '6':
          dataPoint.push(data[k]);
          label.push("Julio");
          break;
        case '7':
          dataPoint.push(data[k]);
          label.push("Agosto");
          break;
        case '8':
          dataPoint.push(data[k]);
          label.push("Septiembre");
          break;
        case '9':
          dataPoint.push(data[k]);
          label.push("Octubre");
          break;
        case '10':
          dataPoint.push(data[k]);
          label.push("Noviembre");
          break;
        case '11':
          dataPoint.push(data[k]);
          label.push("Diciembre");
          break;
      }

    }
  }

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
    $('#conTendencias').highcharts({
      chart: {
        type: 'areaspline'
      },
      exporting: {
        enabled: false
      },
      title: {
        text: ''
      },
      legend: {
        layout: 'vertical',
        align: 'left',
        verticalAlign: 'top',
        x: 150,
        y: 100,
        floating: true,
        borderWidth: 1,
        backgroundColor:
          Highcharts.defaultOptions.legend.backgroundColor || '#FFFFFF'
      },
      xAxis: {
        categories: label,
        plotBands: [{ // visualize the weekend
          from: 4.5,
          to: 6.5,
          color: 'rgba(68, 170, 213, .2)'
        }]
      },
      yAxis: {
        title: {
          text: 'Numero de Consultas'
        }
      },
      tooltip: {
        shared: true,
        valueSuffix: ' units'
      },
      credits: {
        enabled: false
      },
      plotOptions: {
        areaspline: {
          fillOpacity: 0.5
        }
      },
      series: [{
        name: 'Consultas',
        data: dataPoint
      }]
    });


    $('#save_btnc').click(function () {
      save_chart($('#conTendencias').highcharts(), 'chart');
    });

  });
}