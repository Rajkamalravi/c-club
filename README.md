# Project README

## Project Overview

This project, **TAO.ai Hires** (or a similar appropriate name based on findings like TAOH_SITE_NAME_SLUG), is a comprehensive platform designed to foster career growth by connecting professionals with opportunities, networks, and resources.

Key features include:

*   **Job Board:** Functionality for listing and applying to jobs.
*   **Event Management:** Tools for creating, discovering, and participating in career-focused events.
*   **Asks (Q&A):** A dedicated section for users to ask questions and receive answers on various professional topics.
*   **Networking:** Features to help users connect with peers, potentially including clubs, chat rooms, and user profiles.
*   **Learning Resources:** Access to articles, flashcards, and other educational content to support skill development.

## Technology Stack

*   **Core Language:** PHP (Requires version 8.2 or higher, as indicated in `hires.php`)
*   **Web Server:** Apache (with `mod_rewrite` for URL rewriting) or Nginx.
*   **Database:** Likely MySQL (This is a common choice for PHP applications of this nature, though not explicitly confirmed in a configuration file. The application relies on external APIs for data persistence which might reduce the direct need for a complex local DB schema).
*   **Frontend:** HTML, CSS, JavaScript. Specific JavaScript libraries or frameworks used are not yet detailed but may include jQuery, given its prevalence in similar vintage PHP projects.
*   **Dependencies:** The project uses Composer for managing PHP dependencies in some modules (e.g., `core/excel/`). A root-level `composer.json` for project-wide dependencies has not been identified.
*   **External Services:** Relies on various TAO.ai services for APIs, CDN, and potentially other functionalities.

## Prerequisites

Before you begin, ensure you have the following installed and configured:

*   **PHP:** Version 8.2 or newer. You'll also need common PHP extensions for web development (e.g., `mbstring`, `json`, `curl`, and whatever database driver is used, likely `pdo_mysql` or `mysqli`).
*   **Web Server:**
    *   **Apache:** With the `mod_rewrite` module enabled.
    *   **Nginx:** Configured to handle PHP requests and URL rewriting.
*   **Database Server:** A MySQL server is likely required. Ensure it's running and accessible.
*   **Composer:** While not confirmed for project-wide dependencies, Composer is used for some internal modules (like Excel export). It's good to have it installed if you need to manage these. You can download it from [getcomposer.org](https://getcomposer.org/).
*   **Access to TAO.ai Services:** The application requires an API secret key from TAO.ai to function.

## Installation & Setup

1.  **Clone the Repository:**
    ```bash
    git clone <repository_url>
    cd <project_directory>
    ```
    (Replace `<repository_url>` with the actual URL and `<project_directory>` with the chosen directory name)

2.  **Obtain API Secret Key:**
    As mentioned in the original `readme.txt`:
    *   Email the following information to `info@tao.ai` with the subject "Requesting Secret Key":
        *   Your First Name
        *   Your Last Name
        *   Your website address
        *   Your primary associated email (This email will receive the secret key)
        *   Your Website Title
        *   Your Website Description
        *   Your Website square logo URL (Typically used for a Favicon, 128x128px)
    *   You will receive a `TAOH_API_SECRET` key.

3.  **Configure the Application:**
    *   The main configuration is handled in `config.php`. This file defines various constants and settings for the application.
    *   **Environment Variables:** The `config.php` file attempts to load an `env.php` file. You can create this file by copying `env_sample.php`:
        ```bash
        cp env_sample.php env.php
        ```
        Then, edit `env.php` to set your `TAOH_API_SECRET` and other site-specific details like `TAOH_SITE_SOURCE`, `TAOH_SITE_NAME_SLUG`, etc.
        ```php
        <?php
        /* env.php */
        if ( ! defined('TAOH_API_SECRET') ) define('TAOH_API_SECRET', 'YOUR_API_SECRET_HERE' );
        if ( ! defined('TAOH_SITE_SOURCE') ) define('TAOH_SITE_SOURCE', 'https://yourwebsite.com');
        if ( ! defined('TAOH_SITE_NAME_SLUG') ) define('TAOH_SITE_NAME_SLUG', 'YourSiteSlug' );
        // Add other overrides from env_sample.php as needed
        ?>
        ```
    *   **Main Configuration (`config.php`):** Review `config.php` for other constants that might need adjustment (e.g., `TAOH_SITE_TITLE`, `TAOH_SITE_DESCRIPTION`, `TAOH_SITE_LOGO`). Many of these can be derived from the `TAOH_SITE_NAME_SLUG` set in `env.php`, but direct customization in `config.php` might be necessary for finer control or if `env.php` is not used.
    *   **Club Environment (`club_env.php`):** This file seems to provide overrides for specific "club" deployments or different environments (e.g., `predash.tao.ai`, `ppapi.tao.ai`). For a standard setup, you might not need to modify this, but be aware of its existence if you encounter environment-specific issues.

4.  **Database Setup:**
    *   The project files do not include an explicit SQL dump or database migration script.
    *   The application interacts heavily with external TAO.ai APIs for data storage and retrieval.
    *   It's possible that a local database is used for minimal local data, session management, or caching, but the schema and setup process are not documented. You may need to inspect the code further or contact the developers for specific database setup instructions if a local database is required beyond what the application might create automatically.

5.  **Install PHP Dependencies (if applicable):**
    *   While a global `composer.json` is missing, some modules like `core/excel/` have their own. If you intend to use functionalities that rely on these modules, you might need to navigate to their directories and run `composer install`.
    ```bash
    # Example for the excel module
    # cd core/excel/
    # composer install
    ```
    A project-wide dependency management strategy is not apparent from the current file structure.

