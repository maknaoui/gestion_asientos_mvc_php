<?php
require_once './app/models/Database.php';
require_once './app/models/Evento.php';
require_once './app/models/Asiento.php';
require_once './app/models/Reserva.php';

class EventoController
{
    private $evento;
    private $reserva;
    private $asiento;

    public function __construct()
    {
        $this->evento = new Evento();
    }

    /**
     * Lista de eventos públicos
     */
    public function index()
    {
        $eventos = $this->evento->listarConFecha(); // ya trae sólo los no eliminados
        require './app/views/eventos/index.php';
    }

    /**
     * Detalle de un evento
     */
    public function detalle($id)
    {
        $evento = $this->evento->obtenerPorId($id);

        if (!$evento || $evento['eliminado']) {
            http_response_code(404);
            echo "<h1>404 Evento no encontrado</h1>";
            exit;
        }
        $this->reserva = new Reserva();
        $reservados = $this->reserva->obtenerAsientosReservados($evento['id']);

        $this->asiento = new Asiento();
        $asientos = $this->asiento->listarPorEvento($evento['id']);
        $max_personas = $evento['maxima_capacidad']- count($reservados);

        $max_left = 0;
        $max_top = 0;
        $ancho_asiento = 30;
        $alto_asiento = 30;
        $margen = 20;

        foreach ($asientos as $asiento) {
            if ($asiento['eliminado']) continue;

            $right = $asiento['left_pos'] + $ancho_asiento;
            $bottom = $asiento['top_pos'] + $alto_asiento;

            if ($right > $max_left) $max_left = $right;
            if ($bottom > $max_top) $max_top = $bottom;
        }

        $map_width = $max_left + $margen;
        $map_height = $max_top + $margen;

        $mensaje = '';
        $errores = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Requeridos
            $campos_requeridos = [
                'nombre', 'apellido', 'correo', 'telefono', 'rut', 'numero_personas', 'asientos_seleccionados'
            ];
            foreach ($campos_requeridos as $campo) {
                if (!isset($_POST[$campo])) {
                    $errores[] = "El campo {$campo} es obligatorio.";
                }
            }
            if (!empty($errores)) {
                require './app/views/eventos/detalle.php';
                exit;
            }

            $nombre = (string) trim($_POST['nombre']);
            $apellido= (string) trim($_POST['apellido']);
            $correo= (string) trim($_POST['correo']);
            $telefono = (string) trim($_POST['telefono']);
            $rut= (string) trim($_POST['rut']);
            if(isset($_POST['fecha_nacimiento']) && preg_match('/^\d{1,2}\/\d{1,2}\/\d{4}$/', $_POST['fecha_nacimiento'])) {
                $dt = DateTime::createFromFormat('d/m/Y', $_POST['fecha_nacimiento']);
                $fecha_nacimiento = $dt->format('Y-m-d');;
            } else {
                $fecha_nacimiento = null;
            }
            $numero_personas  = (int) $_POST['numero_personas'];

            $asientos_seleccionados = explode(",",$_POST['asientos_seleccionados']);

            if (empty($nombre)) {
                $errores[] = 'El nombre es obligatorio.';
            }
            if (empty($apellido)) {
                $errores[] = 'El apellido es obligatorio.';
            }
            if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
                $errores[] = 'El correo electrónico no es válido.';
            }
            if (empty($telefono)) {
                $errores[] = 'El teléfono es obligatorio.';
            }
            if (!$this->validarRut($rut)) {
                $errores[] = 'El RUT no es válido.';
            }
            if ($numero_personas < 1) {
                $errores[] = 'El número de personas debe ser al menos 1.';
            }
            if(!count($asientos_seleccionados)){
                $errores[] = 'Debe seleccionar al menos un asiento.';
            } elseif (count($asientos_seleccionados) > $max_personas) {
                $errores[] = 'No puede seleccionar más asientos que el número de personas.';
            } elseif(count($asientos_seleccionados)!= $numero_personas) {
                $errores[] = 'El número de asientos seleccionados debe coincidir con el número de personas.';
            } else {
                foreach ($asientos_seleccionados as $asiento_id) {
                    if (!is_numeric($asiento_id) || $asiento_id <= 0) {
                        $errores[] = 'Asiento seleccionado no válido.';
                        break;
                    }else if (in_array($asiento_id, $reservados)) {
                        $codigo_asiento = $asiento_id;
                        foreach ($asientos as $a) {
                            if ($a['id'] == $asiento_id) {
                                $codigo_asiento = $a['codigo'];
                                break;
                            }
                        }
                        $errores[] = 'El asiento ' . $codigo_asiento . ' ya está reservado.';
                        break;
                    }
                }
            }

            if (empty($errores)) {
                $data = [
                    'nombre'           => $nombre,
                    'apellido'         => $apellido,
                    'correo'           => $correo,
                    'fecha_nacimiento' => $fecha_nacimiento,
                    'telefono'         => $telefono,
                    'rut'              => $rut,
                    'numero_personas'  => $numero_personas,
                    'evento_id'        => $id
                ];

                $reserva_id = $this->reserva->crear($data,$asientos_seleccionados );


                if ($reserva_id) {
                    $mensaje = '¡Reserva realizada con éxito!';
                    $reservados = $this->reserva->obtenerAsientosReservados($evento['id']);
                    unset($nombre, $apellido, $correo, $telefono, $rut, $fecha_nacimiento, $numero_personas,$asientos_seleccionados);
                } else {
                    $errores[] = 'Ocurrió un error al guardar la reserva.';
                }
            }
        }

        require './app/views/eventos/detalle.php';
    }
    private function validarRut($rut)
    {
        $rut = preg_replace('/[^0-9kK]/', '', $rut);

        if (strlen($rut) < 2) {
            return false;
        }

        $numero = substr($rut, 0, -1);
        $dv = strtolower(substr($rut, -1));

        $suma = 0;
        $multiplo = 2;

        for ($i = strlen($numero) - 1; $i >= 0; $i--) {
            $suma += $numero[$i] * $multiplo;
            $multiplo = ($multiplo == 7) ? 2 : $multiplo + 1;
        }

        $resto = $suma % 11;
        $resultado = 11 - $resto;

        if ($resultado == 11) {
            $digito = '0';
        } elseif ($resultado == 10) {
            $digito = 'k';
        } else {
            $digito = (string) $resultado;
        }

        return $digito === $dv;
    }


}
