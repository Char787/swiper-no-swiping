<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Swiper, No Swiping!</title>
    <script src="https://cdn.jsdelivr.net/npm/moment"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/date-fns"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns@2.0.0"></script>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            background-color: #eef1f7;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            color: #333;
            overflow-x: hidden;
        }
        header, footer {
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white;
            text-align: center;
            padding: 20px 0;
            font-size: 24px;
            font-weight: bold;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        .container {
            width: 90%;
            max-width: 800px;
            margin: 30px auto;
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
            text-align: center;
            overflow-x: auto;
        }
        h2 {
            color: #0056b3;
            margin-bottom: 15px;
        }
        canvas {
            width: 100% !important;
            height: 400px !important;
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 10px;
            background-color: #f8f9fa;
        }
        .description {
            margin-top: 20px;
            font-size: 16px;
            color: #555;
            background: #f1f3f6;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
        }
    </style>
</head>
<body>
    <header>
        <h1>Swiper, No Swiping!</h1>
    </header>
    <div class="container">
        <h2>Motion Detector Log</h2>
        <canvas id="motionChart"></canvas>
        <p class="description">
            Welcome to Swiper, No Swiping! This webpage links to the SNS annoyance and security device that tracks the security of your belongings in real-time. Need to leave your backpack, bike, or other important belonging unattended in a public place? Simply insert or affix the Swiper, No Swiping device and you're all set!<br><br>
            The SNS device constantly monitors for movement. If it detects that it moved, ie someone has picked up your important belonging, it will buzz loudly and uncontrollably, frightening the culprit until they leave your bag alone. Not only that, but it will send you an email notification so you can come back to check on your belongings. What's more, you can access the web server to see the history of your device's movement. If it only moved for a brief few seconds, perhaps it was merely pushed - but if the convenient graph indicates that it moved for an extended period of time, you know someone is trying to steal your stuff.<br><br>
            How does it work? The Swiper, No Swiping device operates off of an ESP8266 microcontroller, MPU6050 gyroscope, and buzzer. When the gyroscope detects excessive movement, it will sound the buzzer and send an HTTP POST request via WiFi to the web server. The web server will receive the movement log and post it onto a database while also sending the email notification to the user such long as it hasn't been less than 10 seconds since the last notification - we want to keep the user informed of all movements, but also not spam them too much. Finally, when any user accesses the web application, they will see the movement history of the device accessed via the database. When not in use, the SNS device can simply be powered off.<br><br>
            This project was inspired by the fright of leaving your expensive personal belongings at a hackathon - or airport, or train station, or anywhere for that matter. Want to leave your bag to claim a seat but don't want it stolen? Simply place the SNS device in your backpack and wander off in peace, knowing that the moment anyone tries to move your bag, you will instantly be notified and the fright of the sound will scare the culprit off.<br><br>
            *Post-Tree Hacks 2025: Nothing on the graph? That's cause there's no hardware device actively writing to the database anymore :(
        </p>
        <iframe width="560" height="315" src="https://www.youtube.com/embed/iazdEkrBMbU?si=3wIz91NfF4d_q-X9" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
    </div>
    <footer>
        <a href="https://me.char787.com" target="_blank" style="color: white; text-decoration: none"><p>me.char787.com</p></a>
    </footer>

    <script>
        const ctx = document.getElementById('motionChart').getContext('2d');
        const motionData = {
            labels: [],
            datasets: [{
                label: 'Motion Detected',
                data: [],
                borderColor: '#dc3545',
                backgroundColor: 'rgba(220, 53, 69, 0.2)',
                borderWidth: 2,
                pointRadius: 4,
                pointBackgroundColor: '#dc3545',
                stepped: true
            }]
        };

        const motionChart = new Chart(ctx, {
            type: 'line',
            data: motionData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        type: 'time',
                        time: {
                            unit: 'second',
                            tooltipFormat: 'HH:mm:ss',
                            displayFormats: { second: 'HH:mm:ss' }
                        },
                        ticks: { source: 'auto' }
                    },
                    y: {
                        type: 'linear',
                        ticks: {
                            callback: function(value) { return value === 1 ? 'Y' : 'N'; },
                            stepSize: 1,
                            min: 0,
                            max: 1
                        }
                    }
                },
                plugins: {
                    zoom: {
                        pan: {
                            enabled: true,
                            mode: 'x'
                        },
                        zoom: {
                            enabled: false
                        }
                    }
                }
            }
        });

        let lastTimestamp = null;

        function fetchMotionData() {
            fetch('read_motion_log.php')
                .then(response => response.json())
                .then(data => {
                    if (!data || !Array.isArray(data.log) || data.log.length === 0) {
                        console.error('Invalid or empty data received');
                        return;
                    }

                    motionData.labels = [];
                    motionData.datasets[0].data = [];

                    const now = new Date();
                    lastTimestamp = null;
                    data.log.forEach(entry => {
                        const entryTime = new Date(entry.timestamp + 'Z');
                        if (lastTimestamp && (entryTime - lastTimestamp) > 8000) {
                            const autoNTime = new Date(lastTimestamp.getTime() + 8000);
                            motionData.labels.push(autoNTime);
                            motionData.datasets[0].data.push(0);
                        }
                        motionData.labels.push(entryTime);
                        motionData.datasets[0].data.push(1);
                        lastTimestamp = entryTime;
                    });

                    if (lastTimestamp && (now - lastTimestamp) > 8000) {
                        const autoNTime = new Date(lastTimestamp.getTime() + 8000);
                        motionData.labels.push(autoNTime);
                        motionData.datasets[0].data.push(0);
                    }

                    motionChart.options.scales.x.min = new Date(now.getTime() - 30000);
                    motionChart.options.scales.x.max = new Date(now.getTime());
                    motionChart.update();
                })
                .catch(error => console.error('Error fetching motion data:', error));
        }

        setInterval(fetchMotionData, 1000);
    </script>
</body>
</html>
