<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
?>


<?php
include 'ingresoEgresoLogica.php';
$getPastor = getPastores();
$getCategoria = getCategorias();
$getFiliales = getFiliales();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <title>Ingresos/Egresos</title>
</head>
<body>
    <div class="container">
        <div class="row">
            <form action="" class="container mt-4">

                <div class="card shadow-sm p-4">
                    <h4 class="mb-4 text-primary">Registrar Transacción</h4>

                    <!-- Fecha -->
                    <div class="mb-3">
                        <label for="fecha" class="form-label fw-semibold">Fecha</label>
                        <input type="date" id="fecha_hoy" name="fecha" class="form-control">
                    </div>

                    <!-- Tipo y Filial -->
                    <div class="row mb-3">
                        <div class="col-md-6 mb-3">
                            <label for="tipo" class="form-label fw-semibold">Tipo de Transacción</label>
                            <select name="tipo" id="tipo" class="form-select">
                                <option value="INGRESO">Ingreso</option>
                                <option value="EGRESO">Egreso</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="filial" class="form-label fw-semibold">Filial</label>
                            <select name="filial" id="filial" class="form-select">
                                <?php
                                foreach($getFiliales as $filial){
                                ?>
                                <option value="<?= $filial['id_filial']?>"><?= $filial['nombre']?></option>
                                <?php
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <!-- Monto -->
                    <div class="mb-3">
                        <label for="monto" class="form-label fw-semibold">Monto</label>
                        <input type="number" id="monto" name="monto" class="form-control" placeholder="0.00">
                    </div>

                    <!-- Categoria y Pastor -->
                    <div class="row mb-3">
                        <div class="col-md-6 mb-3">
                            <label for="categoria" class="form-label fw-semibold">Categoría</label>
                            <select name="categoria" id="categoria" class="form-select">
                                <?php
                                foreach($getCategoria as $categoria){
                                ?>
                                <option value="<?= $categoria['id_categoria']?>"><?= $categoria['nombre']?></option>
                                <?php
                                }
                                ?>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="pastor" class="form-label fw-semibold">Pastor</label>
                            <select name="pastor" id="pastor" class="form-select">
                                <?php
                                foreach($getPastor as $pastor){
                                ?>
                                <option value="<?= $pastor['id_pastor']?>"><?= $pastor['nombre']?></option>
                                <?php
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="" class="form-label fw-semibold">Descripcion</label>
                        <input type="text" class="form-control">
                    </div>

                    <!-- Botón -->
                    <div class="text-end">
                        <button class="btn btn-primary px-4">Guardar</button>
                    </div>
                </div>

            </form>

        </div>
    </div>

</body>
</html>