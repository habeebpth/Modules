<!-- queue_status.blade.php -->
<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Google Fonts -->
    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&family=Playfair+Display:wght@700&display=swap"
        rel="stylesheet">

    <style>
        :root {
            --primary-color: #3a7bd5;
            --primary-gradient: linear-gradient(135deg, #3a7bd5 0%, #3a6073 100%);
            --secondary-color: #6c757d;
            --dark-color: #2d3748;
            --light-color: #f8f9fa;
            --accent-color: #00c9ff;
            --danger-color: #dc3545;
            --success-color: #28a745;
        }

        body {
            font-family: 'Montserrat', sans-serif;
            background-color: #f5f7fa;
            margin: 0;
            padding: 0;
            color: var(--dark-color);
            line-height: 1.6;
        }

        .container {
            max-width: 1000px;
            margin: 50px auto;
            padding: 0 20px;
        }

        .queue-card {
            background-color: white;
            border-radius: 20px;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            position: relative;
            margin-bottom: 30px;
        }

        .header {
            background: var(--primary-gradient);
            padding: 40px;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: relative;
        }

        .header::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 20px;
            background: linear-gradient(135deg, transparent 25px, white 0);
        }

        .logo {
            height: 80px;
            filter: brightness(0) invert(1);
        }

        .event-info {
            text-align: right;
        }

        .event-title {
            font-family: 'Playfair Display', serif;
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 10px;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .event-description {
            font-size: 16px;
            font-weight: 300;
            opacity: 0.9;
            max-width: 400px;
        }

        .content {
            padding: 10px;
        }

        .page-title {
            font-family: 'Playfair Display', serif;
            font-size: 20px;
            font-weight: 700;
            text-align: center;
            margin-bottom: 0px;
            color: var(--dark-color);
            position: relative;
            padding-bottom: 8px;
        }

        .page-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 3px;
            background: var(--primary-gradient);
            border-radius: 3px;
        }

        .token-display {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 30px;
            margin: 20px 0;
        }

        .token-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            padding: 30px;
            width: 300px;
            text-align: center;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .token-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        }

        .token-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 5px;
            height: 100%;
            background: var(--primary-gradient);
        }

        .token-category {
            font-weight: 600;
            font-size: 20px;
            margin-bottom: 20px;
            color: var(--dark-color);
        }

        .token-range {
            font-size: 32px;
            font-weight: 700;
            color: var(--primary-color);
            background: linear-gradient(135deg, #3a7bd5 0%, #3a6073 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            letter-spacing: 2px;
            margin-top: 10px;
        }

        .footer {
            background: #f5f7fa;
            border-top: 1px solid rgba(0, 0, 0, 0.05);
            padding: 25px;
            text-align: center;
            color: var(--secondary-color);
            font-size: 14px;
            font-weight: 500;
        }

        /* Next batch and countdown styles */
        .next-batch {
            text-align: center;
            margin: 20px 0 10px;
            font-size: 22px;
            font-weight: 600;
            color: var(--dark-color);
        }

        .countdown {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }

        .countdown-item {
            margin: 0 10px;
            text-align: center;
        }

        .countdown-number {
            font-size: 30px;
            font-weight: 700;
            background: var(--primary-gradient);
            color: white;
            border-radius: 10px;
            padding: 10px 15px;
            min-width: 60px;
            display: inline-block;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .countdown-label {
            font-size: 12px;
            color: var(--secondary-color);
            margin-top: 5px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* Admin Form Styles - These are applied only when form is injected */
        .admin-form-container {
            background-color: white;
            border-radius: 20px;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            position: relative;
            margin-bottom: 30px;
        }

        .form-header {
            background: var(--primary-gradient);
            padding: 20px 40px;
            color: white;
        }

        .form-title {
            font-family: 'Playfair Display', serif;
            font-size: 24px;
            font-weight: 700;
            margin: 0;
        }

        .form-content {
            padding: 30px 40px;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--dark-color);
        }

        .form-control {
            width: 100%;
            padding: 12px 15px;
            font-size: 16px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            background-color: #f8fafc;
            transition: all 0.3s;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(58, 123, 213, 0.1);
        }

        .form-control:disabled {
            background-color: #edf2f7;
            cursor: not-allowed;
        }

        .btn {
            display: inline-block;
            font-weight: 600;
            text-align: center;
            white-space: nowrap;
            vertical-align: middle;
            user-select: none;
            border: 1px solid transparent;
            padding: 12px 25px;
            font-size: 16px;
            line-height: 1.5;
            border-radius: 8px;
            transition: all 0.15s ease-in-out;
            cursor: pointer;
        }

        .btn-primary {
            color: white;
            background: var(--primary-gradient);
            border-color: var(--primary-color);
        }

        .btn-primary:hover {
            opacity: 0.9;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(58, 123, 213, 0.3);
        }

        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid transparent;
            border-radius: 8px;
        }

        .alert-success {
            color: #155724;
            background-color: #d4edda;
            border-color: #c3e6cb;
        }

        .alert-danger {
            color: #721c24;
            background-color: #f8d7da;
            border-color: #f5c6cb;
        }

        .alert-info {
            color: #0c5460;
            background-color: #d1ecf1;
            border-color: #bee5eb;
        }

        /* Loading spinner */
        .loading-spinner {
            display: inline-block;
            width: 40px;
            height: 40px;
            border: 4px solid rgba(0, 0, 0, 0.1);
            border-radius: 50%;
            border-top: 4px solid var(--primary-color);
            animation: spin 1s linear infinite;
            margin: 20px auto;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .loading-container {
            text-align: center;
            padding: 20px;
        }

        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                padding: 30px;
                text-align: center;
            }

            .event-info {
                text-align: center;
                margin-top: 20px;
            }

            .token-display {
                flex-direction: column;
                align-items: center;
            }

            .token-card {
                width: 100%;
                max-width: 300px;
            }
        }
    </style>

    <title>Queue Status - THE SOLUTION</title>
