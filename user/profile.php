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

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $state = $_POST['state'] ?? '';
    $district = $_POST['district'] ?? '';
    $region = trim($_POST['region'] ?? '');
    $language = $_POST['language'] ?? 'en';
    
    if (empty($name)) {
        $error = 'Name is required.';
    } else {
        $stmt = $db->query("UPDATE users SET name = ?, address = ?, state = ?, district = ?, region = ?, language = ? WHERE id = ?");
        if ($db->execute($stmt, [$name, $address, $state, $district, $region, $language, $_SESSION['user_id']])) {
            $_SESSION['user_name'] = $name;
            $_SESSION['user_language'] = $language;
            $success = 'Profile updated successfully!';
            
            $stmt = $db->query("SELECT * FROM users WHERE id = ?");
            $user = $db->fetch($stmt, [$_SESSION['user_id']]);
        } else {
            $error = 'Failed to update profile. Please try again.';
        }
    }
}

$states = ['Maharashtra', 'Karnataka', 'Gujarat', 'Madhya Pradesh', 'Rajasthan', 'Uttar Pradesh', 'Punjab', 'Haryana', 'Tamil Nadu', 'Andhra Pradesh', 'Kerala', 'West Bengal', 'Bihar', 'Odisha', 'Assam'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - AGRIVISION</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="dashboard-container">
        <aside class="sidebar">
            <div class="sidebar-header">
                <h3><i class="fas fa-leaf"></i> AGRIVISION</h3>
            </div>
            <nav class="sidebar-menu">
                <a href="dashboard.php"><i class="fas fa-home"></i> <?php echo t('dashboard'); ?></a>
                <a href="my-field.php"><i class="fas fa-seedling"></i> <?php echo t('my_field'); ?></a>
                <a href="krishi-mandi.php"><i class="fas fa-store"></i> <?php echo t('krishi_mandi'); ?></a>
                <a href="my-greenhouse.php"><i class="fas fa-warehouse"></i> <?php echo t('my_greenhouse'); ?></a>
                <a href="ai-support.php"><i class="fas fa-robot"></i> <?php echo t('ai_support'); ?></a>
                <a href="appointments.php"><i class="fas fa-calendar"></i> <?php echo t('appointments'); ?></a>
                <a href="profile.php" class="active"><i class="fas fa-user"></i> <?php echo t('profile'); ?></a>
                <a href="logout.php"><i class="fas fa-sign-out-alt"></i> <?php echo t('logout'); ?></a>
            </nav>
        </aside>
        
        <main class="main-content">
            <div class="top-bar">
                <div class="user-info">
                    <div class="user-avatar"><?php echo strtoupper(substr($user['name'], 0, 1)); ?></div>
                    <div>
                        <h4><?php echo htmlspecialchars($user['name']); ?></h4>
                        <small>My Profile</small>
                    </div>
                </div>
                <button class="theme-toggle" title="Toggle Theme">
                    <i class="fas fa-moon"></i>
                </button>
            </div>
            
            <div class="grid-2">
                <div class="card">
                    <div class="card-header">
                        <h3><i class="fas fa-user-edit"></i> <?php echo t('edit_profile'); ?></h3>
                    </div>
                    
                    <?php if ($error): ?>
                        <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
                    <?php endif; ?>
                    
                    <?php if ($success): ?>
                        <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
                    <?php endif; ?>
                    
                    <form method="POST" action="">
                        <div class="form-group">
                            <label for="name"><?php echo t('name'); ?> *</label>
                            <input type="text" id="name" name="name" required 
                                   value="<?php echo htmlspecialchars($user['name']); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="mobile"><?php echo t('mobile_number'); ?></label>
                            <input type="tel" id="mobile" name="mobile" readonly 
                                   value="<?php echo htmlspecialchars($user['mobile']); ?>"
                                   style="background: var(--bg-light); cursor: not-allowed;">
                            <small style="color: var(--text-light);">Mobile number cannot be changed.</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="address"><?php echo t('address'); ?></label>
                            <textarea id="address" name="address" rows="3"><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label for="state"><?php echo t('state'); ?></label>
                            <select id="state" name="state">
                                <option value="">Select State</option>
                                <?php foreach ($states as $s): ?>
                                    <option value="<?php echo $s; ?>" <?php echo ($user['state'] ?? '') == $s ? 'selected' : ''; ?>><?php echo $s; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="district"><?php echo t('district'); ?></label>
                            <input type="text" id="district" name="district" 
                                   value="<?php echo htmlspecialchars($user['district'] ?? ''); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="region"><?php echo t('region'); ?></label>
                            <input type="text" id="region" name="region" 
                                   value="<?php echo htmlspecialchars($user['region'] ?? ''); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="language"><?php echo t('preferred_language'); ?></label>
                            <select id="language" name="language">
                                <option value="en" <?php echo ($user['language'] ?? 'en') == 'en' ? 'selected' : ''; ?>>English</option>
                                <option value="hi" <?php echo ($user['language'] ?? '') == 'hi' ? 'selected' : ''; ?>>हिंदी (Hindi)</option>
                                <option value="ta" <?php echo ($user['language'] ?? '') == 'ta' ? 'selected' : ''; ?>>தமிழ் (Tamil)</option>
                                <option value="te" <?php echo ($user['language'] ?? '') == 'te' ? 'selected' : ''; ?>>తెలుగు (Telugu)</option>
                                <option value="kn" <?php echo ($user['language'] ?? '') == 'kn' ? 'selected' : ''; ?>>ಕನ್ನಡ (Kannada)</option>
                                <option value="mr" <?php echo ($user['language'] ?? '') == 'mr' ? 'selected' : ''; ?>>मराठी (Marathi)</option>
                                <option value="bn" <?php echo ($user['language'] ?? '') == 'bn' ? 'selected' : ''; ?>>বাংলা (Bengali)</option>
                                <option value="gu" <?php echo ($user['language'] ?? '') == 'gu' ? 'selected' : ''; ?>>ગુજરાતી (Gujarati)</option>
                                <option value="pa" <?php echo ($user['language'] ?? '') == 'pa' ? 'selected' : ''; ?>>ਪੰਜਾਬੀ (Punjabi)</option>
                                <option value="ml" <?php echo ($user['language'] ?? '') == 'ml' ? 'selected' : ''; ?>>മലയാളം (Malayalam)</option>
                            </select>
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-block"><?php echo t('save_changes'); ?></button>
                    </form>
                </div>
                
                <div>
                    <div class="card">
                        <div class="card-header">
                            <h3><i class="fas fa-info-circle"></i> <?php echo t('account_info'); ?></h3>
                        </div>
                        <div style="text-align: center; padding: 30px;">
                            <div class="user-avatar" style="width: 80px; height: 80px; font-size: 2rem; margin: 0 auto 20px;">
                                <?php echo strtoupper(substr($user['name'], 0, 1)); ?>
                            </div>
                            <h3 style="margin-bottom: 5px;"><?php echo htmlspecialchars($user['name']); ?></h3>
                            <p style="color: var(--text-light); margin-bottom: 20px;">
                                <i class="fas fa-phone"></i> <?php echo htmlspecialchars($user['mobile']); ?>
                            </p>
                            <div style="text-align: left; padding: 20px; background: var(--bg-light); border-radius: var(--radius);">
                                <p style="margin: 10px 0;"><strong>Role:</strong> Farmer</p>
                                <p style="margin: 10px 0;"><strong>Status:</strong> <?php echo t($user['status']); ?></p>
                                <p style="margin: 10px 0;"><strong>Language:</strong> <?php echo ucfirst($user['language'] ?? 'English'); ?></p>
                                <p style="margin: 10px 0;"><strong>Member Since:</strong> <?php echo date('M d, Y', strtotime($user['created_at'])); ?></p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card">
                        <div class="card-header">
                            <h3><i class="fas fa-headset"></i> Contact Support</h3>
                        </div>
                        <div style="padding: 20px;">
                            <p style="margin-bottom: 15px;">Need help? Contact our support team:</p>
                            <p style="margin: 10px 0;">
                                <i class="fas fa-envelope"></i> 
                                <strong>Email:</strong> support@agrivision.com
                            </p>
                            <p style="margin: 10px 0;">
                                <i class="fas fa-phone"></i> 
                                <strong>Phone:</strong> 1800-123-4567 (Toll Free)
                            </p>
                            <div style="margin-top: 20px;">
                                <p style="margin-bottom: 10px;"><strong>Follow Us:</strong></p>
                                <div style="display: flex; gap: 10px;">
                                    <a href="#" style="color: #1877f2; font-size: 1.5rem;"><i class="fab fa-facebook"></i></a>
                                    <a href="#" style="color: #1da1f2; font-size: 1.5rem;"><i class="fab fa-twitter"></i></a>
                                    <a href="#" style="color: #c32aa3; font-size: 1.5rem;"><i class="fab fa-instagram"></i></a>
                                    <a href="#" style="color: #ff0000; font-size: 1.5rem;"><i class="fab fa-youtube"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    <script src="../assets/js/main.js"></script>
</body>
</html>
