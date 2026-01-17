<?php
session_start();
require_once '../config/config.php';
require_once '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$db = new Database();

$stmt = $db->query("SELECT * FROM users WHERE id = ?");
$user = $db->fetch($stmt, [$_SESSION['user_id']]);

$stmt = $db->query("SELECT * FROM weather_data WHERE user_id = ? ORDER BY recorded_date DESC, recorded_time DESC LIMIT 1");
$weather = $db->fetch($stmt, [$_SESSION['user_id']]);

if (!$weather) {
    $weather = [
        'temperature' => 28.5,
        'rainfall' => 0,
        'humidity' => 65,
        'wind_speed' => 12
    ];
}

$stmt = $db->query("SELECT * FROM government_schemes WHERE status = 'active' ORDER BY created_at DESC LIMIT 6");
$schemes = $db->fetchAll($stmt);

$stmt = $db->query("SELECT COUNT(*) as count FROM fields WHERE user_id = ?");
$field_count = $db->fetch($stmt, [$_SESSION['user_id']]);

$stmt = $db->query("SELECT COUNT(*) as count FROM greenhouses WHERE user_id = ?");
$greenhouse_count = $db->fetch($stmt, [$_SESSION['user_id']]);

$stmt = $db->query("SELECT COUNT(*) as count FROM appointments WHERE user_id = ? AND status = 'pending'");
$pending_appointments = $db->fetch($stmt, [$_SESSION['user_id']]);

