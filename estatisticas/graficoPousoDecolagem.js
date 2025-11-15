/* globals Chart:false */
(() => {
  'use strict'

  // Graphs
  const ctx = document.getElementById('graficoPousoDecolagem');
  // eslint-disable-next-line no-unused-vars
  const myChart = new Chart(ctx, {
    type: 'line',
    data: {
      labels: ['Domingo','Segunda','Terça','Quarta','Quinta','Sexta','Sábado'],
      datasets: [{
        label: 'Pousos',
        data: [15,21,18,24,23,24,12],
        lineTension: 0,
        backgroundColor: 'transparent',
        borderColor: '#007bff',
        borderWidth: 4,
        pointBackgroundColor: '#007bff',
      },{
        label: 'Decolagens',
        data: [50,2,1,2,2,4,1],
        lineTension: 0,
        backgroundColor: 'transparent',
        borderColor: '#ff7bff',
        borderWidth: 4,
        pointBackgroundColor: '#007bff'
      }],
    },
    options: {
      plugins: {
        legend: {
          display: true
        },
        tooltip: {
          boxPadding: 3
        }
      }
    }
  })
})()
