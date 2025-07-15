<?php 
require './app/utils.php';
include './app/views/templates/header.php';
$TEST_RUT="";
$TEST_NOMBRE="";
$TEST_APELLIDO="";
$TEST_CORREO="";
$TEST_TELEFONO="";
$TEST_FECHA_NACIMIENTO="";
$TEST_NUMERO_PERSONAS=1;
$TEST_ASIENTOS_SELECCIONADOS=array();
$TEST_ASIENTOS_SELECCIONADOS_TXT="";
$campos_requeridos = [
    'nombre', 'apellido', 'correo', 'telefono', 'rut', 'fecha_nacimiento', 'numero_personas', 'asientos_seleccionados'
];
foreach ($campos_requeridos as $campo) {
    if (!empty($$campo)) {
        ${"TEST_" . strtoupper($campo)}=$$campo;
    }
}
if(count($TEST_ASIENTOS_SELECCIONADOS)>0) {
    $TEST_ASIENTOS_SELECCIONADOS_TXT=implode(",", $TEST_ASIENTOS_SELECCIONADOS);
}
?>

<h2 class="heading_b uk-margin-bottom"><a href="<?= $PATH ?>" title="Volver al inicio" class="go_home"><i class="material-icons md-24">home</i></a> <span class="separator">/</span> <?= htmlspecialchars($evento['nombre']) ?></h2>
<?php if (!empty($errores)): ?>
<div class="uk-alert uk-alert-danger">
    <ul>
        <?php foreach ($errores as $error): ?>
            <li><?= htmlspecialchars($error) ?></li>
        <?php endforeach; ?>
    </ul>
</div>
<?php elseif (!empty($mensaje)): ?>
<div class="uk-alert uk-alert-success">
    <?= htmlspecialchars($mensaje) ?>
</div>
<?php endif; ?>

