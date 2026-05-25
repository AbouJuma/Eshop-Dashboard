<!-- SMS Balance Widget -->
<div class="row mb-5">
    <div class="col-12">
        <div class="section-header" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); border-radius: 12px; padding: 20px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); position: relative; overflow: hidden;">
            <div style="position: absolute; top: -20px; right: -20px; width: 80px; height: 80px; background: rgba(255, 255, 255, 0.1); border-radius: 50%;"></div>
            <div style="position: absolute; bottom: -15px; left: -15px; width: 60px; height: 60px; background: rgba(255, 255, 255, 0.08); border-radius: 50%;"></div>
            <div style="display: flex; align-items: center; position: relative; z-index: 2;">
                <div style="background: rgba(255, 255, 255, 0.2); border-radius: 50%; width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; margin-right: 15px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);">
                    <i class="fas fa-sms" style="color: white; font-size: 20px;"></i>
                </div>
                <div>
                    <h4 style="color: white; font-weight: 700; margin: 0; font-size: 18px; text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);">SMS Balance Management</h4>
                    <p style="color: rgba(255, 255, 255, 0.9); margin: 0; font-size: 14px;">Monitor your Beems SMS balance and usage</p>
                </div>
                <div style="margin-left: auto; display: flex; gap: 10px;">
                    <button onclick="refreshSMSBalance()" class="btn btn-sm btn-light" style="background: rgba(255, 255, 255, 0.2); border: 1px solid rgba(255, 255, 255, 0.3); color: white; padding: 6px 12px; border-radius: 6px; font-size: 12px;">
                        <i class="fas fa-sync-alt"></i> Refresh
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- SMS Balance Cards -->
<div class="row app-dash-row mb-5">
    <div class="col-md-3 col-sm-6 col-xs-12 col-custom">
        <div class="info-box info-box-new-style app-dash-box">
            <span class="info-box-icon" style="background: linear-gradient(135deg, #10b981, #059669);">
                <i class="fas fa-sms"></i>
            </span>
            <div class="info-box-content">
                <span class="info-box-text">Available SMS</span>
                <span class="info-box-number sms-balance-count" style="text-decoration: none; color: inherit;">
                    <i class="fas fa-sync fa-spin fa-fw margin-bottom"></i>
                </span>
                <p class="mb-0 text-muted fs-10 mt-5">Real-time balance from Beems</p>
            </div>
        </div>
    </div>

    <div class="col-md-3 col-sm-6 col-xs-12 col-custom">
        <div class="info-box info-box-new-style app-dash-box">
            <span class="info-box-icon" style="background: linear-gradient(135deg, #f59e0b, #d97706);">
                <i class="fas fa-chart-line"></i>
            </span>
            <div class="info-box-content">
                <span class="info-box-text">Balance Status</span>
                <span class="info-box-number sms-balance-status" style="text-decoration: none; color: inherit;">
                    <i class="fas fa-sync fa-spin fa-fw margin-bottom"></i>
                </span>
                <p class="mb-0 text-muted fs-10 mt-5">Current account status</p>
            </div>
        </div>
    </div>

    <div class="col-md-3 col-sm-6 col-xs-12 col-custom">
        <div class="info-box info-box-new-style app-dash-box">
            <span class="info-box-icon" style="background: linear-gradient(135deg, #ef4444, #dc2626);">
                <i class="fas fa-exclamation-triangle"></i>
            </span>
            <div class="info-box-content">
                <span class="info-box-text">Warning Threshold</span>
                <span class="info-box-number sms-warning-threshold" style="text-decoration: none; color: inherit;">
                    100
                </span>
                <p class="mb-0 text-muted fs-10 mt-5">Alert when balance reaches this level</p>
            </div>
        </div>
    </div>

    <div class="col-md-3 col-sm-6 col-xs-12 col-custom">
        <div class="info-box info-box-new-style app-dash-box">
            <span class="info-box-icon" style="background: linear-gradient(135deg, #8b5cf6, #7c3aed);">
                <i class="fas fa-bell"></i>
            </span>
            <div class="info-box-content">
                <span class="info-box-text">Alert Recipients</span>
                <span class="info-box-number sms-notifications" style="text-decoration: none; color: inherit;">
                    4 Numbers
                </span>
                <p class="mb-0 text-muted fs-10 mt-5">
                    <div style="display: flex; flex-wrap: wrap; gap: 8px;">
                        <span style="display: inline-flex; align-items: center; background: rgba(139, 92, 246, 0.1); padding: 2px 6px; border-radius: 4px; font-size: 9px;">
                            <i class="fas fa-phone" style="color: #8b5cf6; margin-right: 3px;"></i> 06267619
                        </span>
                        <span style="display: inline-flex; align-items: center; background: rgba(139, 92, 246, 0.1); padding: 2px 6px; border-radius: 4px; font-size: 9px;">
                            <i class="fas fa-phone" style="color: #8b5cf6; margin-right: 3px;"></i> 0787011402
                        </span>
                        <span style="display: inline-flex; align-items: center; background: rgba(139, 92, 246, 0.1); padding: 2px 6px; border-radius: 4px; font-size: 9px;">
                            <i class="fas fa-phone" style="color: #8b5cf6; margin-right: 3px;"></i> 0684551070
                        </span>
                        <span style="display: inline-flex; align-items: center; background: rgba(139, 92, 246, 0.1); padding: 2px 6px; border-radius: 4px; font-size: 9px;">
                            <i class="fas fa-phone" style="color: #8b5cf6; margin-right: 3px;"></i> 0788753599
                        </span>
                    </div>
                </p>
            </div>
        </div>
    </div>
