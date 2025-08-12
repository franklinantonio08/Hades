<!-- resources/views/includes/header.blade.php -->
<header style="background-color: black; color: white; padding: 10px;">
    <div class="container" style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <button class="menu-toggle-btn" style="background: none; border: none; cursor: pointer;" onclick="toggleMenu()">
                <i class="fas fa-bars" style="font-size: 24px; color: white;"></i>
            </button>
        </div>
        <div style="flex: 1; text-align: center;"> <!-- Centrar la imagen -->
            <img src="{{ asset('images/logo1.png') }}" alt="Logo" style="max-width: 800px; height: auto;"> <!-- Aumentar el tamaño -->
        </div>
    </div>
</header>
