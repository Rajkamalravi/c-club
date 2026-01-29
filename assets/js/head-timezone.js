        let userTimezone = Intl.DateTimeFormat().resolvedOptions().timeZone;

        // Get current timezone from the cookie
        let cookies = document.cookie.split("; ");
        let storedTimeZone = cookies.find(row => row.startsWith("client_time_zone="))?.split("=")[1];

        // Only update the cookie if the timezone has changed or doesn't exist
        if (!storedTimeZone || storedTimeZone !== userTimezone) {
            document.cookie = "client_time_zone=" + userTimezone + "; path=/; max-age=" + (60 * 60 * 24);
        }
