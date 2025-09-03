// Bot Dashboard JavaScript
class BotDashboard {
    constructor() {
        this.bots = {};
        this.activityCount = 0;
        this.init();
    }

    init() {
        console.log('Initializing Bot Dashboard...');

        // Set up event listeners
        this.setupEventListeners();

        // Initialize bot states  
        this.initializeBotStates();

        // Update dashboard every second
        setInterval(() => {
            this.updateDashboard();
        }, 1000);
    }

    setupEventListeners() {
        // Start individual bot buttons
        $(document).on('click', '.start-bot-btn', (e) => {
            const botId = $(e.target).closest('button').data('bot-id');
            this.startBot(botId);
        });

        // Stop individual bot buttons
        $(document).on('click', '.stop-bot-btn', (e) => {
            const botId = $(e.target).closest('button').data('bot-id');
            this.stopBot(botId);
        });

        // Start all bots
        $('#startAllBots').on('click', () => {
            this.startAllBots();
        });

        // Stop all bots
        $('#stopAllBots').on('click', () => {
            this.stopAllBots();
        });

        // Interval change
        $(document).on('change', '.bot-interval', (e) => {
            const botId = $(e.target).data('bot-id');
            const newInterval = parseInt($(e.target).val());
            if (this.bots[botId]) {
                this.bots[botId].interval = newInterval * 1000; // Convert to milliseconds
                if (this.bots[botId].active) {
                    this.stopBot(botId);
                    setTimeout(() => this.startBot(botId), 500);
                }
            }
        });
    }

    initializeBotStates() {
        // Bot states will be initialized from the page data
        if (typeof window.botConfigs !== 'undefined') {
            for (const [botId, bot] of Object.entries(window.botConfigs)) {
                this.bots[botId] = {
                    id: botId,
                    name: bot.name,
                    active: false,
                    interval: bot.interval * 1000,
                    startTime: null,
                    actionCount: 0,
                    timer: null
                };
            }
        }
    }

    startBot(botId) {
        console.log('Starting bot:', botId);

        if (!this.bots[botId]) {
            console.error('Bot not found:', botId);
            return;
        }

        // Send start request to server
        $.ajax({
            url: window.URLROOT + '/bot/startBot',
            method: 'POST',
            data: { bot_id: botId },
            success: (response) => {
                if (response.success) {
                    this.bots[botId].active = true;
                    this.bots[botId].startTime = Date.now();

                    // Update UI
                    this.updateBotStatus(botId, 'active');

                    // Start bot execution loop
                    this.startBotLoop(botId);

                    this.addActivityLog(botId, 'Bot started successfully', 'success');
                    this.addGlobalActivity(`${this.bots[botId].name} started`, 'success');
                } else {
                    this.addActivityLog(botId, 'Failed to start: ' + response.message, 'error');
                }
            },
            error: (xhr, status, error) => {
                console.error('Start bot error:', error);
                this.addActivityLog(botId, 'Failed to start bot', 'error');
            }
        });
    }

    stopBot(botId) {
        console.log('Stopping bot:', botId);

        if (!this.bots[botId]) {
            console.error('Bot not found:', botId);
            return;
        }

        // Send stop request to server
        $.ajax({
            url: window.URLROOT + '/bot/stopBot',
            method: 'POST',
            data: { bot_id: botId },
            success: (response) => {
                if (response.success) {
                    this.bots[botId].active = false;

                    // Clear timer
                    if (this.bots[botId].timer) {
                        clearTimeout(this.bots[botId].timer);
                        this.bots[botId].timer = null;
                    }

                    // Update UI
                    this.updateBotStatus(botId, 'inactive');

                    this.addActivityLog(botId, 'Bot stopped', 'info');
                    this.addGlobalActivity(`${this.bots[botId].name} stopped`, 'info');
                } else {
                    this.addActivityLog(botId, 'Failed to stop: ' + response.message, 'error');
                }
            },
            error: (xhr, status, error) => {
                console.error('Stop bot error:', error);
                this.addActivityLog(botId, 'Failed to stop bot', 'error');
            }
        });
    }

