<?php
// Create preloader component that will be included in the header
?>
<div id="preloader">
    <div class="loader">
        <svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" viewBox="0 0 50 50">
            <circle cx="25" cy="25" r="20" fill="none" stroke="#3498db" stroke-width="3" />
            <path id="progress" fill="none" stroke="#2ecc71" stroke-width="3" stroke-linecap="round" stroke-dasharray="94.2" stroke-dashoffset="94.2"
                  d="M25 5 A20 20 0 0 1 45 25">
                <animate attributeName="stroke-dashoffset" values="94.2;0" dur="1.5s" repeatCount="indefinite" />
            </path>
        </svg>
        <p class="mt-2">Future Hope Foundation</p>
    </div>
</div>

<style>
#preloader {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255, 255, 255, 0.98);
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: opacity 0.5s ease;
}

#preloader .loader {
    text-align: center;
}

#preloader p {
    font-family: 'Montserrat', sans-serif;
    font-weight: 600;
    color: #3498db;
    margin-top: 10px;
}

#preloader circle {
    animation: pulse 2s ease-in-out infinite;
}

@keyframes pulse {
    0% { transform: scale(0.95); opacity: 0.7; }
    50% { transform: scale(1); opacity: 1; }
    100% { transform: scale(0.95); opacity: 0.7; }
}
</style>
