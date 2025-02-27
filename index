<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Cyberpunk Trading Background</title>
  <style>
    /* Ensure the canvas covers the entire background */
    html, body {
      margin: 0;
      padding: 0;
      overflow: hidden;
      background: #000;
      height: 100%;
    }
    #canvas {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      z-index: -1; /* behind other content */
    }
  </style>
</head>
<body>
  <canvas id="canvas"></canvas>
  <script>
    const canvas = document.getElementById('canvas');
    const ctx = canvas.getContext('2d');
    canvas.width = window.innerWidth;
    canvas.height = window.innerHeight;

    window.addEventListener('resize', () => {
      canvas.width = window.innerWidth;
      canvas.height = window.innerHeight;
    });

    // Sample stock data for dynamic tickers
    const stocks = ['AAPL', 'GOOG', 'AMZN', 'MSFT', 'TSLA', 'FB', 'NFLX'];
    const tickers = [];
    for (let i = 0; i < 50; i++) {
      tickers.push({
        x: Math.random() * canvas.width,
        y: Math.random() * canvas.height,
        symbol: stocks[Math.floor(Math.random() * stocks.length)],
        price: (Math.random() * 1000).toFixed(2),
        alpha: Math.random() * 0.5 + 0.3, // slight transparency
        speed: 0.5 + Math.random() * 2,
        phase: Math.random() * Math.PI * 2
      });
    }

    // Draw a stock ticker text with a subtle glitch effect
    function drawTicker(t) {
      ctx.save();
      ctx.globalAlpha = t.alpha;
      ctx.fillStyle = '#fff';
      ctx.font = '16px monospace';
      // Glitch: random offset for a split second
      const glitchOffset = 1 * (Math.random() - 0.5);
      ctx.fillText(`${t.symbol} ${t.price}`, t.x + glitchOffset, t.y + glitchOffset);
      ctx.restore();
    }

    // Update ticker positions and simulate price fluctuations
    function updateTickers() {
      tickers.forEach(t => {
        t.x += t.speed * Math.cos(t.phase) * 0.5;
        t.y += t.speed * Math.sin(t.phase) * 0.5;
        // Simulate price change
        t.price = (parseFloat(t.price) + (Math.random() - 0.5) * 2).toFixed(2);
        // Loop around screen edges
        if (t.x > canvas.width) t.x = 0;
        if (t.x < 0) t.x = canvas.width;
        if (t.y > canvas.height) t.y = 0;
        if (t.y < 0) t.y = canvas.height;
      });
    }

    // Draw subtle circuit-like patterns as background details
    function drawCircuitPattern() {
      ctx.save();
      ctx.strokeStyle = 'rgba(100, 100, 255, 0.1)';
      ctx.lineWidth = 1;
      for (let i = 0; i < 20; i++) {
        ctx.beginPath();
        ctx.moveTo(Math.random() * canvas.width, Math.random() * canvas.height);
        ctx.lineTo(Math.random() * canvas.width, Math.random() * canvas.height);
        ctx.stroke();
      }
      ctx.restore();
    }

    // Add a holographic overlay with a subtle gradient
    function drawHolographicOverlay() {
      const grad = ctx.createLinearGradient(0, 0, canvas.width, canvas.height);
      grad.addColorStop(0, 'rgba(255, 0, 255, 0.1)');
      grad.addColorStop(0.5, 'rgba(0, 255, 255, 0.1)');
      grad.addColorStop(1, 'rgba(255, 255, 0, 0.1)');
      ctx.fillStyle = grad;
      ctx.fillRect(0, 0, canvas.width, canvas.height);
    }

    // Main animation loop
    function animate() {
      ctx.clearRect(0, 0, canvas.width, canvas.height);

      // Create a subtle animated gradient background that shifts over time
      const time = Date.now() * 0.00005;
      const r = Math.floor(50 + 50 * Math.sin(time));
      const b = Math.floor(50 + 50 * Math.cos(time));
      ctx.fillStyle = `rgb(${r}, 0, ${b})`;
      ctx.fillRect(0, 0, canvas.width, canvas.height);

      // Render circuit patterns
      drawCircuitPattern();

      // Render stock tickers
      tickers.forEach(t => drawTicker(t));
      updateTickers();

      // Overlay the holographic gradient
      drawHolographicOverlay();

      requestAnimationFrame(animate);
    }

    animate();
  </script>
</body>
</html>