<div class="md-card uk-margin-large-bottom">
    <div class="md-card-content">

        <!-- Detalles del evento -->
        <div class="uk-grid">
            <div class="uk-width-medium-1-3 event_details">
                <?php if (!empty($evento['imagen'])): ?>
                    <img src="<?= $PATH ?><?= htmlspecialchars($evento['imagen']) ?>" alt="<?= htmlspecialchars($evento['nombre']) ?>" class="blog_list_teaser_image uk-width-1-1">
                <?php endif; ?>
                <p><strong>Fecha:</strong> <?= date("d/m/Y H:i", strtotime($evento['fecha_evento'])) ?></p>
                <p><?= nl2br(htmlspecialchars($evento['descripcion'])) ?></p>
            </div>
            <div class="uk-width-medium-2-3 event_booking">
                <form class="uk-form-stacked" id="wizard_advanced_form" method="post">
                    <div id="wizard_advanced" data-uk-observe>

                        <!-- Paso 1 -->
                        <h3>Datos personales</h3>
                        <section>
                            <h2 class="heading_a">Datos personales</h2>
                            <hr class="md-hr"/>
                            
                            <div class="uk-form-row parsley-row">
                                <label>RUT <span class="req">*</span></label>
                                <input type="text" name="rut" required class="md-input rut" data-parsley-rut-message="Rut no valido" data-parsley-trigger="change" pattern="[0-9]{1,2}.[0-9]{3}.[0-9]{3}-[0-9kK]{1}" value="<?= $TEST_RUT ?>">
                            </div>
                            <div class="uk-form-row">
                                <div class="uk-grid">
                                    <div class="uk-width-medium-1-2 parsley-row">
                                        <label>Nombre <span class="req">*</span></label>
                                        <input type="text" name="nombre" data-parsley-trigger="change" value="<?= $TEST_NOMBRE ?>" required class="md-input">
                                    </div>
                                    <div class="uk-width-medium-1-2 parsley-row">
                                        <label>Apellido</label>
                                        <input type="text" name="apellido" value="<?= $TEST_APELLIDO ?>" class="md-input">
                                    </div>
                                </div>
                            </div>
                            <div class="uk-form-row parsley-row">
                                <div class="uk-input-group">
                                    <span class="uk-input-group-addon">
                                        <i class="material-icons">&#xE0BE;</i>
                                    </span>
                                    <label for="wizard_email">Correo <span class="req">*</span></label>
                                    <input type="email" class="md-input" data-parsley-trigger="change" name="correo" id="wizard_email" required value="<?= $TEST_CORREO ?>" />
                                </div>
                            </div>
                            <div class="uk-form-row parsley-row">
                                <div class="uk-input-group">
                                    <span class="uk-input-group-addon">
                                        <i class="material-icons">&#xE0CD;</i>
                                    </span>
                                    <label for="telefono">Teléfono</label>
                                    <input type="text" class="md-input telefono" name="telefono" id="telefono" value="<?= $TEST_TELEFONO ?>" />
                                </div>
                            </div>
                            <div class="uk-form-row parsley-row">
                                <div class="uk-input-group">
                                    <span class="uk-input-group-addon">
                                        <i class="material-icons">today</i>
                                    </span>
                                    <label>Fecha de nacimiento</label>
                                    <input type="text" name="fecha_nacimiento" class="md-input" data-uk-datepicker="{format:'DD/MM/YYYY'}" value="<?= $TEST_FECHA_NACIMIENTO ?>">
                                </div>
                            </div>
                            <div class="uk-form-row parsley-row">
                                <div class="uk-input-group">
                                    <span class="uk-input-group-addon">
                                        <i class="material-icons">user</i>
                                    </span>
                                    <label>Número de personas <span class="req">*</span></label>
                                    <input type="number" name="numero_personas" id="numero_personas" class="md-input" min="1" value="<?= $TEST_NUMERO_PERSONAS ?>" required>
                                </div>
                            </div>
                        </section>

                        <!-- Paso 2 -->
                        <h3>Reserva</h3>
                        <section>
                            <h2 class="heading_a">Selecciona tus asientos</h2>
                            <hr class="md-hr" />

                            <div class="uk-alert uk-alert-info" id="instrucciones-1">
                                Haz clic en los asientos para seleccionarlos. Puedes elegir hasta <strong><span id="max_personas"><?= $TEST_NUMERO_PERSONAS ?? 1 ?></span></strong> asientos.
                            </div>
                            <div class="uk-alert uk-alert-info">
                            Quedan <span id="quedan"><?= $max_personas ?></span> desocupados.
                            </div>
                            <div class="uk-grid">
                                <div class="uk-width-1-1">
                                    <div id="mapa_wrapper" style="overflow:auto;width:100%;max-height:400px;border:1px solid #ccc;">
                                        <div id="mapa_asientos" style="position: relative; width:<?= $map_width ?>px;height:<?= $map_height ?>px; border: 1px solid #ccc; margin: 0 auto; background: #f9f9f9;" data-max="<?= $max_personas ?>" data-max-select="<?= $TEST_NUMERO_PERSONAS ?? 1 ?>">
                                            <?php $reservados = $reservados ?? []; ?>
                                            <?php foreach ($asientos as $asiento): ?>
                                                <?php if ($asiento['eliminado']) continue; ?>
                                                <div 
                                                    class="asiento <?= $asiento['discapacitado'] ? 'discapacitado' : '' ?>  <?= in_array($asiento['id'], $reservados) ? 'ocupado' : '' ?> <?= in_array($asiento["id"],$TEST_ASIENTOS_SELECCIONADOS)? "seleccionado" : "" ?> <?= $asiento['estado']=="deshabilitado" ? 'deshabilitado' : '' ?>"
                                                    data-id="<?= $asiento['id'] ?>"
                                                    
                                                    style="
                                                        position: absolute;
                                                        top: <?= $asiento['top_pos'] ?>px;
                                                        left: <?= $asiento['left_pos'] ?>px;
                                                        
                                                    ">
                                                    <span class="asiento_codigo" title="<?= htmlspecialchars($asiento['codigo']) ?>: <?= $asiento['discapacitado'] ? '|discapacitado|' : '' ?> <?= in_array($asiento['id'], $reservados) ? '|ocupado|' : '|disponible|' ?> <?= in_array($asiento["id"],$TEST_ASIENTOS_SELECCIONADOS)? "|seleccionado|" : "" ?>"><?= htmlspecialchars($asiento['codigo']) ?><span>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                    <div>
                                        <br>
                                        <hr>
                                        <h3>Leyenda</h3>
                                        <div class="uk-grid">
                                        <div class="leyenda-asientos uk-grid">
                                            <div class="item uk-width-1-5">
                                                <div class="asiento_muestra"><span class="asiento_codigo" title="A: |Disponible|">A<span></div> Disponible
                                            </div>
                                            <div class="item uk-width-1-5">
                                                <div class="asiento_muestra discapacitado"><span class="asiento_codigo" title="A: |Discapacitado|">A<span></div> Discapacitado
                                            </div>
                                            <div class="item uk-width-1-5">
                                                <div class="asiento_muestra seleccionado"><span class="asiento_codigo" title="A: |Seleccionado|">A<span></div> Seleccionado
                                            </div>
                                            <div class="item uk-width-1-5">
                                                <div class="asiento_muestra ocupado"><span class="asiento_codigo" title="A: |Ocupado|">A<span></div> Ocupado
                                            </div>
                                            <div class="item uk-width-1-5">
                                                <div class="asiento_muestra deshabilitado"><span class="asiento_codigo" title="A: |Deshabilitado|">A<span></div> Deshabilitado
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <input type="hidden" name="asientos_seleccionados" id="asientos_seleccionados" value="<?= htmlspecialchars($TEST_ASIENTOS_SELECCIONADOS_TXT) ?>">
                        </section>


                        <!-- Paso 3 -->
                        <h3>Confirmar</h3>
                        <section>
                            <h2 class="heading_a">Confirmar reserva</h2>
                            <hr class="md-hr"/>
                            <p>Al finalizar, tu reserva quedará registrada y recibirás un correo de confirmación con el link de pago.</p>
                        </section>

                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include './app/views/templates/footer.php'; ?>
