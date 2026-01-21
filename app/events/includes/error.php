<style>
    /* Custom Error page - Fullscreen overlay with background blur */
    .custom-error-page.error-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(10px);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 1001;
    }

    .custom-error-page .error-container {
        background: white;
        border-radius: 10px;
        padding: 30px;
        text-align: center;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
        max-width: 560px;
        width: 90%;
        position: relative;
    }

    .custom-error-page .error-image {
        max-width: 180px;
        margin: 0 auto 20px;
        display: block;
    }

    .custom-error-page .error-container h1 {
        font-size: 24px;
        margin-bottom: 20px;
        color: #333;
    }

    .custom-error-page .error-container p {
        font-size: 16px;
        color: #555;
    }

    .custom-error-page .button-container {
        display: flex;
        justify-content: space-evenly;
        margin-top: 30px;
    }

    .custom-error-page .button-container button {
        padding: 10px 20px;
        font-size: 16px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .custom-error-page .button-container .refresh-btn {
        background-color: #007BFF;
        color: white;
    }

    .custom-error-page .button-container .refresh-btn:hover {
        background-color: #0056b3;
    }

    .custom-error-page .button-container .go-back-btn {
        background-color: #6c757d;
        color: white;
    }

    .custom-error-page .button-container .go-back-btn:hover {
        background-color: #565e64;
    }

    /* /Custom Error page */
</style>

<!-- Error Page Content -->
<div class="custom-error-page error-overlay">
    <div class="error-container">
        <?php
        $allow_go_back_btn = true;
        $allow_refresh_btn = true;

        if (empty($error_code)) {
            $error_code = 1001;
        }

        if (empty($error_from)) {
            $error_from = 'server';
        }

        if ($error_from === 'event_exhibitor' && in_array($error_code, [1001])) {
            echo '<img src="' . TAOH_SITE_URL_ROOT . '/assets/images/server-maintenance.png" alt="Error" class="error-image">';
            echo '<h1>An unexpected issue occurred while processing your request.</h1>';
            echo '<p>Unable to complete the operation due to an invalid request.</p>';
            echo '<small>Error Code: ' . $error_code . '</small>';

            $allow_refresh_btn = false;
        } else {
            echo '<img src="' . TAOH_SITE_URL_ROOT . '/assets/images/server-maintenance.png" alt="Error" class="error-image">';
            echo '<h1>We’re Working on It!</h1>';
            echo '<p>Our servers are experiencing heavy load; we’re working to get you in as soon as the room opens. Please keep this window open, refresh the page, or click the buttons below to explore other options.</p>';
        }

        if($allow_go_back_btn || $allow_refresh_btn){
            echo '<div class="button-container">';
            if($allow_go_back_btn) {
                echo '<button class="go-back-btn" onclick="history.back()">Go Back</button>';
            }
            if($allow_refresh_btn) {
                echo '<button class="refresh-btn" onclick="location.reload()">Refresh</button>';
            }
            echo '</div>';
        }

        ?>
    </div>
</div>

<script type="text/javascript">
    setInterval(function() {
        location.reload();
    }, 60000); // Reload the page every 1 minute
</script>