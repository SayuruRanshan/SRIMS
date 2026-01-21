<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit();
}

require_once '../config/db.php';

$database = new Database();
$db = $database->getConnection();

// Get products with live stock and low stock alerts
$products = $db->query(
    "SELECT p.*, pg.group_name 
     FROM products p 
     LEFT JOIN product_groups pg ON p.group_id = pg.id 
     ORDER BY 
     CASE WHEN p.current_stock <= p.min_stock THEN 1 ELSE 2 END,
     p.current_stock ASC,
     p.product_name"
)->fetchAll();

// Get company name
$company = $db->query("SELECT company_name FROM settings LIMIT 1")->fetch(PDO::FETCH_ASSOC);
$company_name = $company['company_name'] ?? 'SRIMS';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Warehouse Display - <?php echo htmlspecialchars($company_name); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { 
            background: #000; 
            color: #fff; 
            font-family: 'Courier New', monospace;
            margin: 0;
            padding: 0;
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 15px;
            text-align: center;
            box-shadow: 0 4px 10px rgba(0,0,0,0.5);
            position: relative;
        }
        .header h1 {
            margin: 0;
            font-size: 2.5rem;
            font-weight: bold;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
        }
        .datetime {
            font-size: 1.2rem;
            margin-top: 10px;
        }
        .stats-bar {
            background: rgba(255,255,255,0.1);
            padding: 15px;
            display: flex;
            justify-content: space-around;
            margin: 10px 20px;
            border-radius: 10px;
        }
        .stat-item {
            text-align: center;
        }
        .stat-value {
            font-size: 2rem;
            font-weight: bold;
            display: block;
        }
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            padding: 20px;
            animation: slideUp 1s ease-in;
        }
        .product-card {
            background: rgba(255,255,255,0.05);
            border: 2px solid rgba(255,255,255,0.1);
            border-radius: 15px;
            padding: 20px;
            text-align: center;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        .product-card:hover {
            transform: translateY(-5px);
            border-color: rgba(255,255,255,0.3);
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }
        .product-card.critical {
            background: rgba(220, 53, 69, 0.2);
            border-color: #dc3545;
            animation: pulse 2s infinite;
        }
        .product-card.low {
            background: rgba(255, 193, 7, 0.2);
            border-color: #ffc107;
        }
        .product-card.normal {
            background: rgba(40, 167, 69, 0.1);
            border-color: #28a745;
        }
        .product-name {
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .stock-badge {
            font-size: 3rem;
            font-weight: bold;
            display: block;
            margin: 10px 0;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
        }
        .stock-label {
            font-size: 0.9rem;
            opacity: 0.8;
        }
        .alert-indicator {
            position: absolute;
            top: 10px;
            right: 10px;
            width: 15px;
            height: 15px;
            border-radius: 50%;
        }
        .critical-indicator {
            background: #dc3545;
            animation: blink 1s infinite;
        }
        .low-indicator {
            background: #ffc107;
        }
        .normal-indicator {
            background: #28a745;
        }
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
        @keyframes blink {
            0% { opacity: 1; }
            50% { opacity: 0.5; }
            100% { opacity: 1; }
        }
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(0,0,0,0.8);
            padding: 10px;
            text-align: center;
            font-size: 0.9rem;
        }
        .auto-refresh {
            color: #28a745;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <!-- Auto-refresh every 10 seconds -->
    <meta http-equiv="refresh" content="10">
    
    <div class="header">
        <h1><i class="fas fa-warehouse me-3"></i><?php echo htmlspecialchars($company_name); ?></h1>
        <div class="datetime">
            <i class="fas fa-clock me-2"></i><span id="datetime"></span>
        </div>
    </div>
    
    <div class="stats-bar">
        <div class="stat-item">
            <span class="stat-value text-danger"><?php echo count(array_filter($products, fn($p) => $p['current_stock'] <= $p['min_stock'])); ?></span>
            <span>CRITICAL</span>
        </div>
        <div class="stat-item">
            <span class="stat-value text-warning"><?php echo count($products); ?></span>
            <span>TOTAL ITEMS</span>
        </div>
        <div class="stat-item">
            <span class="stat-value text-info"><?php echo array_sum(array_column($products, 'current_stock')); ?></span>
            <span>TOTAL STOCK</span>
        </div>
        <div class="stat-item">
            <span class="stat-value text-success">
                <i class="fas fa-sync-alt"></i> 
                Auto Refresh
            </span>
            <span>10 sec</span>
        </div>
    </div>
    
    <div class="products-grid">
        <?php foreach ($products as $product): ?>
            <?php
            $status = 'normal';
            $indicator = 'normal-indicator';
            if ($product['current_stock'] <= 5) {
                $status = 'critical';
                $indicator = 'critical-indicator';
            } elseif ($product['current_stock'] <= $product['min_stock']) {
                $status = 'low';
                $indicator = 'low-indicator';
            }
            ?>
            <div class="product-card <?php echo $status; ?>">
                <div class="alert-indicator <?php echo $indicator; ?>"></div>
                <div class="product-name"><?php echo htmlspecialchars($product['product_name']); ?></div>
                <div class="stock-label"><?php echo htmlspecialchars($product['group_name'] ?? 'Uncategorized'); ?></div>
                <div class="stock-badge">
                    <?php echo $product['current_stock']; ?>
                </div>
                <div class="stock-label">
                    Min: <?php echo $product['min_stock']; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    
    <div class="footer">
        <div class="auto-refresh">
            <i class="fas fa-sync-alt me-2"></i>
            Auto-refresh in <span id="countdown">10</span> seconds
        </div>
        <div>
            <?php echo date('Y'); ?> <?php echo htmlspecialchars($company_name); ?> - Warehouse Display System
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Update datetime
        function updateDateTime() {
            const now = new Date();
            document.getElementById('datetime').textContent = 
                now.toLocaleDateString() + ' ' + now.toLocaleTimeString();
        }
        
        // Countdown timer
        let seconds = 10;
        function updateCountdown() {
            document.getElementById('countdown').textContent = seconds;
            seconds--;
            if (seconds < 0) {
                seconds = 10;
            }
        }
        
        updateDateTime();
        setInterval(updateDateTime, 1000);
        setInterval(updateCountdown, 1000);
        
        // Full screen mode on spacebar
        document.addEventListener('keydown', function(e) {
            if (e.code === 'Space' && e.target === document.body) {
                e.preventDefault();
                if (!document.fullscreenElement) {
                    document.documentElement.requestFullscreen();
                } else {
                    document.exitFullscreen();
                }
            }
        });
    </script>
</body>
</html>