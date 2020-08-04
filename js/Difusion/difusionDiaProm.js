export function initDifusionDiaPromedioGraph(data) {

  var diasCantidad = data[1];

  var ctx = document.getElementById("DifusionDiaProm");
  new Chart(ctx, {
    type: 'line',
    data: {
      labels: ['Domingo', 'Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sábado'],
      datasets: [{
        data: [diasCantidad[6], diasCantidad[0], diasCantidad[1], diasCantidad[2],
          diasCantidad[3], diasCantidad[4],  diasCantidad[5]
        ],
        label: "Cantidad Día",
        borderColor: "#8e5ea2",
        fill: false
      }]
    },
    options: {
      title: {
        display: false,
        text: 'World population per region (in millions)'
      }
    }
  });

    // on the submit event, generate a image from the canvas and save the data in the textarea
    document.getElementById('form2').addEventListener("submit", function () {
      var image = ctx.toDataURL(); // data:image/png....
      document.getElementById('base66').value = image;
    }, false);
}