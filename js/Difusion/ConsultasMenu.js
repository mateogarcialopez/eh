export function initResultadosConsultasMenuGraph(data) {
  // grafico resultados

  var ctx = document.getElementById("ingresoMenus");

  var chart = new CanvasJS.Chart(ctx, {
    exportEnabled: false,
    animationEnabled: true,
    title:{
      text: ""
    },
    legend:{
      cursor: "pointer",
      itemclick: explodePie
    },
    data: [{
      type: "pie",
      showInLegend: false,
      toolTipContent: "{name}: <strong>{y}</strong>",
      indexLabel: "{name}",
      dataPoints: [
        { y: 88, name: "Temas de Interés"},
        { y: 88, name: "Factura" },
        { y: 88, name: "Pago Línea" },
        { y: 88, name: "Puntos de atención" },
        { y: 88, name: "Comunicar Asesor" },
        { y: 88, name: "Otros" }
      ]
    }]
  });
  chart.render();
  }
  
  function explodePie (e) {
    if(typeof (e.dataSeries.dataPoints[e.dataPointIndex].exploded) === "undefined" || !e.dataSeries.dataPoints[e.dataPointIndex].exploded) {
      e.dataSeries.dataPoints[e.dataPointIndex].exploded = true;
    } else {
      e.dataSeries.dataPoints[e.dataPointIndex].exploded = false;
    }
    e.chart.render();

  /* Highcharts.chart(ctx, {
    chart: {
      type: 'pie',
      options3d: {
        enabled: true,
        alpha: 45,
        beta: 0
      }
    },
    title: {
      text: ''
    },
    tooltip: {
      pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
    },
    plotOptions: {
      pie: {
        allowPointSelect: true,
        cursor: 'pointer',
        depth: 35,
        dataLabels: {
          enabled: true,
          format: '{point.name}'
        }
      }
    },
    series: [{
      type: 'pie',
      name: 'Porcentaje',
      data: [
        ["Falta de Energía", data.faltaEnergia],
        ["Puntos de Atención", data.puntosAtencion],
        ["Pago en Línea", data.pagoLinea],
        ["Vacantes", data.vacantes],
        ["Otros", data.otros]
      ]
    }]
  }); */
}