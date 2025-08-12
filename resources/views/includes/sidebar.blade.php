<!-- resources/views/includes/sidebar.blade.php -->
<nav class="sidebar" style="background-color: #333; color: white; position: fixed; height: 100%; left: -250px; top: 0; width: 250px; padding-top: 20px; transition: left 0.3s ease-out;">
    <ul class="sidebar-nav">
        <li><a href="#"><i class="fas fa-home icon"></i> Inicio</a></li>
        <li><a href="#"><i class="fas fa-user icon"></i> Perfil</a></li>
        <li><a href="#"><i class="fas fa-cog icon"></i> Configuración</a></li>
        <li><a href="#" onclick="toggleMenu()"><i class="fas fa-sign-out-alt icon"></i> Salir</a></li>
    </ul>
</nav>

<style>
    /* Estilos para el sidebar */
    .sidebar {
        z-index: 1000;
    }

    .sidebar-nav {
        list-style-type: none;
        padding: 0;
        margin: 0;
    }

    .sidebar-nav li {
        padding: 10px;
        transition: background-color 0.3s;
    }

    .sidebar-nav li a {
        display: flex;
        align-items: center;
        color: white;
        text-decoration: none;
        padding: 10px;
    }

    .sidebar-nav li a:hover {
        background-color: #555;
    }

    .sidebar-nav li a i {
        margin-right: 10px;
        padding: 10px;
        border-radius: 50%;
        background-color: #007BFF;
        color: white;
    }
</style>
