function updateTime() {
    const now = new Date();
    const timeString = now.toLocaleTimeString('en-US', {
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
        hour12: true
    });
    const timeEl = document.getElementById('currentTime');
    if (timeEl) timeEl.textContent = timeString;
}
updateTime();
setInterval(updateTime, 1000);

// Optional: check chart data
if (window.chartData) {
    console.log("Chart data loaded:", window.chartData);
}