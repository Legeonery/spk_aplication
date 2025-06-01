// node/chart/generateChart.js
const { ChartJSNodeCanvas } = require('chartjs-node-canvas');
const fs = require('fs');

const width = 1000;
const height = 400;

const chartJSNodeCanvas = new ChartJSNodeCanvas({ width, height });

const input = process.argv[2]; // JSON data
const output = process.argv[3]; // output path

(async () => {
  const config = JSON.parse(input);
  const buffer = await chartJSNodeCanvas.renderToBuffer({
    type: 'bar',
    data: {
      labels: config.labels,
      datasets: config.datasets,
    },
    options: {
      plugins: {
        title: { display: true, text: 'Поставки и отгрузки по культурам' },
        legend: { position: 'bottom' },
      },
      responsive: true,
      scales: {
        x: { stacked: false },
        y: { beginAtZero: true },
      },
    },
  });

  fs.writeFileSync(output, buffer);
})();