    startBotLoop(botId) {
        if (!this.bots[botId] || !this.bots[botId].active) {
            return;
        }

        const executeAction = () => {
            if (!this.bots[botId].active) {
                return;
            }

            $.ajax({
                url: window.URLROOT + '/bot/executeAction',
                method: 'POST',
                data: { bot_id: botId },
                success: (response) => {
                    if (response.success) {
                        this.bots[botId].actionCount++;

                        // Update action count display
                        $(`#actions-${botId}`).text(this.bots[botId].actionCount);

                        // Add activity log
                        this.addActivityLog(botId, response.message, 'success', response.details);
                        this.addGlobalActivity(`${this.bots[botId].name}: ${response.message}`, 'success', response);

                        // Update pricing logic panel if this is pricing bot
                        if (botId === 'pricing_bot' && response.pricing_logic) {
                            this.updatePricingLogicPanel(response.pricing_logic);
                        }

                        // Animate progress bar
                        this.animateProgress(botId);
                    } else {
                        this.addActivityLog(botId, response.message, 'error');
                    }
                },
                error: (xhr, status, error) => {
                    console.error('Execute action error:', error, xhr.status, xhr.responseText);
                    // If unauthorized or forbidden, stop the bot and show clear message
                    if (xhr.status === 401) {
                        this.addActivityLog(botId, 'Authentication required. Please log in.', 'error');
                        this.stopBot(botId);
                        this.addGlobalActivity('Bot stopped: authentication required', 'error');
                        return;
                    }

                    if (xhr.status === 403) {
                        this.addActivityLog(botId, 'Permission denied. Contact admin.', 'error');
                        this.stopBot(botId);
                        this.addGlobalActivity('Bot stopped: permission denied', 'error');
                        return;
                    }

                    this.addActivityLog(botId, 'Action execution failed', 'error');
                },
                complete: () => {
                    // Schedule next execution
                    if (this.bots[botId].active) {
                        this.bots[botId].timer = setTimeout(executeAction, this.bots[botId].interval);
                    }
                }
            });
        };

        // Start the first execution
        executeAction();
    }

    updateBotStatus(botId, status) {
        const statusBadge = $(`#status-${botId}`);
        const startBtn = $(`.start-bot-btn[data-bot-id="${botId}"]`);
        const stopBtn = $(`.stop-bot-btn[data-bot-id="${botId}"]`);

        if (status === 'active') {
            statusBadge.removeClass('badge-secondary').addClass('badge-success').text('Active');
            startBtn.prop('disabled', true);
            stopBtn.prop('disabled', false);
        } else {
            statusBadge.removeClass('badge-success').addClass('badge-secondary').text('Inactive');
            startBtn.prop('disabled', false);
            stopBtn.prop('disabled', true);
        }
    }

    addActivityLog(botId, message, type = 'info', details = null) {
        const logContainer = $(`#log-${botId}`);
        const timestamp = new Date().toLocaleTimeString();

        let iconClass = 'fas fa-info-circle';
        let textClass = 'text-info';

        switch (type) {
            case 'success':
                iconClass = 'fas fa-check-circle';
                textClass = 'text-success';
                break;
            case 'error':
                iconClass = 'fas fa-exclamation-circle';
                textClass = 'text-danger';
                break;
            case 'warning':
                iconClass = 'fas fa-exclamation-triangle';
                textClass = 'text-warning';
                break;
        }

        const logEntry = $(`
            <div class="d-flex align-items-start mb-1 ${textClass}" style="font-size: 0.7rem;">
                <i class="${iconClass} mr-1 mt-1" style="font-size: 0.6rem;"></i>
                <div>
                    <div class="font-weight-bold">${timestamp}</div>
                    <div>${message}</div>
                    ${details ? `<div class="text-muted">${details}</div>` : ''}
                </div>
            </div>
        `);

        logContainer.append(logEntry);

        // Keep only last 10 entries
        const entries = logContainer.children();
        if (entries.length > 10) {
            entries.first().remove();
        }

        // Auto-scroll to bottom
        logContainer.scrollTop(logContainer[0].scrollHeight);
    }

    addGlobalActivity(message, type = 'info', data = null) {
        const feedContainer = $('#globalActivityFeed');
        const timestamp = new Date().toLocaleTimeString();
        this.activityCount++;

        let iconClass = 'fas fa-info-circle';
        let badgeClass = 'badge-info';

        switch (type) {
            case 'success':
                iconClass = 'fas fa-check-circle';
                badgeClass = 'badge-success';
                break;
            case 'error':
                iconClass = 'fas fa-exclamation-circle';
                badgeClass = 'badge-danger';
                break;
            case 'warning':
                iconClass = 'fas fa-exclamation-triangle';
                badgeClass = 'badge-warning';
                break;
        }

        const activityEntry = $(`
            <div class="list-group-item list-group-item-action border-0 py-2">
                <div class="d-flex w-100 justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <i class="${iconClass} mr-2 text-${type === 'success' ? 'success' : type === 'error' ? 'danger' : 'info'}"></i>
                        <div>
                            <h6 class="mb-1 font-weight-medium">${message}</h6>
                            <small class="text-muted">${timestamp}</small>
                        </div>
                    </div>
                    <span class="badge ${badgeClass}">#${this.activityCount}</span>
                </div>
                ${data && data.details ? `<p class="mb-1 text-muted small mt-2">${data.details}</p>` : ''}
            </div>
        `);

        // Remove the "waiting" message if it exists
        feedContainer.find('.text-center').remove();

        feedContainer.prepend(activityEntry);

        // Keep only last 20 entries
        const entries = feedContainer.children();
        if (entries.length > 20) {
            entries.last().remove();
        }
    }

