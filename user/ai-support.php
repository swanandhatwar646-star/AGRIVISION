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

$response = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $query = trim($_POST['query'] ?? '');
    
    if (!empty($query)) {
        $query_type = 'general';
        
        if (stripos($query, 'crop') !== false || stripos($query, 'plant') !== false) {
            $query_type = 'crop_health';
        } elseif (stripos($query, 'fertilizer') !== false || stripos($query, 'nutrient') !== false) {
            $query_type = 'fertilizer';
        } elseif (stripos($query, 'pest') !== false || stripos($query, 'insect') !== false) {
            $query_type = 'pest';
        } elseif (stripos($query, 'weather') !== false || stripos($query, 'rain') !== false || stripos($query, 'temperature') !== false) {
            $query_type = 'weather';
        }
        
        $ai_response = generateAIResponse($query, $query_type);
        
        $stmt = $db->query("INSERT INTO ai_queries (user_id, query, response, query_type) VALUES (?, ?, ?, ?)");
        $db->execute($stmt, [$_SESSION['user_id'], $query, $ai_response, $query_type]);
        
        $response = $ai_response;
    }
}

$stmt = $db->query("SELECT * FROM ai_queries WHERE user_id = ? ORDER BY created_at DESC LIMIT 10");
$history = $db->fetchAll($stmt, [$_SESSION['user_id']]);

