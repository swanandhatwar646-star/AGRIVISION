<?php
session_start();
require_once 'config/config.php';
require_once 'config/database.php';

if (isset($_SESSION['user_id'])) {
    header('Location: user/dashboard.php');
    exit();
} elseif (isset($_SESSION['admin_id'])) {
    header('Location: admin/dashboard.php');
    exit();
}

$weatherStats = [
    ['label' => 'Temperature', 'value' => '29°C', 'icon' => 'fa-temperature-three-quarters'],
    ['label' => 'Rainfall', 'value' => '18 mm', 'icon' => 'fa-cloud-rain'],
    ['label' => 'Humidity', 'value' => '68%', 'icon' => 'fa-droplet'],
    ['label' => 'Wind Speed', 'value' => '14 km/h', 'icon' => 'fa-wind'],
];

$schemes = [
    ['title' => 'PM-KISAN Support', 'summary' => 'Quarterly income support with timely status updates and document reminders for registered farmers.'],
    ['title' => 'Soil Health Card', 'summary' => 'Zone-based recommendations for balanced nutrients, fertilizer planning, and soil recovery strategies.'],
    ['title' => 'Crop Insurance Alerts', 'summary' => 'Track policy windows, rainfall risks, and claim-ready advisory cards from one dashboard.'],
];

$fieldZones = [
    ['zone' => 'Zone 1', 'status' => 'Mostly Healthy', 'healthy' => 68, 'stressed' => 20, 'deficient' => 12],
    ['zone' => 'Zone 2', 'status' => 'Water Stress', 'healthy' => 52, 'stressed' => 30, 'deficient' => 18],
    ['zone' => 'Zone 3', 'status' => 'Nutrition Alert', 'healthy' => 61, 'stressed' => 17, 'deficient' => 22],
];