$stmt = $db->query("SELECT COUNT(*) as count FROM notifications WHERE user_id = ? AND is_read = FALSE");
$unread_notifications = $db->fetch($stmt, [$_SESSION['user_id']]);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - AGRIVISION</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="dashboard-container">
        <aside class="sidebar">
            <div class="sidebar-header">
                <h3><i class="fas fa-leaf"></i> AGRIVISION</h3>
            </div>
            <nav class="sidebar-menu">
                <a href="dashboard.php" class="active"><i class="fas fa-home"></i> <?php echo t('dashboard'); ?></a>
                <a href="my-field.php"><i class="fas fa-seedling"></i> <?php echo t('my_field'); ?></a>
                <a href="krishi-mandi.php"><i class="fas fa-store"></i> <?php echo t('krishi_mandi'); ?></a>
                <a href="my-greenhouse.php"><i class="fas fa-warehouse"></i> <?php echo t('my_greenhouse'); ?></a>
                <a href="ai-support.php"><i class="fas fa-robot"></i> <?php echo t('ai_support'); ?></a>
                <a href="appointments.php"><i class="fas fa-calendar"></i> <?php echo t('appointments'); ?></a>
                <a href="profile.php"><i class="fas fa-user"></i> <?php echo t('profile'); ?></a>
                <a href="logout.php"><i class="fas fa-sign-out-alt"></i> <?php echo t('logout'); ?></a>
            </nav>
        </aside>
        
        <main class="main-content">
            <div class="top-bar">
                <div class="user-info">
                    <div class="user-avatar"><?php echo strtoupper(substr($user['name'], 0, 1)); ?></div>
                    <div>
                        <h4><?php echo htmlspecialchars($user['name']); ?></h4>
                        <small>Farmer</small>
                    </div>
                </div>
                <div style="display: flex; gap: 15px; align-items: center;">
                    <a href="notifications.php" style="position: relative; color: var(--text-dark); text-decoration: none;">
                        <i class="fas fa-bell fa-lg"></i>
                        <?php if ($unread_notifications['count'] > 0): ?>
                            <span style="position: absolute; top: -8px; right: -8px; background: #f44336; color: white; border-radius: 50%; width: 18px; height: 18px; font-size: 10px; display: flex; align-items: center; justify-content: center;"><?php echo $unread_notifications['count']; ?></span>
                        <?php endif; ?>
                    </a>
                    <button class="theme-toggle" title="Toggle Theme">
                        <i class="fas fa-moon"></i>
                    </button>
                </div>
            </div>
            
            <div class="grid-4" style="margin-bottom: 30px;">
                <div class="stat-card">
                    <i class="fas fa-map"></i>
                    <h4><?php echo $field_count['count']; ?></h4>
                    <p><?php echo t('my_field'); ?></p>
                </div>
                <div class="stat-card">
                    <i class="fas fa-warehouse"></i>
                    <h4><?php echo $greenhouse_count['count']; ?></h4>
                    <p><?php echo t('my_greenhouse'); ?></p>
                </div>
                <div class="stat-card">
                    <i class="fas fa-calendar-check"></i>
                    <h4><?php echo $pending_appointments['count']; ?></h4>
                    <p><?php echo t('pending_appointments'); ?></p>
                </div>
                <div class="stat-card">
                    <i class="fas fa-bell"></i>
                    <h4><?php echo $unread_notifications['count']; ?></h4>
                    <p><?php echo t('unread'); ?> <?php echo t('alerts'); ?></p>
                </div>
            </div>
            
            <div class="grid-2" style="margin-bottom: 30px;">
                <div class="card">
                    <div class="card-header">
                        <h3><i class="fas fa-cloud-sun"></i> <?php echo t('live_weather'); ?></h3>
                    </div>
                    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px;">
                        <div style="text-align: center; padding: 20px; background: var(--bg-light); border-radius: var(--radius);">
                            <i class="fas fa-temperature-high fa-2x" style="color: var(--secondary-color);"></i>
                            <h4 style="font-size: 2rem; margin: 10px 0;"><?php echo $weather['temperature']; ?>°C</h4>
                            <p><?php echo t('temperature'); ?></p>
                        </div>
                        <div style="text-align: center; padding: 20px; background: var(--bg-light); border-radius: var(--radius);">
                            <i class="fas fa-tint fa-2x" style="color: #2196f3;"></i>
                            <h4 style="font-size: 2rem; margin: 10px 0;"><?php echo $weather['rainfall']; ?> mm</h4>
                            <p><?php echo t('rainfall'); ?></p>
                        </div>
                        <div style="text-align: center; padding: 20px; background: var(--bg-light); border-radius: var(--radius);">
                            <i class="fas fa-water fa-2x" style="color: #00bcd4;"></i>
                            <h4 style="font-size: 2rem; margin: 10px 0;"><?php echo $weather['humidity']; ?>%</h4>
                            <p><?php echo t('humidity'); ?></p>
                        </div>
                        <div style="text-align: center; padding: 20px; background: var(--bg-light); border-radius: var(--radius);">
                            <i class="fas fa-wind fa-2x" style="color: #9e9e9e;"></i>
                            <h4 style="font-size: 2rem; margin: 10px 0;"><?php echo $weather['wind_speed']; ?> km/h</h4>
                            <p><?php echo t('wind_speed'); ?></p>
                        </div>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <h3><i class="fas fa-chart-line"></i> Weekly Weather Trend</h3>
                    </div>
                    <div class="chart-container">
                        <canvas id="weatherChart"></canvas>
                    </div>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-hand-holding-usd"></i> <?php echo t('government_schemes'); ?></h3>
                    <a href="schemes.php" class="btn btn-sm btn-outline"><?php echo t('view_all'); ?></a>
                </div>
                <div class="schemes-grid">
                    <?php foreach ($schemes as $scheme): ?>
                        <div class="scheme-card">
                            <h4><?php echo htmlspecialchars($scheme['title']); ?></h4>
                            <p><?php echo substr(htmlspecialchars($scheme['description']), 0, 150); ?>...</p>
                            <a href="schemes.php?id=<?php echo $scheme['id']; ?>" class="btn btn-sm"><?php echo t('view_details'); ?></a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </main>
    </div>
    
    <nav class="bottom-nav">
        <div class="container">
            <a href="dashboard.php" class="active">
                <i class="fas fa-home"></i>
                <span><?php echo t('dashboard'); ?></span>
            </a>
            <a href="my-field.php">
                <i class="fas fa-seedling"></i>
                <span><?php echo t('my_field'); ?></span>
            </a>
            <a href="krishi-mandi.php">
                <i class="fas fa-store"></i>
                <span><?php echo t('krishi_mandi'); ?></span>
            </a>
            <a href="profile.php">
                <i class="fas fa-user"></i>
                <span><?php echo t('profile'); ?></span>
            </a>
        </div>
    </nav>
    
    <script>
        const ctx = document.getElementById('weatherChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                datasets: [{
                    label: 'Temperature (°C)',
                    data: [28, 30, 27, 29, 31, 28, 26],
                    borderColor: '#ff9800',
                    backgroundColor: 'rgba(255, 152, 0, 0.1)',
                    fill: true,
                    tension: 0.4
                }, {
                    label: 'Humidity (%)',
                    data: [65, 60, 70, 68, 55, 62, 72],
                    borderColor: '#2196f3',
                    backgroundColor: 'rgba(33, 150, 243, 0.1)',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
    
    <script src="../assets/js/main.js"></script>
</body>
</html>
