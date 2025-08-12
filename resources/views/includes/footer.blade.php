<!-- resources/views/includes/footer.blade.php -->

<footer class="footer" style="background-color: #000; color: #fff; padding: 20px; text-align: center;">
    <a href="https://www.migracion.gob.pa/">
        <img src="{{ asset('images/logo1.png') }}" alt="Logo" class="logo" style="max-width: 150px; margin-bottom: 20px;">
    </a>
    <div class="social-icons" style="margin-bottom: 10px;">
        <a href="https://www.facebook.com/migracionpanama" class="social-icon"><i class="fab fa-facebook-f icon2"></i></a>
        <a href="https://www.instagram.com/migracionpanama/" class="social-icon"><i class="fab fa-instagram icon2"></i></a>
        <a href="https://x.com/migracionpanama" class="social-icon"><i class="fab fa-twitter icon2"></i></a>
        <a href="https://www.youtube.com/@migracionpanama5695" class="social-icon"><i class="fab fa-youtube icon2"></i></a>
        <a href="https://www.tiktok.com/@migracionpanama" class="social-icon"><i class="fab fa-tiktok icon2"></i></a>
    </div>
    <p style="margin-bottom: 10px;">@migracionpanama</p>
    <p style="font-size: 12px;">Servicio Nacional de Migración © 2024 Todos los Derechos Reservados</p>
</footer>

<style>
    .icon2 {
        margin-right: 5px;
        padding: 10px;
        border-radius: 50%;
        background-color: #007BFF;
        color: white;
    }

    .social-icons {
        display: flex;
        justify-content: center;
        gap: 10px;
    }

    .social-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background-color: #007BFF;
        color: white;
        text-decoration: none;
        transition: background-color 0.3s;
    }

    .social-icon:hover {
        background-color: #0056b3;
    }

    .social-icon .icon {
        font-size: 20px;
    }
</style>
