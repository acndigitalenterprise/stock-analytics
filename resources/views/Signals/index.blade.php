<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Stock Signals - Ticker AI</title>
    <link rel="stylesheet" href="{{ asset('Admin/admin.css') }}">
    <link rel="stylesheet" href="{{ asset('signals/signals.css') }}?v={{ time() }}">
    <style>
        /* TARGETED CSS - IMPROVED SIGNALS PAGE LAYOUT */
        .signals-header {
            background: rgba(255, 255, 255, 0.95) !important;
            backdrop-filter: blur(15px) !important;
            border: 1px solid rgba(255, 255, 255, 0.3) !important;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1) !important;
            border-radius: 16px !important;
            padding: 2rem !important;
            margin-bottom: 2rem !important;
        }

        .signals-title {
            color: #2d3748 !important;
            font-size: 2rem !important;
            font-weight: bold !important;
            margin-bottom: 0.5rem !important;
        }

        .signals-subtitle {
            color: #718096 !important;
            margin-bottom: 1.5rem !important;
        }

        .signals-stats-container {
            display: grid !important;
            grid-template-columns: repeat(3, 1fr) !important;
            gap: 1rem !important;
        }

        .signals-stat-card {
            background: rgba(255, 255, 255, 0.9) !important;
            backdrop-filter: blur(10px) !important;
            border: 2px solid rgba(102, 126, 234, 0.2) !important;
            border-radius: 12px !important;
            padding: 1.5rem !important;
            text-align: center !important;
            transition: all 0.3s ease !important;
        }

        .signals-stat-card:hover {
            border-color: rgba(102, 126, 234, 0.4) !important;
            transform: translateY(-2px) !important;
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.15) !important;
        }

        .signals-stat-value {
            display: block !important;
            font-size: 2rem !important;
            font-weight: bold !important;
            color: #667eea !important;
            margin-bottom: 0.5rem !important;
        }

        .signals-stat-label {
            display: block !important;
            font-size: 0.875rem !important;
            color: #718096 !important;
            font-weight: 500 !important;
        }

        .signals-filter-bar {
            background: rgba(255, 255, 255, 0.95) !important;
            backdrop-filter: blur(15px) !important;
            border: 1px solid rgba(255, 255, 255, 0.3) !important;
            border-radius: 16px !important;
            padding: 1.5rem !important;
            margin-bottom: 2rem !important;
            display: flex !important;
            align-items: center !important;
            gap: 2rem !important;
            flex-wrap: wrap !important;
        }

        .signals-filter-group {
            display: flex !important;
            align-items: center !important;
            gap: 0.5rem !important;
        }

        .signals-filter-group label {
            font-weight: 600 !important;
            color: #4a5568 !important;
            font-size: 0.875rem !important;
        }

        .signals-filter-select {
            padding: 0.5rem 1rem !important;
            border: 1px solid #cbd5e0 !important;
            border-radius: 8px !important;
            background: white !important;
            color: #2d3748 !important;
            font-size: 0.875rem !important;
        }

        .signals-refresh-btn {
            background: #48bb78 !important;
            color: white !important;
            border: none !important;
            padding: 0.75rem 1.5rem !important;
            border-radius: 8px !important;
            cursor: pointer !important;
            font-size: 0.875rem !important;
            display: flex !important;
            align-items: center !important;
            gap: 0.5rem !important;
            transition: all 0.2s !important;
        }

        .signals-refresh-btn:hover {
            background: #38a169 !important;
            transform: translateY(-1px) !important;
        }

        .signals-last-update {
            color: #718096 !important;
            font-size: 0.875rem !important;
            margin-left: auto !important;
        }

        .signals-no-data {
            background: rgba(255, 255, 255, 0.95) !important;
            backdrop-filter: blur(15px) !important;
            border: 1px solid rgba(255, 255, 255, 0.3) !important;
            border-radius: 16px !important;
            padding: 3rem !important;
            text-align: center !important;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1) !important;
        }

        .signals-no-data-icon {
            font-size: 4rem !important;
            margin-bottom: 1rem !important;
        }

        .signals-no-data h3 {
            color: #2d3748 !important;
            margin: 0 0 1rem 0 !important;
            font-size: 1.5rem !important;
        }

        .signals-no-data p {
            color: #718096 !important;
            margin: 0 !important;
            font-size: 1rem !important;
        }

        .signals-table {
            background: rgba(255, 255, 255, 0.95) !important;
            backdrop-filter: blur(15px) !important;
            border-radius: 16px !important;
            overflow: hidden !important;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1) !important;
        }
    </style>
</head>
<body class="admin-layout">

