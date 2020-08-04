export function initDifusionFaqGraph(data) {

    var ctx = document.getElementById("grapDifusionHoraPromfaq");
    new Chart(ctx, {
        type: "bar",
        data: {
            labels: ['Proveedores', 'Tarifas', 'Hoja de Vida', 'Proyectos', 'Convenios', 'Leyes', 'Usuarios', 'Alumbrado Publico', 'Normatividad', 'Factura'],
            datasets: [{
                label: 'Numero de Consultas',
                data: [data[0], data[1], data[2], data[3], data[4], data[5], data[6], data[7], data[8], data[9]],
                backgroundColor: [
                    'rgb(187, 52, 23,0.5)',
                    'rgb(187, 147, 23,0.5)',
                    'rgb(229, 89, 50,0.5)',
                    'rgb(66, 134, 244,0.5)',
                    'rgb(74, 135, 72,0.5)',
                    'rgb(144, 187, 23,0.5)',
                    'rgb(23, 187, 65,0.5)',
                    'rgb(32, 124, 103,0.5)',
                    'rgb(32, 62, 124,0.5)',
                    'rgb(39, 69, 154,0.5)',
                ]
            }]
        },
        options: {
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true
                    }
                }]
            }
        }
    });

      // on the submit event, generate a image from the canvas and save the data in the textarea
  document.getElementById('form1').addEventListener("submit", function () {
    var image = ctx.toDataURL(); // data:image/png....
    document.getElementById('base65').value = image;
  }, false);
}