
export function initSankeyGraph(datos) {

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

    var ctx = document.getElementById("sankeyDifusion");
    $('#sankeyDifusion').highcharts({

      title: {
        text: ''
      },
      exporting: {
        enabled: false
      },

      colors: ['#ED762A', '#EDD82C', '#DC4300', '#44ED2C', '#2C9DED', '#CAED2C'],

      series: [{
        keys: ['from', 'to', 'weight'],
        data: [

          ['Temas de Interes', 'Consultas de temas de interes', datos[0]],
          ['Consultas de temas de interes', 'Proveedores', datos[7]],
          ['Consultas de temas de interes', 'Tarifas', datos[22]],
          ['Consultas de temas de interes', 'Hoja de vida', datos[8]],
          ['Consultas de temas de interes', 'Proyectos', datos[9]],
          ['Consultas de temas de interes', 'Convenios', datos[10]],
          ['Consultas de temas de interes', 'Leyes', datos[11]],
          ['Consultas de temas de interes', 'Usuarios', datos[12]],
          ['Consultas de temas de interes', 'Alumbrado Publico', datos[14]],
          ['Consultas de temas de interes', 'Normatividad', datos[13]],
          ['Consultas de temas de interes', 'Tramites', datos[6]],

          ['Factura', 'Consultas Factura', datos[1]],
          ['Consultas Factura', 'Informacion factura', datos[15]],

          ['Pago en linea', 'Consultas de Pago en Linea', datos[2]],
          ['Consultas de Pago en Linea', 'A tener en cuenta', datos[16]],
          ['Consultas de Pago en Linea', 'Como pago en linea', datos[17]],

          ['Puntos de atencion', 'Consultas a Puntos de atencion', datos[3]],
          ['Consultas a Puntos de atencion', 'Sede Garzon', datos[18]],
          ['Consultas a Puntos de atencion', 'Sede La Plata', datos[19]],
          ['Consultas a Puntos de atencion', 'Sede Pitalito', datos[20]],
          ['Consultas a Puntos de atencion', 'Sede Saire', datos[21]],

          ['Asesor', 'Consultas asesor', datos[23]],
          ['Consultas asesor', 'Habilitado para asesor', datos[4]],

          ['Otros', 'Consulta a otros', datos[5]],
          ['Consulta a otros', 'Otras consultas', datos[5]],

        ],
        type: 'sankey',
        name: ''
      }]

    });

    $('#save_btn').click(function () {
      save_chart($('#sankeyDifusion').highcharts(), 'chart');
    });

  });

}