<!DOCTYPE html>
<html>
<head>
    <title>Pomodoro Timer</title>
    <style>
        body {
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: green;
            color: white;
            font-size: 3rem;
        }
    </style>
</head>
<body>
    <script>
        function updateBackground() {
            const totalDuration = 1 * 60; // 30 minutes in seconds
            const startTime = localStorage.getItem('start_time');
            const currentTime = Math.floor(Date.now() / 1000);
            const elapsedSeconds = currentTime - (startTime || currentTime);
            const remainingTime = Math.max(totalDuration - elapsedSeconds, 0);
            const percentage = (totalDuration - remainingTime) / totalDuration;
            const colorValue = Math.floor(255 * percentage);
            const backgroundColor = `rgb(${colorValue}, ${255 - colorValue}, 0)`;
            document.body.style.backgroundColor = backgroundColor;

            if (remainingTime <= 0 ) {
                clearInterval(timerInterval);
                alert('Pomodoro timer is complete!');
            }
        }

        // Update the background every second
        const timerInterval = setInterval(updateBackground, 1000);

        // Initial setup for the timer
        if (!localStorage.getItem('start_time')) {
            localStorage.setItem('start_time', Math.floor(Date.now() / 1000));
        }

        // Initial call to start the timer
        updateBackground();
    </script>
</body>
</html>