<!-- DESKTOP ADMIN LAYOUT -->
<div class="admin-layout-container">
    @include('Components.header')

    <!-- Mobile Sidebar Overlay -->
    <div class="mobile-sidebar-overlay" onclick="closeMobileMenu()"></div>

    <!-- Main Content Area -->
    <div class="admin-main-content">
        @include('Components.sidebar')

        <!-- Admin Body Content -->
        <main class="admin-body">
            <div class="admin-content-container">

    <div class="signals-header">
        <div>
            <h2 class="signals-title">Stock Signals</h2>
            <p class="signals-subtitle">AI-powered trading opportunities from top 50 IDX stocks</p>
        </div>
        <div class="signals-stats-container">
            <div class="signals-stat-card">
                <span class="signals-stat-value" id="total-signals">-</span>
                <span class="signals-stat-label">Active Signals</span>
            </div>
            <div class="signals-stat-card">
                <span class="signals-stat-value" id="win-rate">-</span>
                <span class="signals-stat-label">Win Rate</span>
            </div>
            <div class="signals-stat-card">
                <span class="signals-stat-value" id="signals-today">-</span>
                <span class="signals-stat-label">Today</span>
            </div>
        </div>
    </div>

    <!-- Filter Controls -->
    <div class="signals-filter-bar">
        <div class="signals-filter-group">
            <label for="timeframe-filter">Timeframe:</label>
            <select id="timeframe-filter" class="signals-filter-select">
                <option value="all">All</option>
                <option value="1h">1 Hour</option>
                <option value="1d">1 Day</option>
            </select>
        </div>
        <div class="signals-filter-group">
            <label for="confidence-filter">Min Confidence:</label>
            <select id="confidence-filter" class="signals-filter-select">
                <option value="70">70%+</option>
                <option value="75">75%+</option>
                <option value="80">80%+</option>
            </select>
        </div>
        <div class="signals-filter-group">
            <button id="refresh-signals" class="btn signals-refresh-btn">
                <span class="refresh-icon">🔄</span> Refresh
            </button>
        </div>
        <div class="signals-filter-group">
            <span class="signals-last-update">Last updated: <span id="last-update-time">-</span></span>
        </div>
    </div>

    <!-- Signals Container -->
    <div id="signals-container">
        <!-- Desktop Table View -->
        <div class="signals-desktop-view">
            <div class="table-responsive">
                <table class="signals-table">
                    <thead>
                        <tr>
                            <th>Stock</th>
                            <th>TF</th>
                            <th>Current Price</th>
                            <th>Entry</th>
                            <th>Target 1</th>
                            <th>Target 2</th>
                            <th>Stop Loss</th>
                            <th>R:R</th>
                            <th>Confidence</th>
                            <th>Score</th>
                            <th>Expires</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="signals-table-body">
                        <tr>
                            <td colspan="12" class="signals-loading">
                                <div class="signals-loading-spinner"></div>
                                Loading signals...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Mobile Cards View -->
        <div class="signals-mobile-view">
            <div id="signals-cards-container">
                <div class="signals-loading-card">
                    <div class="signals-loading-spinner"></div>
                    Loading signals...
                </div>
            </div>
        </div>
    </div>

    <!-- No Signals Message -->
    <div id="no-signals-message" class="signals-no-data" style="display: none;">
        <div class="signals-no-data-icon">📊</div>
        <h3>No signals available</h3>
        <p>We're constantly scanning the market for opportunities. Check back in a few minutes!</p>
    </div>

    <!-- Signal Detail Modal -->
    <div id="signal-detail-modal" class="signals-modal" style="display: none;">
        <div class="signals-modal-content">
            <div class="signals-modal-header">
                <h3 id="modal-stock-title">Stock Signal Details</h3>
                <span class="signals-modal-close" onclick="closeSignalModal()">&times;</span>
            </div>
            <div class="signals-modal-body" id="modal-signal-details">
                <!-- Signal details will be loaded here -->
            </div>
        </div>
    </div>

            </div>
        </main>
    </div>
</div>

@include('Components.admin-scripts')

<script>
// Signals management
let signalsData = [];
let lastUpdateTime = null;

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    loadSignals();
    loadStats();

    // Auto refresh every 5 minutes
    setInterval(loadSignals, 5 * 60 * 1000);
    setInterval(loadStats, 60 * 1000);

    // Filter event listeners
    document.getElementById('timeframe-filter').addEventListener('change', loadSignals);
    document.getElementById('confidence-filter').addEventListener('change', loadSignals);
    document.getElementById('refresh-signals').addEventListener('click', function() {
        this.classList.add('signals-refreshing');
        loadSignals().finally(() => {
            this.classList.remove('signals-refreshing');
        });
    });
});

