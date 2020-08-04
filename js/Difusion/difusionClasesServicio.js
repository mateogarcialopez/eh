export function initDifusionClasesGraph(data) {

    new Chart(document.getElementById("DifusionClasesServicio"), {
        type: 'pie',
        data: {
          labels: ['Alu. Público', 'Otros', 'Serv. y Oficial', 'Residencial', 'Comercial', 'Industrial', 'Esp. Asistencial', 'Esp. Educativo', 'Areas Comunes', 'Oxigenode.', 'Provisional'],
          datasets: [{
            data: [data.alumbrado, data.otros, data.oficial, data.residencial, data.comercial, data.industria, data.asistencial, data.educativo, data.areasComunes, data.oxigeno, data.provisional],
            label: "Segmento",
            fill: true,
            backgroundColor: [
              "#56d798",
              "#f38b4a",
              "#6CFF43",
              "#7CB5EC",
              "#857CEC",
              "#EC7CEA",
              "#EC7C88",
              "#7CEC8E",
              "#D0EC7C",
              "#B6A461",
              "#C33204",
            ],
            hoverBackgroundColor: [
              "#56d798",
              "#f38b4a",
              "#6CFF43",
              "#7CB5EC",
              "#857CEC",
              "#EC7CEA",
              "#EC7C88",
              "#7CEC8E",
              "#D0EC7C",
              "#B6A461",
              "#C33204",
            ]
          }]
        },
        options: {
          legend: {
            position:'bottom',
            display: true,
            labels: {
              fontColor: 'black'
          }
          }
        }
      });
}