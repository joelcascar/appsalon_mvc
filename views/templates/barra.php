<div class="barra">
    <p>Hola: <?php echo $nombre ?? ""; ?></p>
    <a href="/index.php/logout" class="boton">Cerrar Sesión</a>
</div>

<?php if(isset($_SESSION["admin"])){ ?>
    <div class="barra-servicios">
        <a href="/index.php/admin" class="boton">Ver Citas</a>
        <a href="/index.php/servicios" class="boton">Ver Servicios</a>
        <a href="/index.php/servicios/crear" class="boton">Nuevo Servicio</a>
    </div>
<?php } ?>