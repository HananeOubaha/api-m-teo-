<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Météo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #00b4db, #0083b0);
            min-height: 100vh;
            padding: 20px;
        }
        .weather-card {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
        }
        .weather-icon {
            width: 100px;
            height: 100px;
        }
        .search-box {
            max-width: 500px;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="search-box">
                    <h1 class="text-center text-white mb-4">Météo en Direct</h1>
                    <div class="input-group mb-3">
                        <input type="text" id="cityInput" class="form-control" placeholder="Entrez le nom d'une ville...">
                        <button class="btn btn-primary" id="searchBtn">Rechercher</button>
                    </div>
                </div>

                <div class="weather-card" id="currentWeather" style="display: none;">
                    <h2 class="text-center mb-4" id="cityName"></h2>
                    <div class="row">
                        <div class="col-md-6 text-center">
                            <img id="weatherIcon" class="weather-icon" src="" alt="Weather icon">
                            <h3 id="temperature"></h3>
                            <p id="description" class="lead"></p>
                        </div>
                        <div class="col-md-6">
                            <div class="mt-3">
                                <p><strong>Humidité:</strong> <span id="humidity"></span></p>
                                <p><strong>Vitesse du vent:</strong> <span id="windSpeed"></span></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="weather-card mt-4" id="forecast" style="display: none;">
                    <h3 class="text-center mb-4">Prévisions à court terme</h3>
                    <div class="row" id="forecastContainer">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('searchBtn').addEventListener('click', getWeather);
        document.getElementById('cityInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                getWeather();
            }
        });

        async function getWeather() {
            const city = document.getElementById('cityInput').value;
            if (!city) {
                alert('Veuillez entrer le nom d\'une ville');
                return;
            }

            try {
                console.log('Recherche de la météo pour:', city);
                
                // Afficher un indicateur de chargement
                document.getElementById('currentWeather').style.display = 'none';
                document.getElementById('forecast').style.display = 'none';
                
                // Récupérer la météo actuelle
                const currentWeatherResponse = await fetch(`/api/weather/current?city=${encodeURIComponent(city)}`);
                console.log('Statut de la réponse:', currentWeatherResponse.status);
                
                const currentWeatherData = await currentWeatherResponse.json();
                
                if (!currentWeatherResponse.ok) {
                    // Afficher le message d'erreur spécifique
                    throw new Error(currentWeatherData.error || `Erreur HTTP: ${currentWeatherResponse.status}`);
                }
                
                console.log('Données météo reçues:', currentWeatherData);

                // Afficher la météo actuelle
                document.getElementById('currentWeather').style.display = 'block';
                document.getElementById('cityName').textContent = currentWeatherData.name;
                document.getElementById('temperature').textContent = `${Math.round(currentWeatherData.main.temp)}°C`;
                document.getElementById('description').textContent = currentWeatherData.weather[0].description;
                document.getElementById('humidity').textContent = `${currentWeatherData.main.humidity}%`;
                document.getElementById('windSpeed').textContent = `${currentWeatherData.wind.speed} m/s`;
                document.getElementById('weatherIcon').src = `http://openweathermap.org/img/wn/${currentWeatherData.weather[0].icon}@2x.png`;

                // Récupérer les prévisions
                const forecastResponse = await fetch(`/api/weather/forecast?city=${encodeURIComponent(city)}`);
                console.log('Statut de la réponse prévisions:', forecastResponse.status);
                
                const forecastData = await forecastResponse.json();
                
                if (!forecastResponse.ok) {
                    // Afficher le message d'erreur spécifique
                    throw new Error(forecastData.error || `Erreur HTTP: ${forecastResponse.status}`);
                }
                
                console.log('Données prévisions reçues:', forecastData);

                // Afficher les prévisions
                displayForecast(forecastData);
            } catch (error) {
                console.error('Erreur détaillée:', error);
                alert(`Une erreur est survenue: ${error.message}`);
            }
        }

        function displayForecast(forecastData) {
            const forecastContainer = document.getElementById('forecastContainer');
            forecastContainer.innerHTML = '';
            document.getElementById('forecast').style.display = 'block';

            // Afficher les 3 prochaines heures
            const nextThreeHours = forecastData.list.slice(0, 3);
            
            nextThreeHours.forEach(hour => {
                const time = new Date(hour.dt * 1000).toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
                const temp = Math.round(hour.main.temp);
                const icon = hour.weather[0].icon;

                const forecastCard = document.createElement('div');
                forecastCard.className = 'col-md-4 text-center';
                forecastCard.innerHTML = `
                    <div class="p-3">
                        <h4>${time}</h4>
                        <img src="http://openweathermap.org/img/wn/${icon}.png" alt="Weather icon" class="weather-icon">
                        <p class="mb-0">${temp}°C</p>
                    </div>
                `;
                forecastContainer.appendChild(forecastCard);
            });
        }
    </script>
</body>
</html> 