// Load signals data
async function loadSignals() {
    try {
        const timeframe = document.getElementById('timeframe-filter').value;
        const confidence = document.getElementById('confidence-filter').value;

        const response = await fetch(`/api/signals?timeframe=${timeframe}&confidence=${confidence}`, {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });

        if (!response.ok) throw new Error('Failed to load signals');

        const data = await response.json();
        signalsData = data.signals || [];

        renderSignals();
        updateLastUpdateTime();

    } catch (error) {
        console.error('Error loading signals:', error);
        showError('Failed to load signals. Please try again.');
    }
}

// Load statistics
async function loadStats() {
    try {
        const response = await fetch('/api/signals/stats', {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });

        if (!response.ok) throw new Error('Failed to load stats');

        const stats = await response.json();

        document.getElementById('total-signals').textContent = stats.total_active || 0;
        document.getElementById('win-rate').textContent = stats.win_rate_this_week ? `${stats.win_rate_this_week}%` : 'N/A';
        document.getElementById('signals-today').textContent = stats.signals_today || 0;

    } catch (error) {
        console.error('Error loading stats:', error);
    }
}

// Render signals in both desktop and mobile views
function renderSignals() {
    const tableBody = document.getElementById('signals-table-body');
    const cardsContainer = document.getElementById('signals-cards-container');
    const noSignalsMessage = document.getElementById('no-signals-message');
    const signalsContainer = document.getElementById('signals-container');

    if (signalsData.length === 0) {
        signalsContainer.style.display = 'none';
        noSignalsMessage.style.display = 'block';
        return;
    }

    signalsContainer.style.display = 'block';
    noSignalsMessage.style.display = 'none';

    // Desktop table view
    tableBody.innerHTML = signalsData.map(signal => `
        <tr class="signals-row" onclick="showSignalDetails(${signal.id})">
            <td>
                <div class="signals-stock-info">
                    <strong>${signal.stock_code.replace('.JK', '')}</strong>
                    <small>${signal.company_name || ''}</small>
                </div>
            </td>
            <td><span class="signals-timeframe signals-timeframe-${signal.timeframe}">${signal.timeframe}</span></td>
            <td>${signal.current_price}</td>
            <td>${signal.entry_price}</td>
            <td class="signals-target">${signal.target_1} <small>(+${signal.potential_profit_target_1}%)</small></td>
            <td class="signals-target">${signal.target_2} <small>(+${signal.potential_profit_target_2}%)</small></td>
            <td class="signals-stop-loss">${signal.stop_loss} <small>(-${signal.potential_loss}%)</small></td>
            <td>${signal.risk_reward}</td>
            <td>
                <div class="signals-confidence">
                    <span class="signals-confidence-badge signals-confidence-${signal.confidence_level.toLowerCase()}">${signal.confidence_level}</span>
                    <small>${signal.confidence_percentage}%</small>
                </div>
            </td>
            <td><span class="signals-score">${signal.scalping_score}/10</span></td>
            <td class="signals-expires">${formatExpiryTime(signal.expires_at)}</td>
            <td>
                <button class="btn signals-view-btn" onclick="event.stopPropagation(); showSignalDetails(${signal.id})">
                    View
                </button>
            </td>
        </tr>
    `).join('');

    // Mobile cards view
    cardsContainer.innerHTML = signalsData.map(signal => `
        <div class="signals-card" onclick="showSignalDetails(${signal.id})">
            <div class="signals-card-header">
                <div class="signals-card-stock">
                    <strong>${signal.stock_code.replace('.JK', '')}</strong>
                    <span class="signals-timeframe signals-timeframe-${signal.timeframe}">${signal.timeframe}</span>
                </div>
                <div class="signals-card-confidence">
                    <span class="signals-confidence-badge signals-confidence-${signal.confidence_level.toLowerCase()}">${signal.confidence_level}</span>
                    <small>${signal.confidence_percentage}%</small>
                </div>
            </div>
            <div class="signals-card-body">
                <div class="signals-card-row">
                    <span>Current Price:</span>
                    <strong>${signal.current_price}</strong>
                </div>
                <div class="signals-card-row">
                    <span>Entry:</span>
                    <strong>${signal.entry_price}</strong>
                </div>
                <div class="signals-card-row">
                    <span>Target 1:</span>
                    <strong class="signals-target">${signal.target_1} (+${signal.potential_profit_target_1}%)</strong>
                </div>
                <div class="signals-card-row">
                    <span>Target 2:</span>
                    <strong class="signals-target">${signal.target_2} (+${signal.potential_profit_target_2}%)</strong>
                </div>
                <div class="signals-card-row">
                    <span>Stop Loss:</span>
                    <strong class="signals-stop-loss">${signal.stop_loss} (-${signal.potential_loss}%)</strong>
                </div>
                <div class="signals-card-row">
                    <span>Risk:Reward:</span>
                    <strong>${signal.risk_reward}</strong>
                </div>
                <div class="signals-card-row">
                    <span>Score:</span>
                    <strong>${signal.scalping_score}/10</strong>
                </div>
            </div>
            <div class="signals-card-footer">
                <small>Expires: ${formatExpiryTime(signal.expires_at)}</small>
                <button class="btn signals-view-btn" onclick="event.stopPropagation(); showSignalDetails(${signal.id})">
                    View Details
                </button>
            </div>
        </div>
    `).join('');
}