$marketProducts = [
    ['name' => 'Bio Crop Shield', 'supplier' => 'GreenLeaf Agro', 'price' => '₹780', 'region' => 'Nashik, Maharashtra'],
    ['name' => 'Drip Nutrient Kit', 'supplier' => 'AquaFarm Inputs', 'price' => '₹1,240', 'region' => 'Mysuru, Karnataka'],
    ['name' => 'Smart Soil Booster', 'supplier' => 'AgriNova Suppliers', 'price' => '₹960', 'region' => 'Guntur, Andhra Pradesh'],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AGRIVISION - Responsive Crop Health Monitoring Platform</title>
    <meta name="description" content="AGRIVISION is a responsive crop health monitoring platform with weather intelligence, field analytics, AI advisory, Krishi Mandi marketplace, and appointments.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/7.2.0/mdb.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="page-shell">
        <header class="topbar">
            <div class="container topbar-inner">
                <a href="#home" class="nav-brand" aria-label="AGRIVISION home">
                    <span class="brand-mark"><i class="fas fa-leaf"></i></span>
                    <span>
                        <strong>AGRIVISION</strong>
                        <small>Crop Health Monitoring Platform</small>
                    </span>
                </a>

                <button class="mobile-menu-btn" type="button" aria-label="Toggle menu">
                    <i class="fas fa-bars"></i>
                </button>

                <nav class="nav-menu" aria-label="Primary">
                    <a href="#dashboard">Dashboard</a>
                    <a href="#my-field">My Field</a>
                    <a href="#krishi-mandi">Krishi Mandi</a>
                    <a href="#contact">Contact</a>
                    <button class="theme-toggle" type="button" aria-label="Toggle theme">
                        <i class="fas fa-moon"></i>
                        <span>Theme</span>
                    </button>
                    <a href="user/login.php" class="btn btn-outline">Login</a>
                    <a href="user/signup.php" class="btn btn-primary">Sign Up</a>
                </nav>
            </div>
        </header>

        <main>
            <section class="hero-section" id="home">
                <div class="container hero-grid">
                    <div class="hero-copy">
                        <span class="eyebrow">Mobile-first digital agriculture experience</span>
                        <h1>One responsive platform for weather, crop health, advisory, and agri-commerce.</h1>
                        <p>Designed for farmers, analysts, suppliers, and field officers with adaptive mobile app-style screens and a clean desktop analytics workspace.</p>
                        <div class="hero-actions">
                            <a href="user/signup.php" class="btn btn-primary btn-lg">Start as Farmer</a>
                            <a href="#dashboard" class="btn btn-soft btn-lg">Explore Live Modules</a>
                        </div>
                        <div class="hero-highlights">
                            <div><strong>10+</strong><span>Languages</span></div>
                            <div><strong>24/7</strong><span>AI support</span></div>
                            <div><strong>Zone-wise</strong><span>Field analysis</span></div>
                        </div>
                    </div>

                    <div class="hero-visual">
                        <div class="device-mockup">
                            <div class="device-topbar">
                                <div class="device-brand"><i class="fas fa-seedling"></i> Agrivision</div>
                                <div class="device-icons">
                                    <i class="fas fa-bell"></i>
                                    <i class="fas fa-bars"></i>
                                </div>
                            </div>
                            <div class="device-content">
                                <div class="mini-weather-card glass-card">
                                    <span>Live Weather</span>
                                    <strong>Monsoon-ready field conditions</strong>
                                    <div class="mini-weather-stats">
                                        <span><i class="fas fa-temperature-three-quarters"></i> 29°C</span>
                                        <span><i class="fas fa-droplet"></i> 68%</span>
                                        <span><i class="fas fa-wind"></i> 14 km/h</span>
                                    </div>
                                </div>
                                <div class="mini-chart-card glass-card">
                                    <div class="chart-heading">
                                        <span>Field Zone Health</span>
                                        <strong>3 Active zones</strong>
                                    </div>
                                    <canvas id="heroZoneChart"></canvas>
                                </div>
                                <div class="device-bottom-nav">
                                    <span class="active"><i class="fas fa-house"></i><small>Dashboard</small></span>
                                    <span><i class="fas fa-chart-pie"></i><small>My Field</small></span>
                                    <span><i class="fas fa-store"></i><small>Mandi</small></span>
                                    <span><i class="fas fa-user"></i><small>Profile</small></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="dashboard-section section-pad" id="dashboard">
                <div class="container">
                    <div class="section-heading">
                        <span class="eyebrow">Dashboard</span>
                        <h2>Weather intelligence and government schemes in farmer-friendly cards.</h2>
                        <p>Responsive weather summaries stack vertically on mobile and align into dashboard grids on desktop.</p>
                    </div>
                    <div class="weather-grid">
                        <?php foreach ($weatherStats as $stat): ?>
                            <article class="info-card weather-card">
                                <div class="card-icon"><i class="fas <?= htmlspecialchars($stat['icon']) ?>"></i></div>
                                <div>
                                    <span class="card-label"><?= htmlspecialchars($stat['label']) ?></span>
                                    <strong><?= htmlspecialchars($stat['value']) ?></strong>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                    <div class="schemes-layout">
                        <div class="scheme-cards">
                            <?php foreach ($schemes as $scheme): ?>
                                <article class="info-card scheme-card">
                                    <span class="chip">Scheme</span>
                                    <h3><?= htmlspecialchars($scheme['title']) ?></h3>
                                    <p><?= htmlspecialchars($scheme['summary']) ?></p>
                                    <a href="user/schemes.php">View details <i class="fas fa-arrow-right"></i></a>
                                </article>
                            <?php endforeach; ?>
                        </div>
                        <aside class="spotlight-card info-card">
                            <span class="chip chip-accent">Adaptive UI</span>
                            <h3>Built for both native-feel mobile use and desktop analytics.</h3>
                            <ul>
                                <li>Top AppBar and touch-friendly cards for phones.</li>
                                <li>Grid dashboards and clear navigation for desktop users.</li>
                                <li>Dark and light themes for field and office environments.</li>
                            </ul>
                        </aside>
                    </div>
                </div>
            </section>

            <section class="field-section section-pad" id="my-field">
                <div class="container">
                    <div class="section-heading">
                        <span class="eyebrow">My Field</span>
                        <h2>Zone-wise crop health, risk distributions, farm reports, and AI suggestions.</h2>
                    </div>
                    <div class="field-layout">
                        <div class="zone-grid">
                            <?php foreach ($fieldZones as $index => $zone): ?>
                                <article class="info-card zone-card">
                                    <div class="zone-header">
                                        <div>
                                            <span class="card-label"><?= htmlspecialchars($zone['zone']) ?></span>
                                            <h3><?= htmlspecialchars($zone['status']) ?></h3>
                                        </div>
                                        <span class="status-pill">Live</span>
                                    </div>
                                    <div class="zone-chart-wrap">
                                        <canvas class="zone-chart" id="zoneChart<?= $index + 1 ?>"
                                            data-healthy="<?= $zone['healthy'] ?>"
                                            data-stressed="<?= $zone['stressed'] ?>"
                                            data-deficient="<?= $zone['deficient'] ?>"></canvas>
                                    </div>
                                    <div class="zone-legend">
                                        <span><i class="legend healthy"></i>Healthy <?= $zone['healthy'] ?>%</span>
                                        <span><i class="legend stressed"></i>Stressed <?= $zone['stressed'] ?>%</span>
                                        <span><i class="legend deficient"></i>Deficient <?= $zone['deficient'] ?>%</span>
                                    </div>
                                </article>
                            <?php endforeach; ?>
                        </div>

                        <div class="analysis-stack">
                            <article class="info-card analysis-card">
                                <div class="analysis-header">
                                    <div>
                                        <span class="card-label">Crop Analysis</span>
                                        <h3>Water, pest, and nutrition classifiers</h3>
                                    </div>
                                    <button class="btn btn-sm btn-soft chart-toggle" type="button" data-target="analysisChart">Switch View</button>
                                </div>
                                <canvas id="analysisChart"></canvas>
                                <div class="risk-tags">
                                    <span class="risk high">High risk</span>
                                    <span class="risk medium">Medium risk</span>
                                    <span class="risk low">Stable</span>
                                </div>
                            </article>

                            <article class="info-card report-card">
                                <div class="analysis-header">
                                    <div>
                                        <span class="card-label">Farm Reports</span>
                                        <h3>Upload reports, images, and documents</h3>
                                    </div>
                                    <a href="user/upload-report.php" class="btn btn-primary btn-sm">Upload</a>
                                </div>
                                <div class="report-bars">
                                    <div>
                                        <label>Yield performance</label>
                                        <progress value="84" max="100"></progress>
                                    </div>
                                    <div>
                                        <label>Irrigation efficiency</label>
                                        <progress value="71" max="100"></progress>
                                    </div>
                                    <div>
                                        <label>Soil recovery index</label>
                                        <progress value="63" max="100"></progress>
                                    </div>
                                </div>
                            </article>

                            <article class="info-card ai-card">
                                <span class="chip chip-accent">AI Decision Support</span>
                                <h3>Ask about crop health, fertilizers, pests, and weather.</h3>
                                <div class="chat-preview">
                                    <div class="bubble farmer">How do I reduce pest risk in Zone 2?</div>
                                    <div class="bubble ai">Use medium-dose biological spray, inspect leaf undersides, and reduce standing moisture for the next 48 hours.</div>
                                </div>
                                <a href="user/ai-support.php" class="btn btn-soft">Open AI Assistant</a>
                            </article>
                        </div>
                    </div>
                </div>
            </section>

            <section class="market-section section-pad" id="krishi-mandi">
                <div class="container">
                    <div class="section-heading">
                        <span class="eyebrow">Krishi Mandi</span>
                        <h2>Crop prediction, pesticide discovery, and location-based product selling.</h2>
                    </div>
                    <div class="market-layout">
                        <article class="info-card prediction-card">
                            <div class="analysis-header">
                                <div>
                                    <span class="card-label">Crop Prediction</span>
                                    <h3>Enter field conditions and get suitable crops</h3>
                                </div>
                            </div>
                            <form class="predict-form" onsubmit="return false;">
                                <label>
                                    <span>Sunlight</span>
                                    <input type="text" value="High sunlight / 8 hrs">
                                </label>
                                <label>
                                    <span>Water level</span>
                                    <input type="text" value="Moderate irrigation">
                                </label>
                                <label>
                                    <span>Soil quality</span>
                                    <input type="text" value="Loamy / Nitrogen medium">
                                </label>
                                <button class="btn btn-primary" type="button">Suggest Best Crops</button>
                            </form>
                            <div class="crop-suggestions">
                                <span>Recommended:</span>
                                <strong>Maize</strong>
                                <strong>Groundnut</strong>
                                <strong>Tur Dal</strong>
                            </div>
                        </article>

                        <article class="info-card pesticide-card">
                            <div class="analysis-header">
                                <div>
                                    <span class="card-label">Pesticide Information</span>
                                    <h3>Search trusted products by name or company</h3>
                                </div>
                            </div>
                            <div class="search-box">
                                <i class="fas fa-magnifying-glass"></i>
                                <input type="text" placeholder="Search pesticide, brand, or usage">
                            </div>
                            <ul class="pesticide-list">
                                <li><strong>Neem Guard</strong><span>Bio-safe foliar spray • 250 ml</span></li>
                                <li><strong>Fungi Secure</strong><span>Soil and leaf application • AgroCare Ltd.</span></li>
                                <li><strong>Stem Protect Plus</strong><span>Targeted pest control • Harvest Biotech</span></li>
                            </ul>
                        </article>

                        <article class="info-card marketplace-card">
                            <div class="analysis-header">
                                <div>
                                    <span class="card-label">Seller & Supplier Network</span>
                                    <h3>Location-based access to transparent supplier listings</h3>
                                </div>
                            </div>
                            <div class="product-list">
                                <?php foreach ($marketProducts as $product): ?>
                                    <div class="product-item">
                                        <div>
                                            <strong><?= htmlspecialchars($product['name']) ?></strong>
                                            <span><?= htmlspecialchars($product['supplier']) ?> • <?= htmlspecialchars($product['region']) ?></span>
                                        </div>
                                        <span class="price-tag"><?= htmlspecialchars($product['price']) ?></span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </article>
                    </div>
                </div>
            </section>

            <section class="support-section section-pad">
                <div class="container support-grid">
                    <article class="info-card appointment-card">
                        <span class="eyebrow">Book an Appointment</span>
                        <h2>Connect farmers with experts and officers in a few taps.</h2>
                        <form class="appointment-form" onsubmit="return false;">
                            <input type="text" placeholder="Farmer name">
                            <input type="text" placeholder="Address">
                            <input type="date">
                            <input type="tel" placeholder="Contact number">
                            <button class="btn btn-primary" type="button">Book Appointment</button>
                        </form>
                    </article>
                    <article class="info-card admin-card">
                        <span class="chip">Admin Dashboard</span>
                        <h3>Role-based backend for administrators and analysts.</h3>
                        <div class="admin-features">
                            <span>Farmer management</span>
                            <span>Weather API monitoring</span>
                            <span>Zone health analytics</span>
                            <span>Scheme visibility control</span>
                            <span>AI query review</span>
                            <span>Appointment scheduling</span>
                        </div>
                    </article>
                </div>
            </section>
        </main>

        <footer class="footer section-pad" id="contact">
            <div class="container footer-grid">
                <div>
                    <div class="nav-brand footer-brand">
                        <span class="brand-mark"><i class="fas fa-leaf"></i></span>
                        <span>
                            <strong>AGRIVISION</strong>
                            <small>Farmer-first digital agriculture</small>
                        </span>
                    </div>
                    <p>Responsive crop monitoring, smart marketplace access, AI guidance, and expert support from one connected platform.</p>
                </div>
                <div>
                    <h4>Contact Us</h4>
                    <ul>
                        <li>Email: support@agrivision.example</li>
                        <li>Phone: +91 98765 43210</li>
                        <li>Follow: Facebook • X • YouTube</li>
                    </ul>
                </div>
                <div>
                    <h4>Quick Links</h4>
                    <ul>
                        <li><a href="user/login.php">Login</a></li>
                        <li><a href="user/signup.php">Signup</a></li>
                        <li><a href="user/dashboard.php">User Dashboard</a></li>
                        <li><a href="admin/login.php">Admin Login</a></li>
                    </ul>
                </div>
            </div>
        </footer>

        <nav class="mobile-bottom-nav" aria-label="Mobile navigation">
            <a href="#dashboard" class="active"><i class="fas fa-house"></i><span>Dashboard</span></a>
            <a href="#my-field"><i class="fas fa-chart-pie"></i><span>My Field</span></a>
            <a href="#krishi-mandi"><i class="fas fa-store"></i><span>Mandi</span></a>
            <a href="user/profile.php"><i class="fas fa-user"></i><span>Profile</span></a>
        </nav>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/7.2.0/mdb.min.js"></script>
    <script src="assets/js/main.js"></script>
</body>
</html>
