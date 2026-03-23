document.addEventListener('DOMContentLoaded', function() {
    initTheme();
    initMobileMenu();
    initLandingCharts();
    initChartToggle();
});

function initTheme() {
    const themeToggle = document.querySelector('.theme-toggle');
    const icon = themeToggle ? themeToggle.querySelector('i') : null;
    const savedTheme = localStorage.getItem('theme') || 'light';

    if (savedTheme === 'dark') {
        document.body.classList.add('dark-mode');
    }

    updateThemeIcon(icon);

    if (themeToggle) {
        themeToggle.addEventListener('click', function() {
            document.body.classList.toggle('dark-mode');
            const currentTheme = document.body.classList.contains('dark-mode') ? 'dark' : 'light';
            localStorage.setItem('theme', currentTheme);
            updateThemeIcon(icon);
        });
    }
}

function updateThemeIcon(icon) {
    if (!icon) {
        return;
    }

    if (document.body.classList.contains('dark-mode')) {
        icon.className = 'fas fa-sun';
    } else {
        icon.className = 'fas fa-moon';
    }
}

function initMobileMenu() {
    const mobileMenuBtn = document.querySelector('.mobile-menu-btn');
    const navMenu = document.querySelector('.nav-menu');

    if (mobileMenuBtn && navMenu) {
        mobileMenuBtn.addEventListener('click', function() {
            navMenu.classList.toggle('active');
        });
    }
}

function initLandingCharts() {
    if (typeof Chart === 'undefined') {
        return;
    }

    renderHeroChart();
    renderZoneCharts();
    renderAnalysisChart('bar');
}

function renderHeroChart() {
    const chartElement = document.getElementById('heroZoneChart');
    if (!chartElement) {
        return;
    }

    new Chart(chartElement, {
        type: 'doughnut',
        data: {
            labels: ['Healthy', 'Stressed', 'Deficient'],
            datasets: [{
                data: [62, 23, 15],
                backgroundColor: ['#66bb6a', '#ffa726', '#ef5350'],
                borderWidth: 0
            }]
        },
        options: getChartOptions(true)
    });
}

function renderZoneCharts() {
    document.querySelectorAll('.zone-chart').forEach(function(canvas) {
        new Chart(canvas, {
            type: 'pie',
            data: {
                labels: ['Healthy', 'Stressed', 'Deficient'],
                datasets: [{
                    data: [canvas.dataset.healthy, canvas.dataset.stressed, canvas.dataset.deficient],
                    backgroundColor: ['#66bb6a', '#ffa726', '#ef5350'],
                    borderWidth: 0
                }]
            },
            options: getChartOptions()
        });
    });
}

let analysisChartInstance;
function renderAnalysisChart(type) {
    const chartElement = document.getElementById('analysisChart');
    if (!chartElement) {
        return;
    }

    if (analysisChartInstance) {
        analysisChartInstance.destroy();
    }

    analysisChartInstance = new Chart(chartElement, {
        type: type,
        data: {
            labels: ['Water Requirement', 'Pest Risk', 'Nutrition Need'],
            datasets: [{
                label: type === 'bar' ? 'Risk Distribution' : 'Trend Score',
                data: [78, 54, 66],
                backgroundColor: ['#42a5f5', '#ffa726', '#66bb6a'],
                borderColor: ['#42a5f5', '#ffa726', '#66bb6a'],
                borderWidth: 2,
                fill: type === 'line'
            }]
        },
        options: getChartOptions(false, type)
    });
}

function getChartOptions(isCompact = false, type = 'pie') {
    const darkMode = document.body.classList.contains('dark-mode');
    const labelColor = darkMode ? '#edf7ee' : '#1f3426';
    const gridColor = darkMode ? 'rgba(237,247,238,0.08)' : 'rgba(31,52,38,0.08)';

    return {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: !isCompact,
                position: 'bottom',
                labels: {
                    color: labelColor,
                    boxWidth: 12,
                    padding: 16
                }
            }
        },
        scales: type === 'pie' || type === 'doughnut' ? {} : {
            x: {
                ticks: { color: labelColor },
                grid: { color: gridColor }
            },
            y: {
                beginAtZero: true,
                ticks: { color: labelColor },
                grid: { color: gridColor }
            }
        }
    };
}

function initChartToggle() {
    const toggleButton = document.querySelector('.chart-toggle');
    if (!toggleButton) {
        return;
    }

    toggleButton.addEventListener('click', function() {
        const nextType = toggleButton.dataset.mode === 'line' ? 'bar' : 'line';
        toggleButton.dataset.mode = nextType;
        toggleButton.textContent = nextType === 'line' ? 'Show Bars' : 'Switch View';
        renderAnalysisChart(nextType);
    });
}