// Show signal details modal
async function showSignalDetails(signalId) {
    try {
        const signal = signalsData.find(s => s.id === signalId);
        if (!signal) return;

        document.getElementById('modal-stock-title').textContent = `${signal.stock_code.replace('.JK', '')} - ${signal.company_name || 'Stock Signal'}`;

        const modalBody = document.getElementById('modal-signal-details');
        modalBody.innerHTML = `
            <div class="signals-detail-grid">
                <div class="signals-detail-section">
                    <h4>Price Information</h4>
                    <div class="signals-detail-row">
                        <span>Current Price:</span>
                        <strong>${signal.current_price}</strong>
                    </div>
                    <div class="signals-detail-row">
                        <span>Entry Price:</span>
                        <strong>${signal.entry_price}</strong>
                    </div>
                    <div class="signals-detail-row">
                        <span>Target 1:</span>
                        <strong class="signals-target">${signal.target_1} (~${signal.potential_profit_target_1}%)</strong>
                    </div>
                    <div class="signals-detail-row">
                        <span>Target 2:</span>
                        <strong class="signals-target">${signal.target_2} (~${signal.potential_profit_target_2}%)</strong>
                    </div>
                    <div class="signals-detail-row">
                        <span>Stop Loss:</span>
                        <strong class="signals-stop-loss">${signal.stop_loss} (~${signal.potential_loss}%)</strong>
                    </div>
                </div>

                <div class="signals-detail-section">
                    <h4>Technical Analysis</h4>
                    <div class="signals-detail-row">
                        <span>RSI:</span>
                        <strong>${signal.rsi || 'N/A'}</strong>
                    </div>
                    <div class="signals-detail-row">
                        <span>MACD Signal:</span>
                        <strong>${signal.macd_signal || 'N/A'}</strong>
                    </div>
                    <div class="signals-detail-row">
                        <span>Volume:</span>
                        <strong>${signal.volume || 'N/A'}</strong>
                    </div>
                    <div class="signals-detail-row">
                        <span>Scalping Score:</span>
                        <strong>${signal.scalping_score}/10</strong>
                    </div>
                </div>

                <div class="signals-detail-section">
                    <h4>Signal Information</h4>
                    <div class="signals-detail-row">
                        <span>Timeframe:</span>
                        <strong>${signal.timeframe}</strong>
                    </div>
                    <div class="signals-detail-row">
                        <span>Confidence Level:</span>
                        <strong>${signal.confidence_level} (${signal.confidence_percentage}%)</strong>
                    </div>
                    <div class="signals-detail-row">
                        <span>Risk:Reward:</span>
                        <strong>${signal.risk_reward}</strong>
                    </div>
                    <div class="signals-detail-row">
                        <span>Expires:</span>
                        <strong>${formatExpiryTime(signal.expires_at)}</strong>
                    </div>
                </div>
            </div>

            <div class="signals-detail-reason">
                <h4>Analysis Reason</h4>
                <p>${signal.analysis_reason}</p>
            </div>
        `;

        document.getElementById('signal-detail-modal').style.display = 'block';

    } catch (error) {
        console.error('Error loading signal details:', error);
        showError('Failed to load signal details.');
    }
}

// Close signal modal
function closeSignalModal() {
    document.getElementById('signal-detail-modal').style.display = 'none';
}

// Format expiry time
function formatExpiryTime(expiryTime) {
    const expiry = new Date(expiryTime);
    const now = new Date();
    const diffMs = expiry - now;

    if (diffMs < 0) {
        return 'Expired';
    }

    const diffHours = Math.floor(diffMs / (1000 * 60 * 60));
    const diffMins = Math.floor((diffMs % (1000 * 60 * 60)) / (1000 * 60));

    if (diffHours > 0) {
        return `${diffHours}h ${diffMins}m`;
    } else {
        return `${diffMins}m`;
    }
}

// Update last update time
function updateLastUpdateTime() {
    const now = new Date();
    document.getElementById('last-update-time').textContent = now.toLocaleTimeString();
    lastUpdateTime = now;
}

// Show error message
function showError(message) {
    // Simple error display - you can enhance this
    console.error(message);
    alert(message);
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('signal-detail-modal');
    if (event.target === modal) {
        closeSignalModal();
    }
}
</script>

</body>
</html>