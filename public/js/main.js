Chart.defaults.pointHitDetectionRadius = 1;
Chart.defaults.plugins.tooltip.enabled = false;
Chart.defaults.plugins.tooltip.mode = 'index';
Chart.defaults.plugins.tooltip.position = 'nearest';
Chart.defaults.plugins.tooltip.external = coreui.ChartJS.customTooltips;
Chart.defaults.color = coreui.Utils.getStyle('--cui-body-color');



document.addEventListener('DOMContentLoaded', function () {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    fetch('/dashboard', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok: ' + response.statusText);
        }
        return response.json();
    })
    .then(data => {
        if (!data.labels || !data.datasets) {
            throw new Error('Invalid data format');
        }

        const cardChartNew1 = new Chart(document.getElementById('card-chart-new1'), {
            type: 'line',
            data: {
                labels: data.labels,
                datasets: data.datasets
            },
            options: {
                plugins: {
                    legend: {
                        display: false
                    }
                },
                maintainAspectRatio: false,
                scales: {
                    x: {
                        display: false
                    },
                    y: {
                        beginAtZero: true,
                        display: false
                    }
                },
                elements: {
                    line: {
                        borderWidth: 2,
                        tension: 0.4
                    },
                    point: {
                        radius: 0,
                        hitRadius: 10,
                        hoverRadius: 4
                    }
                }
            }
        });
    })
    .catch(error => console.error('Fetch error:', error));
});


