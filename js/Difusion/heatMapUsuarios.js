export function initHeatMap(datos) {
  // grafico heat map

  document.getElementById('consultasHoraDia').innerHTML = '';
  var ctx = document.getElementById("consultasHoraDia");

  function generateData(count, horas) {
    var i = 0;
    var series = [];
    while (i < count) {
      var x;
      if (i < 10) {

        x = "0" + (i).toString();
      } else {
        x = (i).toString();
      }
      var y = horas[x];

      series.push({
        x: x,
        y: y
      });
      i++;
    }
    return series;
  }

  var data = [{
    name: 'Lunes',
    data: generateData(24, datos['1'])
  },
  {
    name: 'Martes',
    data: generateData(24, datos['2'])
  },
  {
    name: 'Miercoles',
    data: generateData(24, datos['3'])
  },
  {
    name: 'Jueves',
    data: generateData(24, datos['4'])
  },
  {
    name: 'Viernes',
    data: generateData(24, datos['5'])
  },
  {
    name: 'Sábado',
    data: generateData(24, datos['6'])
  },
  {
    name: 'Domingo',
    data: generateData(24, datos['0'])
  }
  ]

  data.reverse()

  //var colors = ["#F27036", "#6A6E94", "#18D8D8",'#46AF78', '#A93F55', '#33A1FD', '#EA0000']
  var colors = ["#00D8C3", "#FFCF00", "#07CA02", '#00D5B5', '#0079D3', '#B1C400', '#A80E2E']

  colors.reverse()


  var options = {
    chart: {
      toolbar: {
        show: true,
        tools: {
          download: true,
          download: '<button class="fas fa-arrow-alt-circle-down texto-gris ml-2 mr-2" style="background-color: white; border: none;"></button>',
          selection: true,                              
        },        
      },
      height: 450,
      type: 'heatmap',
    },
    dataLabels: {
      enabled: false
    },
    colors: colors,
    series: data,
    xaxis: {
      type: 'category',
      categories: ['00:00', '01:00', '02:00', '03:00', '04:00', '05:00', '06:00', '07:00', '08:00', '09:00', '10:00', '11:00', '12:00',
        '13:00', '14:00', '15:00', '16:00', '17:00', '18:00', '19:00', '20:00', '21:00', '22:00', '23:00'
      ]
    },
    title: {
      text: ''
    },
    grid: {
      padding: {
        right: 20
      }
    }
  }

  var chart = new ApexCharts(
    ctx,
    options
  );

  chart.render();

}