export function initDifusionComparacionTipificacion(CantidadLucy, CantidadDifusion, CantidadLlamadas, categories, type) {

  var options = {
    chart: {
      height: 350,
      type: 'line',
      zoom: {
        enabled: false
      },
    },
    dataLabels: {
      enabled: false
    },
    stroke: {
      width: [2, 3, 4],
      curve: 'straight',
      dashArray: [0, 2, 3]
    },
    series: [{
      name: 'Consultas Chatbot',
      data: CantidadDifusion
    }, {
      name: 'Llamadas',
      data: CantidadLlamadas
    }],
    markers: {
      size: 0,

      hover: {
        sizeOffset: 3
      },
      colors: ['#55F61B', '#1F1BDA', '#9C27B0']
    },
    xaxis: {
      categories: categories,
    },
    tooltip: {
      y: [{
        title: {
          formatter: function (val) {
            return val + " Consultas"
          }
        }
      }, {
        title: {
          formatter: function (val) {
            return val;
          }
        }
      }]
    },
    grid: {
      borderColor: '#E3E3E3',
    }
  }

  var chart = new ApexCharts(
    document.querySelector(type),
    options
  );

  chart.render();

}