    animateProgress(botId) {
        const progressBar = $(`#progress-${botId}`);
        progressBar.css('width', '100%');
        setTimeout(() => {
            progressBar.css('width', '0%');
        }, 800);
    }

    startAllBots() {
        Object.keys(this.bots).forEach(botId => {
            if (!this.bots[botId].active) {
                this.startBot(botId);
            }
        });
    }

    stopAllBots() {
        Object.keys(this.bots).forEach(botId => {
            if (this.bots[botId].active) {
                this.stopBot(botId);
            }
        });
    }

    updateDashboard() {
        // Update runtime for active bots
        Object.entries(this.bots).forEach(([botId, bot]) => {
            if (bot.active && bot.startTime) {
                const runtime = Math.floor((Date.now() - bot.startTime) / 1000);
                const hours = Math.floor(runtime / 3600);
                const minutes = Math.floor((runtime % 3600) / 60);
                const seconds = runtime % 60;

                const runtimeDisplay = `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
                $(`#runtime-${botId}`).text(runtimeDisplay);
            }
        });
    }

    // Pricing Logic Panel Functions
    updatePricingLogicPanel(data) {
        // Update selection criteria if provided
        if (data.selectionCriteria) {
            this.updateSelectionCriteria(data.selectionCriteria);
        }

        // Update active strategies if provided
        if (data.strategies) {
            this.updateActiveStrategies(data.strategies);
        }

        // Add new decision if provided
        if (data.decision) {
            this.addPricingDecision(data.decision);
        }
    }

    updateSelectionCriteria(criteria) {
        const container = $('#selection-criteria');

        // Update margin target
        if (criteria.marginTarget) {
            container.find('.badge-info').next('.text-muted').text(`Target: ≥ ${criteria.marginTarget}% margin`);
        }

        // Highlight active criteria
        if (criteria.activeFactors) {
            container.find('.badge').removeClass('badge-light').addClass('badge-info');
            criteria.activeFactors.forEach(factor => {
                container.find(`.badge:contains("${factor}")`).removeClass('badge-info').addClass('badge-primary');
            });
        }
    }

    updateActiveStrategies(strategies) {
        const container = $('#active-strategies');

        // Reset all strategies to default state
        container.find('.strategy-item').removeClass('active-strategy');

        // Highlight active strategies
        strategies.forEach(strategy => {
            const item = container.find(`[data-strategy="${strategy.key}"]`);
            if (item.length) {
                item.addClass('active-strategy');
                if (strategy.range) {
                    item.find('.badge').text(strategy.range);
                }
            }
        });
    }

    addPricingDecision(decision) {
        const container = $('#recent-decisions');

        // Remove "no decisions" message if present
        if (container.find('.text-center').length) {
            container.empty();
        }

        // Create decision entry
        const timestamp = new Date().toLocaleTimeString();
        const decisionHtml = `
            <div class="decision-entry mb-2 p-2" style="border-left: 3px solid #007bff; background: #f8f9fa;">
                <div class="d-flex justify-content-between align-items-start mb-1">
                    <strong class="text-primary">${decision.productName}</strong>
                    <small class="text-muted">${timestamp}</small>
                </div>
                <div class="small">
                    <div><strong>Strategy:</strong> ${decision.strategy}</div>
                    <div><strong>Price:</strong> ₹${decision.oldPrice} → ₹${decision.newPrice} (${decision.change})</div>
                    <div class="text-muted mt-1">${decision.reason}</div>
                </div>
            </div>
        `;

        container.prepend(decisionHtml);

        // Keep only last 10 decisions
        const decisions = container.find('.decision-entry');
        if (decisions.length > 10) {
            decisions.slice(10).remove();
        }
    }

    // Highlight active strategy when pricing bot is working
    highlightActiveStrategy(strategyKey) {
        const container = $('#active-strategies');
        container.find('.strategy-item').removeClass('highlight');

        const targetStrategy = container.find(`[data-strategy="${strategyKey}"]`);
        if (targetStrategy.length) {
            targetStrategy.addClass('highlight');
            setTimeout(() => {
                targetStrategy.removeClass('highlight');
            }, 3000);
        }
    }
}

function refreshDashboard() {
    location.reload();
}

function clearActivityFeed() {
    $('#globalActivityFeed').html(`
        <div class="text-center py-4 text-muted">
            <i class="fas fa-clock fa-2x mb-2"></i>
            <div>Waiting for bot activities...</div>
        </div>
    `);
}

// Initialize when jQuery is ready
$(document).ready(function () {
    console.log('Initializing Bot Dashboard...');
    window.botDashboard = new BotDashboard();
    console.log('Bot Dashboard initialized successfully');
});
