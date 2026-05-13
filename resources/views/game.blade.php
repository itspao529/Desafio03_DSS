<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>MasterMind Express</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; font-family: 'Segoe UI', sans-serif; }
        .color-preview { width: 30px; height: 30px; border-radius: 50%; display: inline-block; border: 1px solid #ddd; }
        .color-rojo { background-color: #ff4d4d; } .color-verde { background-color: #2ecc71; }
        .color-azul { background-color: #3498db; } .color-amarillo { background-color: #f1c40f; }
        .color-naranja { background-color: #e67e22; } .color-violeta { background-color: #9b59b6; }
        .attempt-card { animation: fadeIn 0.5s; margin-bottom: 10px; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-md-8">
                <div class="card shadow-sm p-4 mb-4">
                    <h2 class="mb-4">MasterMind Express</h2>
                    <div class="mb-3">
                        <label class="form-label">Tu Nombre:</label>
                        <input type="text" id="playerName" class="form-control w-50" placeholder="Ingresa tu nombre" required>
                    </div>
                    <div class="d-flex gap-2 mb-4" id="selectors">
                        @for($i=0; $i<4; $i++)
                            <select class="form-select c" onchange="updatePreview()">
                                <option value="rojo">Rojo</option>
                                <option value="verde">Verde</option>
                                <option value="azul">Azul</option>
                                <option value="amarillo">Amarillo</option>
                                <option value="naranja">Naranja</option>
                                <option value="violeta">Violeta</option>
                            </select>
                        @endfor
                    </div>
                    <div class="mb-3">
                        <span id="preview-dots"></span>
                    </div>
                    <div class="d-flex gap-2">
                        <button onclick="sendGuess()" class="btn btn-primary px-4">Enviar Intento</button>
                        <button onclick="restartGame()" class="btn btn-outline-secondary">Reiniciar</button>
                    </div>
                </div>
                <div class="card shadow-sm p-4">
                    <h4>Historial e Intentos (<span id="tries">8</span> restantes)</h4>
                    <div id="hist" class="mt-3"></div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm p-4 border-primary">
                    <h4 class="text-primary">🏆 Ranking Top 5</h4>
                    <hr>
                    <div id="ranking">Cargando...</div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const token = document.querySelector('meta[name="csrf-token"]').content;

        function updatePreview() {
            const dots = Array.from(document.querySelectorAll('.c')).map(s => `<span class="color-preview color-${s.value} me-1"></span>`).join('');
            document.getElementById('preview-dots').innerHTML = dots;
        }

        async function sendGuess() {
            const name = document.getElementById('playerName').value;
            if(!name) return alert("Por favor ingresa tu nombre");
            
            const cols = Array.from(document.querySelectorAll('.c')).map(s => s.value);
            const res = await fetch('/guess', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token },
                body: JSON.stringify({ colors: cols, player_name: name })
            });
            
            const d = await res.json();
            if(res.status === 400) return alert(d.message);

            const div = document.createElement('div');
            div.className = 'attempt-card p-2 border-bottom d-flex justify-content-between align-items-center';
            div.innerHTML = `<div>${cols.map(c => `<span class="color-preview color-${c}"></span>`).join('')}</div>
                             <div><span class="badge bg-success">${d.exact} Exactos</span> <span class="badge bg-warning text-dark">${d.color} Color</span></div>`;
            document.getElementById('hist').prepend(div);
            document.getElementById('tries').innerText = d.attempts_left;

            if(d.gameOver) {
                alert(d.win ? "¡Felicidades Paola, ganaste!" : "Fin del juego. ¡Sigue intentando!");
                loadRanking();
            }
        }

        async function loadRanking() {
            const res = await fetch('/leaderboard');
            const data = await res.json();
            document.getElementById('ranking').innerHTML = data.map((r, i) => 
                `<div class="mb-2"><strong>${i+1}. ${r.player_name}</strong>: ${r.score} pts</div>`
            ).join('') || "Aún no hay puntuaciones.";
        }

        async function restartGame() {
            await fetch('/restart', { method: 'POST', headers: { 'X-CSRF-TOKEN': token } });
            location.reload();
        }

        updatePreview();
        loadRanking();
        setInterval(loadRanking, 30000);
    </script>
</body>
</html>
