/* globals Chart:false */
// (() => {
//   'use strict'

// Cria o gráfico
    // Obtém o contexto 2D do canvas
    const ctx = document.getElementById('graficoOcupacao');

    new Chart(ctx, {
        type: 'pie',
        data: {
            labels: ['Categoria A', 'Categoria B', 'Categoria C', 'Categoria D'],
            datasets: [{
                label: 'Minha Distribuição',
                data: [300, 50, 100, 75],
                backgroundColor: [
                    'rgb(255, 99, 132)', // Cor para Categoria A
                    'rgb(54, 162, 235)',  // Cor para Categoria B
                    'rgb(255, 205, 86)',  // Cor para Categoria C
                    'rgb(75, 192, 192)'   // Cor para Categoria D
                ],
                hoverOffset: 4 // Efeito de destaque ao passar o mouse
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    display: true,
                    text: 'Exemplo de Gráfico de Pizza'
                }
            }
        }
    });
//})()