document.body.addEventListener('themeChange', () => {
   cardChart1.data.datasets[0].pointBackgroundColor = coreui.Utils.getStyle('--cui-primary');
   cardChart1.update();
});

   


         /* primera grafica derecha TRAFICO MENSUAL */

         document.addEventListener('DOMContentLoaded', function () {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
            fetch('/dashboard/migrantes-mensual', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({})
            })
            .then(response => response.json())
            .then(data => {
                const ctx = document.getElementById('main-bar-chart').getContext('2d');
                const mainBarChart = new Chart(ctx, {
                    type: 'bar',
                    data: data,
                    options: {
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: true
                            }
                        },
                        scales: {
                            x: {
                                grid: {
                                    display: false,
                                    drawBorder: false,
                                    drawTicks: false
                                },
                                ticks: {
                                    color: coreui.Utils.getStyle('--cui-text-disabled'),
                                    font: {
                                        size: 14
                                    },
                                    padding: 16
                                }
                            },
                            y: {
                                grid: {
                                    drawBorder: false,
                                    borderDash: [2, 4]
                                },
                                ticks: {
                                    beginAtZero: true,
                                    color: coreui.Utils.getStyle('--cui-text-disabled'),
                                    font: {
                                        size: 14
                                    },
                                    maxTicksLimit: 5,
                                    padding: 16,
                                    stepSize: Math.ceil(100 / 4)
                                }
                            }
                        }
                    }
                });
            })
            .catch(error => console.error('Error:', error));
        });
        

        document.addEventListener('DOMContentLoaded', function () {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
            fetch('/dashboard/migrantes-semanal', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({})
            })
            .then(response => response.json())
            .then(data => {
                document.getElementById('totalLastWeek').textContent = data.totals.totalLastWeek;
                document.getElementById('totalCurrentWeek').textContent = data.totals.totalCurrentWeek;
                document.getElementById('totalYearly').textContent = data.totals.totalYearly;
                document.getElementById('totalMonthly').textContent = data.totals.totalMonthly;
        
                document.getElementById('totalMasculino').textContent = data.gender.totalMasculino;
                document.getElementById('totalFemenino').textContent = data.gender.totalFemenino;
        
                const porcentajeMasculino = data.gender.porcentajeMasculino.toFixed(1);
                const porcentajeFemenino = data.gender.porcentajeFemenino.toFixed(1);
        
                document.getElementById('porcentajeMasculino').innerHTML = `
                    (${porcentajeMasculino}% 
                    )`;
        
                document.getElementById('porcentajeFemenino').innerHTML = `
                    (${porcentajeFemenino}% 
                    )`;
        
                // Procesar datos de grupos de edad
                const ageGroupLabels = {
                    '0-6': '0 - 6 años (Menores de edad)',
                    '7-17': '7 - 17 años (Menores de edad)',
                    '18-27': '18 - 27 años (Adultos)',
                    '28-37': '28 - 37 años (Adultos)',
                    '38-47': '38 - 47 años (Adultos)',
                    '48-57': '48 - 57 años (Adultos)',
                    '58-84': '58 - 84 años (Adultos)',
                    '85+': '85 años y más (Adultos)'
                };
        
                let htmlContent = '';
        
                data.ageGroups.forEach(item => {
                    const label = ageGroupLabels[item.age_group];
                    const percentage = (item.total_migrants / data.totals.totalYearly * 100).toFixed(1);
        
                    htmlContent += `
                        <div class="progress-group">
                            <div class="progress-group-header">
                                <div>${label}</div>
                                <div class="ms-auto fw-semibold me-2">${item.total_migrants}</div>
                                <div class="text-disabled small">(${percentage}%)</div>
                            </div>
                            <div class="progress-group-bars">
                                <div class="progress progress-thin">
                                    <div class="progress-bar bg-success-gradient " role="progressbar" style="width: ${percentage}%" aria-valuenow="${percentage}" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        </div>
                    `;
                });
        
                document.getElementById('migrantes-edad').innerHTML = htmlContent;
        
                // Procesar datos semanales
                const daysOfWeek = ['Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sabado', 'Domingo'];
                const yearlyData = {};
                const weeklyData = {};
        
                data.yearly.forEach(item => {
                    yearlyData[item.day_of_week] = item.total_migrants;
                });
        
                data.weekly.forEach(item => {
                    weeklyData[item.day_of_week] = item.total_migrants;
                });
        
                htmlContent = ''; // Reset HTML content for weekly data
        
                daysOfWeek.forEach((day, index) => {
                    const dayOfWeek = index + 1;
                    const yearlyTotal = yearlyData[dayOfWeek] || 0;
                    const weeklyTotal = weeklyData[dayOfWeek] || 0;
        
                    htmlContent += `
                        <div class="progress-group mb-4">
                            <div class="progress-group-prepend"><span class="text-disabled small">${day}</span></div>
                            <div class="progress-group-bars">
                                <div class="progress progress-thin">
                                    <div class="progress-bar bg-info-gradient" role="progressbar" style="width: ${yearlyTotal}%"
                                        aria-valuenow="${yearlyTotal}" aria-valuemin="0" aria-valuemax="100"
                                        onmouseover="showTooltip(event, '${day}', ${yearlyTotal}, ${weeklyTotal})"
                                        onmouseout="hideTooltip()"></div>
                                </div>
                                <div class="progress progress-thin">
                                    <div class="progress-bar bg-danger-gradient" role="progressbar" style="width: ${weeklyTotal}%"
                                        aria-valuenow="${weeklyTotal}" aria-valuemin="0" aria-valuemax="100"
                                        onmouseover="showTooltip(event, '${day}', ${yearlyTotal}, ${weeklyTotal})"
                                        onmouseout="hideTooltip()"></div>
                                </div>
                            </div>
                        </div>
                    `;
                });
        
                document.getElementById('migrantes-semanal-weekly').innerHTML = htmlContent;
            })
            .catch(error => console.error('Error:', error));
        });
        
        function showTooltip(event, day, yearlyTotal, weeklyTotal) {
            let tooltip = document.getElementById('tooltip');
            if (!tooltip) {
                tooltip = document.createElement('div');
                tooltip.id = 'tooltip';
                tooltip.style.position = 'absolute';
                tooltip.style.background = '#333';
                tooltip.style.color = '#fff';
                tooltip.style.padding = '5px 10px';
                tooltip.style.borderRadius = '5px';
                tooltip.style.zIndex = '1000';
                document.body.appendChild(tooltip);
            }
        
            tooltip.innerHTML = `
                <strong>${day}</strong><br>
                <span class="bg-info-gradient" style="display: inline-block; width: 20px; height: 10px;"></span> Total Anual: ${yearlyTotal}<br>
                <span class="bg-danger-gradient" style="display: inline-block; width: 20px; height: 10px;"></span> Semana Actual: ${weeklyTotal}
            `;
            tooltip.style.left = event.pageX + 10 + 'px';
            tooltip.style.top = event.pageY + 10 + 'px';
            tooltip.style.display = 'block';
        }
        
        function hideTooltip() {
            const tooltip = document.getElementById('tooltip');
            if (tooltip) {
                tooltip.style.display = 'none';
            }
        }
        