</head>

<body>
    <div class="container" id="mainContainer">
        <!-- Queue Display Card -->
        <div class="queue-card">
            <div class="header">
                <div>
                    <img src="123123" alt="THE SOLUTION" class="logo" />
                </div>
                <div class="event-info">
                    <div class="event-title">THE SOLUTION</div>
                    <div class="event-description">
                        123123
                    </div>
                </div>
            </div>

            <div class="content">
                <h1 class="page-title">Queue Status</h1>

                <div id="loadingContainer" class="loading-container">
                    <div class="loading-spinner"></div>
                    <p>Loading queue status...</p>
                </div>

                <div id="tokenDisplay" class="token-display" style="display: none;">
                    <div class="token-card">
                        <div class="token-category">Gents</div>
                        <div class="token-range" id="gentsTokenDisplay"></div>
                    </div>

                    <div class="token-card">
                        <div class="token-category">Ladies</div>
                        <div class="token-range" id="ladiesTokenDisplay"></div>
                    </div>
                </div>

                <!-- Next Batch section with countdown -->
                <div id="nextBatchSection" style="display: none;">
                    <div class="next-batch">അടുത്ത ബാച്ച് 20 മിനിറ്റിനുള്ളിൽ</div>
                    <div class="countdown">
                        <div class="countdown-item">
                            <div class="countdown-number" id="minutesDisplay">20</div>
                            <div class="countdown-label">Minutes</div>
                        </div>
                        <div class="countdown-item">
                            <div class="countdown-number" id="secondsDisplay">00</div>
                            <div class="countdown-label">Seconds</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="footer">
                This is a real-time queue status display. The display updates automatically.
            </div>
        </div>

        <!-- Admin form will be injected here if authorized -->
        <div id="adminFormContainer"></div>
    </div>

    <script>
        // Configuration
        const EVENT_ID = 1; // Get from Laravel view
        const API_BASE_URL = '/api'; // Adjust based on your actual API base URL
        let countdownInterval; // To hold the countdown interval
        let countdownMinutes = 20; // Default countdown time

        // Function to get URL parameters
        function getUrlParameter(name) {
            name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
            var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
            var results = regex.exec(location.search);
            return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
        }

        // Function to show alerts
        function showAlert(message, type, container) {
            const alertBox = document.createElement('div');
            alertBox.className = type === 'success' ? 'alert alert-success' : 'alert alert-danger';
            alertBox.textContent = message;

            // Clear any existing alerts
            while (container.firstChild) {
                container.removeChild(container.firstChild);
            }

            // Add the new alert
            container.appendChild(alertBox);

            // Hide after 3 seconds
            setTimeout(() => {
                if (container.contains(alertBox)) {
                    container.removeChild(alertBox);
                }
            }, 3000);
        }

        // Function to start the countdown
        function startCountdown(minutes) {
            // Clear any existing interval
            if (countdownInterval) {
                clearInterval(countdownInterval);
            }

            // Set the countdown time
            countdownMinutes = minutes || 20;
            let totalSeconds = countdownMinutes * 60;
            
            // Show the next batch section
            document.getElementById('nextBatchSection').style.display = 'block';
            
            // Update the display immediately
            updateCountdownDisplay(totalSeconds);
            
            // Start the countdown
            countdownInterval = setInterval(() => {
                totalSeconds--;
                
                if (totalSeconds <= 0) {
                    clearInterval(countdownInterval);
                    // Hide the countdown when it reaches zero
                    document.getElementById('nextBatchSection').style.display = 'none';
                } else {
                    updateCountdownDisplay(totalSeconds);
                }
            }, 1000);
        }

        // Function to update the countdown display
        function updateCountdownDisplay(totalSeconds) {
            const minutes = Math.floor(totalSeconds / 60);
            const seconds = totalSeconds % 60;
            
            document.getElementById('minutesDisplay').textContent = String(minutes).padStart(2, '0');
            document.getElementById('secondsDisplay').textContent = String(seconds).padStart(2, '0');
        }

        // Function to fetch queue status
        async function fetchQueueStatus() {
            try {
                const response = await fetch(`${API_BASE_URL}/app/get-queue-status/${EVENT_ID}`);
                const data = await response.json();

                if (data.status === 'success') {
                    document.getElementById('gentsTokenDisplay').textContent = data.data.gents || '';
                    document.getElementById('ladiesTokenDisplay').textContent = data.data.ladies || '';

                    // Hide loading, show token display
                    document.getElementById('loadingContainer').style.display = 'none';
                    document.getElementById('tokenDisplay').style.display = 'flex';
                    
                    // If countdown minutes is set in the data, start the countdown
                    if (data.data.countdown_minutes) {
                        startCountdown(parseInt(data.data.countdown_minutes));
                    }

                    return data.data;
                } else {
                    throw new Error(data.message || 'Failed to fetch queue status');
                }
            } catch (error) {
                console.error('Error fetching queue status:', error);
                // Show error in the loading container
                const loadingContainer = document.getElementById('loadingContainer');
                loadingContainer.innerHTML = `
                    <div class="alert alert-danger">
                        Failed to load queue status. Please refresh the page.
                    </div>
                `;
            }
        }

        // Function to inject admin form dynamically
        function injectAdminForm(type, queueData) {
            const formContainer = document.getElementById('adminFormContainer');

            // Create form elements
            const adminForm = document.createElement('div');
            adminForm.className = 'admin-form-container';

            // Determine if the countdown field should be visible
            // For gents admin, we don't need to show it as it auto-resets
            const showCountdownField = type !== 'gents';

            // Create form HTML
            adminForm.innerHTML = `
                <div class="form-header">
                    <h2 class="form-title">Update Queue Status</h2>
                </div>
                <div class="form-content">
                    <div id="alertContainer"></div>

                    <form id="queueUpdateForm">
                        <input type="hidden" id="eventId" name="event_id" value="${EVENT_ID}">
                        <input type="hidden" id="type" name="type" value="${type}">
                        <input type="hidden" id="token" name="token" value="7fG2hJ9kLpQ1">

                        <div class="form-group">
                            <label for="gentsQueue" class="form-label">Gents Queue</label>
                            <input type="text" id="gentsQueue" name="gents" class="form-control"
                                placeholder="e.g. G201 - G230" ${type !== 'gents' ? 'disabled' : ''}>
                        </div>

                        <div class="form-group">
                            <label for="ladiesQueue" class="form-label">Ladies Queue</label>
                            <input type="text" id="ladiesQueue" name="ladies" class="form-control"
                                placeholder="e.g. L201 - L230" ${type !== 'ladies' ? 'disabled' : ''}>
                        </div>

                        ${showCountdownField ? `
                        <div class="form-group">
                            <label for="countdownMinutes" class="form-label">Countdown Minutes</label>
                            <input type="number" id="countdownMinutes" name="countdown_minutes" class="form-control"
                                placeholder="e.g. 20" min="1" max="60" value="${queueData.countdown_minutes || 20}">
                        </div>
                        ` : `
                        <div class="form-group">
                            <p class="alert alert-info">
                                <i>When updating Gents queue, the timer will automatically reset to 20 minutes.</i>
                            </p>
                            <input type="hidden" id="countdownMinutes" name="countdown_minutes" value="20">
                        </div>
                        `}

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </div>
                    </form>
                </div>
            `;

            // Append form to the container
            formContainer.appendChild(adminForm);

            // Pre-fill the form fields with current values
            document.getElementById('gentsQueue').value = queueData.gents || '';
            document.getElementById('ladiesQueue').value = queueData.ladies || '';

            // Add form submission handler
            document.getElementById('queueUpdateForm').addEventListener('submit', async function(e) {
                e.preventDefault();

                const formData = new FormData(this);
                const alertContainer = document.getElementById('alertContainer');

                try {
                    // Show loading state
                    const submitButton = this.querySelector('button[type="submit"]');
                    const originalButtonText = submitButton.textContent;
                    submitButton.disabled = true;
                    submitButton.textContent = 'Saving...';

                    // Prepare data for API
                    const payload = {
                        event_id: formData.get('event_id'),
                        token: formData.get('token'),
                        type: formData.get('type')
                    };

                    // Only include the fields they're allowed to update
                    if (!document.getElementById('gentsQueue').disabled) {
                        payload.gents = formData.get('gents');
                        // When updating gents, countdown will be auto-reset by the server
                    } else if (!document.getElementById('ladiesQueue').disabled) {
                        payload.ladies = formData.get('ladies');
                        // Only include countdown_minutes if explicitly set by ladies admin
                        payload.countdown_minutes = formData.get('countdown_minutes');
                    }

                    // Send to API
                    const response = await fetch(`${API_BASE_URL}/app/save-queue-status`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                ?.getAttribute('content')
                        },
                        body: JSON.stringify(payload)
                    });

                    const data = await response.json();

                    // Reset button state
                    submitButton.disabled = false;
                    submitButton.textContent = originalButtonText;

                    if (data.status === 'success') {
                        // Update the display with new values
                        if (!document.getElementById('gentsQueue').disabled) {
                            document.getElementById('gentsTokenDisplay').textContent = formData.get('gents');
                            // For gents, always restart the countdown with 20 minutes
                            startCountdown(20);
                        } else if (!document.getElementById('ladiesQueue').disabled) {
                            document.getElementById('ladiesTokenDisplay').textContent = formData.get('ladies');
                            // For ladies, restart countdown with the specified value
                            startCountdown(parseInt(formData.get('countdown_minutes')));
                        }

                        // Show success message
                        showAlert('Queue status updated successfully!', 'success', alertContainer);
                    } else {
                        throw new Error(data.message || 'Failed to update queue status');
                    }
                } catch (error) {
                    console.error('Error updating queue status:', error);
                    showAlert(error.message || 'Failed to update queue status. Please try again.', 'danger',
                        alertContainer);
                }
            });
        }

        // Initialize the page
        document.addEventListener('DOMContentLoaded', async function() {
            // First load the queue status
            const queueData = await fetchQueueStatus();

            // Check for admin access
            const role = getUrlParameter('role');
            const type = getUrlParameter('type');

            // If role parameter matches the secret key, inject the admin form
            if (role === '7fG2hJ9kLpQ1' && queueData) {
                injectAdminForm(type, queueData);
            }

            // Set up auto-refresh every 30 seconds
            setInterval(fetchQueueStatus, 30000);
        });

        // Security measures for non-admin users
        document.addEventListener('DOMContentLoaded', function() {
            const role = getUrlParameter('role');
            if (role !== '7fG2hJ9kLpQ1') {
                // Override the browser's inspect element context menu
                document.addEventListener('contextmenu', function(e) {
                    e.preventDefault();
                    return false;
                });

                // Prevent keyboard shortcuts
                document.addEventListener('keydown', function(e) {
                    // Prevent Ctrl+U (view source)
                    if (e.ctrlKey && e.key === 'u') {
                        e.preventDefault();
                        return false;
                    }
                    
                    // Prevent F12 (developer tools)
                    // if (e.key === 'F12') {
                    //     e.preventDefault();
                    //     return false;
                    // }
                });
            }
        });
    </script>
</body>

</html>