</div>

<!-- SMS Balance Progress Bar -->
<div class="row mb-5">
    <div class="col-12">
        <div class="card" style="border-radius: 12px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); border: none;">
            <div class="card-body" style="padding: 20px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                    <h5 style="margin: 0; color: #374151; font-weight: 600;">SMS Balance Overview</h5>
                    <span class="sms-last-updated" style="color: #6b7280; font-size: 12px;">
                        <i class="fas fa-clock"></i> Loading...
                    </span>
                </div>
                <div class="progress" style="height: 25px; border-radius: 12px; background-color: #f3f4f6;">
                    <div class="progress-bar sms-balance-progress" role="progressbar" style="border-radius: 12px; background: linear-gradient(90deg, #10b981, #059669); transition: width 0.6s ease; font-weight: 600; font-size: 12px;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                        0%
                    </div>
                </div>
                <div style="display: flex; justify-content: space-between; margin-top: 10px;">
                    <small style="color: #6b7280;">0 SMS</small>
                    <small style="color: #6b7280;">1000 SMS (Starting Balance)</small>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function refreshSMSBalance() {
    const balanceElements = document.querySelectorAll('.sms-balance-count, .sms-balance-status');
    balanceElements.forEach(el => {
        el.innerHTML = '<i class="fas fa-sync fa-spin fa-fw margin-bottom"></i>';
    });
    
    fetch('{{ route("sms.balance.api") }}')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateSMSBalanceDisplay(data.data);
            } else {
                document.querySelector('.sms-balance-count').textContent = 'Error';
                document.querySelector('.sms-balance-status').textContent = 'Error';
            }
        })
        .catch(error => {
            document.querySelector('.sms-balance-count').textContent = 'Offline';
            document.querySelector('.sms-balance-status').textContent = 'Offline';
        });
}

function updateSMSBalanceDisplay(data) {
    // Update balance
    document.querySelector('.sms-balance-count').textContent = data.balance + ' SMS';
    
    // Update status with appropriate styling
    const statusElement = document.querySelector('.sms-balance-status');
    statusElement.textContent = data.status.charAt(0).toUpperCase() + data.status.slice(1);
    
    // Update status color based on level
    statusElement.style.color = 
        data.status === 'critical' ? '#ef4444' : 
        data.status === 'warning' ? '#f59e0b' : 
        '#10b981';
    
    // Update progress bar
    const percentage = Math.min((data.balance / 1000) * 100, 100);
    const progressBar = document.querySelector('.sms-balance-progress');
    progressBar.style.width = percentage + '%';
    progressBar.setAttribute('aria-valuenow', percentage);
    progressBar.textContent = percentage.toFixed(1) + '%';
    
    // Update progress bar color based on status
    if (data.status === 'critical') {
        progressBar.style.background = 'linear-gradient(90deg, #ef4444, #dc2626)';
    } else if (data.status === 'warning') {
        progressBar.style.background = 'linear-gradient(90deg, #f59e0b, #d97706)';
    } else {
        progressBar.style.background = 'linear-gradient(90deg, #10b981, #059669)';
    }
    
    // Update last updated time
    const lastUpdated = document.querySelector('.sms-last-updated');
    if (lastUpdated) {
        lastUpdated.innerHTML = '<i class="fas fa-clock"></i> Last updated: ' + new Date().toLocaleTimeString();
    }
}

// Auto-refresh every 5 minutes
setInterval(refreshSMSBalance, 300000);

// Initial load when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    refreshSMSBalance();
});
</script>