6.  **Configure Web Server:**
    *   **Apache:**
        Create or edit your `.htaccess` file in the project root with the following content (as suggested in `index.php`):
        ```apache
        RewriteEngine on
        RewriteCond %{REQUEST_FILENAME} !-d
        RewriteCond %{REQUEST_FILENAME} !-f
        RewriteRule . index.php [L]
        ```
        (Note: `index.php` also includes `RewriteRule . ".TAOH_SITE_URL_ROOT."/index.php [L]`. The simpler `RewriteRule . index.php [L]` is more common if the `.htaccess` is in the same directory as `index.php` which is the web server's document root for this site.) Ensure `AllowOverride All` is set in your Apache virtual host configuration for this directory to allow `.htaccess` to function.

    *   **Nginx:**
        Add the following location block to your Nginx site configuration (as suggested in `index.php`):
        ```nginx
        location / { # Or your specific base path if not root
            root /path/to/your/project_directory; # Adjust to your project's root
            try_files $uri $uri/ /index.php?$args;
        }

        location ~ \.php$ {
            include snippets/fastcgi-php.conf;
            fastcgi_pass unix:/var/run/php/php8.2-fpm.sock; # Adjust to your PHP-FPM socket
            fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
            include fastcgi_params;
        }
        ```
        (Note: The `index.php` suggested `try_files $uri $uri/ $temp/index.php?$args;` where `$temp` was the plugin path. The version above is more standard for a root deployment. Adjust `root` and `fastcgi_pass` according to your server setup.)

7.  **File/Folder Permissions:**
    *   Ensure the web server has write permissions to the `cache/` directory and its subdirectories (e.g., `cache/configs/`) as the application writes cache files and configuration there.
    ```bash
    chmod -R 775 cache/
    chown -R www-data:www-data cache/ # Replace www-data with your web server user/group
    ```

## Running the Application

1.  **Configure your Web Server's Document Root:**
    *   Ensure your Apache Virtual Host or Nginx server block is configured to point to the root directory of this project (e.g., where `index.php` and `config.php` are located).

2.  **Access via Web Browser:**
    *   Open your web browser and navigate to the URL you've configured for the project (e.g., `http://yourwebsite.com` or `http://localhost/your_project_directory`).

3.  **Initial Checks:**
    *   If the setup is correct, you should see the application's main page.
    *   If you encounter errors related to API secrets or configuration, revisit the "Installation & Setup" steps, particularly the creation and content of `env.php` and `config.php`.
    *   If you see errors about URL rewriting or "Page Not Found" for sub-pages, ensure your `.htaccess` (for Apache) or Nginx configuration is correctly implemented and active.

## Key Configuration Files

*   **`config.php`**: The main application configuration file. It defines most of the constants and operational parameters for the platform. Many settings are dependent on values set in `env.php`.
*   **`env.php`**: (Created by copying `env_sample.php`) Used for environment-specific settings, primarily your `TAOH_API_SECRET`, site URL, and site slug. This allows you to keep sensitive and environment-specific data separate from the main `config.php`.
*   **`club_env.php`**: Appears to contain overrides for specific "club" instances or alternative deployment environments (like development/staging vs. production).
*   **`.htaccess`**: (For Apache users) Contains URL rewriting rules necessary for the application's routing to work correctly.
*   **Nginx Site Configuration File**: (For Nginx users) The equivalent of `.htaccess`, where you define `location` blocks for URL rewriting and PHP processing.
*   **`readme.txt`**: The original text file that provided initial setup clues. The new `README.md` aims to supersede this with more comprehensive information.

## Project Structure (Brief Overview)

Here's a look at some of the key directories within the project:

*   **`app/`**: Contains the PHP code and templates for the main application modules:
    *   `app/asks/`: Handles the "Asks" or Q&A functionality.
    *   `app/events/`: Manages the events module.
    *   `app/jobs/`: Powers the job board features.
*   **`assets/`**: Static resources for the frontend:
    *   `assets/css/`: Stylesheets.
    *   `assets/js/`: JavaScript files.
    *   `assets/images/`: Image assets.
    *   `assets/fonts/`: Font files.
*   **`core/`**: Contains the core application logic, shared libraries, and utility functions.
    *   `core/main.php`: A central script included by `hires.php`, likely responsible for routing requests and loading the appropriate application modules or pages.
    *   `core/ajax.php`, `core/networkingajax.php`, `core/networkingredis_ajax.php`: Handle AJAX requests.
    *   It also includes libraries like TCPDF for PDF generation and FPDI.
*   **`cache/`**: Used for storing cached data and configurations. Requires write permissions by the web server.
*   **`keywords/`**: Contains files related to keyword management (e.g., `keywords.php`).
*   **`index.php`**: The primary entry point for all web requests. It sets up initial configurations and includes `hires.php`.
*   **`hires.php`**: A core bootstrapping script that handles session management, further configuration checks, and includes `core/main.php` to run the application.
*   **`config.php`**: Main configuration file.
*   **`function.php`**: Contains global functions used throughout the application.
*   **`helper_functions.php`**: Contains helper utility functions.

This is not an exhaustive list, but it covers some of the most important directories and files for understanding the application's layout.

## Contributing

Currently, there are no formal contribution guidelines established for this project. If you are interested in contributing, please consider reaching out to the project maintainers at `info@tao.ai` (the email address mentioned for API key requests) to discuss potential contributions.

Future contribution guidelines might include:

*   Coding standards and style guides.
*   Branching strategy (e.g., feature branches, Gitflow).
*   Testing requirements.
*   Pull request process.
