export function initDifusionHoraPromedioGraph(data) {

  var sumaHoras = data[1];

  var ctx = document.getElementById("grapDifusionHoraProm");
  new Chart(ctx, {
    type: 'line',
    data: {
      labels: ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20', '21', '22', '23'],
      datasets: [{
        data: [
          (sumaHoras['00']), (sumaHoras['01']),
          (sumaHoras['02']), (sumaHoras['03']),
          (sumaHoras['04']), (sumaHoras['05']),
          (sumaHoras['06']), (sumaHoras['07']),
          (sumaHoras['08']), (sumaHoras['09']),
          (sumaHoras['10']), (sumaHoras['11']),
          (sumaHoras['12']), (sumaHoras['13']),
          (sumaHoras['14']), (sumaHoras['15']),
          (sumaHoras['16']), (sumaHoras['17']),
          (sumaHoras['18']), (sumaHoras['19']),
          (sumaHoras['20']), (sumaHoras['21']),
          (sumaHoras['22']), (sumaHoras['23'])
        ],
        label: "Cantidad Hora",
        borderColor: "#c45850",
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

  var dataURL = ctx.toDataURL('image/png');

  // on the submit event, generate a image from the canvas and save the data in the textarea
  document.getElementById('form').addEventListener("submit", function () {
    var image = ctx.toDataURL(); // data:image/png....
    document.getElementById('base64').value = image;
  }, false);

  // Build the chart


}