function generateAIResponse($query, $type) {
    $query_lower = strtolower($query);
    
    if ($type == 'crop_health') {
        if (stripos($query, 'yellow') !== false || stripos($query, 'wilting') !== false) {
            return "Yellowing or wilting leaves can indicate several issues:\n\n1. **Water Stress**: Check soil moisture. Underwatering causes wilting, overwatering causes yellowing.\n2. **Nutrient Deficiency**: Yellow leaves may indicate nitrogen deficiency. Consider applying balanced fertilizer.\n3. **Disease**: Fungal infections can cause yellowing. Check for spots or mold.\n\n**Recommendation**: Check soil moisture first, then consider a soil test for nutrient levels.";
        } elseif (stripos($query, 'growth') !== false || stripos($query, 'slow') !== false) {
            return "Slow crop growth can be caused by:\n\n1. **Insufficient Light**: Ensure crops receive adequate sunlight.\n2. **Nutrient Deficiency**: Lack of nitrogen, phosphorus, or potassium can stunt growth.\n3. **Water Issues**: Both overwatering and underwatering can slow growth.\n4. **Soil pH**: Improper pH can block nutrient uptake.\n\n**Recommendation**: Test soil pH and nutrient levels. Adjust watering schedule accordingly.";
        }
        return "For crop health concerns, I recommend:\n\n1. Monitor your field zones regularly using the My Field section.\n2. Check the pie charts for healthy, stressed, and deficient areas.\n3. Review water requirements and pest risk levels.\n4. Consider soil testing if you notice persistent issues.\n\nWould you like more specific guidance on a particular symptom?";
    }
    
    if ($type == 'fertilizer') {
        if (stripos($query, 'nitrogen') !== false) {
            return "Nitrogen is crucial for leaf and stem growth:\n\n**Signs of Deficiency**: Yellow leaves, stunted growth.\n\n**Sources**: Urea, Ammonium sulfate, Compost.\n\n**Application**: Apply during active growth phase. Avoid over-application as it can burn plants.\n\n**Recommendation**: Use soil test results to determine exact requirements.";
        } elseif (stripos($query, 'phosphorus') !== false) {
            return "Phosphorus supports root development and flowering:\n\n**Signs of Deficiency**: Purple leaves, poor root growth.\n\n**Sources**: Rock phosphate, Bone meal, DAP.\n\n**Application**: Best applied at planting time.\n\n**Recommendation**: Mix into soil before planting for best results.";
        }
        return "For fertilizer recommendations:\n\n1. **Nitrogen (N)**: Promotes leaf and stem growth\n2. **Phosphorus (P)**: Supports root development and flowering\n3. **Potassium (K)**: Improves disease resistance and fruit quality\n\n**Best Practice**: Conduct a soil test before applying fertilizers to determine exact nutrient needs. Apply based on crop growth stage.";
    }
    
    if ($type == 'pest') {
        if (stripos($query, 'aphid') !== false) {
            return "Aphids are small sap-sucking insects:\n\n**Damage**: Yellow leaves, sticky residue, distorted growth.\n\n**Control Methods**:\n- Spray with neem oil solution\n- Introduce ladybugs (natural predators)\n- Use insecticidal soap\n- Remove heavily infested plants\n\n**Prevention**: Maintain healthy plants, avoid over-fertilizing with nitrogen.";
        } elseif (stripos($query, 'fungus') !== false || stripos($query, 'mold') !== false) {
            return "Fungal diseases require prompt attention:\n\n**Common Types**: Powdery mildew, downy mildew, root rot.\n\n**Control**:\n- Improve air circulation\n- Reduce humidity\n- Apply fungicide (Mancozeb, Carbendazim)\n- Remove infected plant parts\n- Avoid overhead watering\n\n**Prevention**: Use resistant varieties, maintain proper spacing, ensure good drainage.";
        }
        return "For pest control:\n\n**Integrated Pest Management (IPM)** approach:\n\n1. **Prevention**: Use resistant varieties, maintain crop rotation\n2. **Monitoring**: Regular field inspections\n3. **Biological Control**: Encourage beneficial insects\n4. **Chemical Control**: Use pesticides as last resort\n\nCheck the Krishi Mandi section for pesticide information and suppliers.";
    }
    
    if ($type == 'weather') {
        return "Weather considerations for farming:\n\n**Temperature**:\n- Most crops prefer 20-30°C\n- Frost can damage sensitive crops\n- Heat stress above 35°C\n\n**Rainfall**:\n- Monitor rainfall patterns\n- Plan irrigation during dry spells\n- Ensure proper drainage during heavy rains\n\n**Humidity**:\n- High humidity promotes fungal diseases\n- Low humidity increases water requirements\n\nCheck your dashboard for live weather updates and forecasts.";
    }
    
    return "I'm here to help with your farming questions! You can ask me about:\n\n🌱 **Crop Health**: Disease symptoms, growth issues, plant care\n🧪 **Fertilizers**: Nutrient requirements, application methods\n🐛 **Pests**: Pest identification, control methods, prevention\n🌤️ **Weather**: Weather impact on crops, irrigation planning\n\nPlease ask a specific question for detailed guidance.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Support - AGRIVISION</title>
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
                <a href="ai-support.php" class="active"><i class="fas fa-robot"></i> <?php echo t('ai_support'); ?></a>
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
                        <small><?php echo t('ai_support'); ?></small>
                    </div>
                </div>
                <button class="theme-toggle" title="Toggle Theme">
                    <i class="fas fa-moon"></i>
                </button>
            </div>
            
            <div class="grid-2">
                <div class="card">
                    <div class="card-header">
                        <h3><i class="fas fa-comments"></i> <?php echo t('ask_question'); ?></h3>
                    </div>
                    
                    <div id="chatContainer" style="height: 400px; overflow-y: auto; padding: 15px; background: var(--bg-light); border-radius: var(--radius); margin-bottom: 20px;">
                        <?php if ($response): ?>
                            <div style="margin-bottom: 15px;">
                                <div style="background: #e3f2fd; padding: 12px 15px; border-radius: var(--radius); display: inline-block; max-width: 80%;">
                                    <strong>You:</strong> <?php echo htmlspecialchars($_POST['query']); ?>
                                </div>
                            </div>
                            <div style="margin-bottom: 15px; text-align: right;">
                                <div style="background: var(--primary-color); color: white; padding: 12px 15px; border-radius: var(--radius); display: inline-block; max-width: 80%; text-align: left;">
                                    <strong>AI:</strong><br>
                                    <?php echo nl2br(htmlspecialchars($response)); ?>
                                </div>
                            </div>
                        <?php else: ?>
                            <div style="text-align: center; padding: 40px; color: var(--text-light);">
                                <i class="fas fa-robot fa-3x" style="margin-bottom: 20px; color: var(--primary-color);"></i>
                                <p><?php echo t('ask_question_message'); ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <form method="POST" action="">
                        <div style="display: flex; gap: 10px;">
                            <input type="text" name="query" id="queryInput" required 
                                   placeholder="Type your question here..." 
                                   style="flex: 1; padding: 12px; border: 2px solid #ddd; border-radius: var(--radius);">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane"></i> <?php echo t('send'); ?>
                            </button>
                        </div>
                    </form>
                    
                    <div style="margin-top: 15px;">
                        <p style="font-size: 0.85rem; color: var(--text-light); margin-bottom: 10px;"><strong><?php echo t('quick_questions'); ?>:</strong></p>
                        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                            <button onclick="setQuestion('My crop leaves are turning yellow')" class="btn btn-sm btn-outline">
                                🍂 Yellow leaves?
                            </button>
                            <button onclick="setQuestion('How much fertilizer should I use?')" class="btn btn-sm btn-outline">
                                🧪 Fertilizer amount?
                            </button>
                            <button onclick="setQuestion('How to control aphids?')" class="btn btn-sm btn-outline">
                                🐛 Control aphids?
                            </button>
                            <button onclick="setQuestion('Weather impact on my crops')" class="btn btn-sm btn-outline">
                                🌤️ Weather tips?
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <h3><i class="fas fa-history"></i> <?php echo t('recent_queries'); ?></h3>
                    </div>
                    <?php if (empty($history)): ?>
                        <p style="text-align: center; color: var(--text-light); padding: 30px;"><?php echo t('no_queries_yet'); ?></p>
                    <?php else: ?>
                        <div style="max-height: 500px; overflow-y: auto;">
                            <?php foreach ($history as $item): ?>
                                <div style="padding: 15px; border-bottom: 1px solid #ddd; margin-bottom: 10px;">
                                    <p style="font-weight: 500; margin-bottom: 5px;">
                                        <i class="fas fa-question-circle" style="color: var(--primary-color);"></i>
                                        <?php echo htmlspecialchars(substr($item['query'], 0, 60)); ?>...
                                    </p>
                                    <p style="font-size: 0.85rem; color: var(--text-light); margin-bottom: 5px;">
                                        <?php echo htmlspecialchars(substr($item['response'], 0, 100)); ?>...
                                    </p>
                                    <small style="color: var(--text-light);">
                                        <i class="fas fa-clock"></i> <?php echo date('M d, H:i', strtotime($item['created_at'])); ?>
                                    </small>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
    
    <script>
        function setQuestion(question) {
            document.getElementById('queryInput').value = question;
            document.getElementById('queryInput').focus();
        }
        
        const chatContainer = document.getElementById('chatContainer');
        if (chatContainer) {
            chatContainer.scrollTop = chatContainer.scrollHeight;
        }
    </script>
    
    <script src="../assets/js/main.js"></script>
</body>
</html>
