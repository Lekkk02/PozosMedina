<?php 
    session_start();
    $mediciones = [];
    $psiArray = [];
    $pozoArray = [];
    $cuencaArray = [];
    $fechaArray = [];

    ob_end_clean();
    $mediciones = array();
    if(isset($_SESSION['arrayBackup'])){
      $mediciones = $_SESSION['arrayBackup'];
    }
    class Medicion {
        public $cuenca;
        public $pozo;
        public $psi;
        public $fechaMedida;
        public $horaMedida;
        public function __construct($cuen,$poz,$ps,$fecha,$hora)
        {
            $this->cuenca = $cuen;
            $this->pozo = $poz;
            $this->psi = $ps;
            $this->fechaMedida = $fecha;
            $this->horaMedida = $hora;
        }
        public function verDatos(){
            echo "Cuenca: {$this->cuenca}<br/>Pozo: {$this->pozo}<br/>PSI: {$this->psi}<br/>Fecha de medición: {$this->fechaMedida}<br/>Hora de Medición: {$this->horaMedida}<br/>";
        }
        public function getCuenca(){
          return $this->cuenca;
        }    
        public function getPozo(){
            return $this->pozo;
        }
        public function getPsi(){
            return $this->psi;
        }
        public function getFecha(){
            return $this->fechaMedida;
        }
        public function getHora(){
            return $this->horaMedida;
        }
    }
    if(isset($_GET['cuenca'])){
        $mediciones[count($mediciones)] = new Medicion($_GET['cuenca'],$_GET['pozo'],$_GET['psi'],$_GET['fechaMedida'],$_GET['horaMedida']);
        $_SESSION['arrayBackup'] = $mediciones;
      }

      if(isset($_POST['borrarReg'])){
        session_unset();
        $mediciones = [];
      }

      if(isset($_POST['generate'])){
        if(count($mediciones) > 2) {
          require_once ('jpgraph-4.4.1/src/jpgraph.php');
          require_once ('jpgraph-4.4.1/src/jpgraph_bar.php');
          foreach($mediciones as $medicion => $index){
            array_push($psiArray, $mediciones[$medicion]->getPsi());
            array_push($cuencaArray, $mediciones[$medicion]->getCuenca());
/*             array_push($pozoArray, $mediciones[$medicion]->getPozo());
 */            array_push($fechaArray, $mediciones[$medicion]->getFecha());
            $tempPozo = $mediciones[$medicion]->getPozo();
            $tempFecha = $mediciones[$medicion]->getFecha();
            $tempHora =  $mediciones[$medicion]->getHora();
            $tempPush = $tempPozo . " " . $tempFecha . " " . $tempHora;
            array_push($pozoArray, $tempPush);
        }
        $data1y=$psiArray;
  
  
        $graph = new Graph(1800,1220,'auto');
        $graph->SetScale("textlin");

        $theme_class=new UniversalTheme;
        $graph->SetTheme($theme_class);
        
        $graph->yaxis->SetTickPositions(array(0,10,20,30,40,60,80,100,200,300,400,500,600,700,1000,2000,3000));
        $graph->SetBox(false);
        $graph->SetMargin(100,60,40,45);







        $graph->yaxis->SetTitleMargin(20);
        $graph->ygrid->SetFill(false);
        $graph->xaxis->SetTickLabels($pozoArray);
        $graph->xaxis->title->Set("POZOS");
        $graph->yaxis->HideLine(false);
        $graph->yaxis->HideTicks(false,false);
        $graph->yaxis->title->Set("PSI");
        $graph->yaxis->title->SetMargin(5);

        $b1plot = new BarPlot($data1y);
        
        $gbplot = new GroupBarPlot(array($b1plot));
        $graph->Add($gbplot);
        
        
        $b1plot->SetColor("white");
        $b1plot->SetFillColor("#cc1111");
        
        
        $graph->title->Set("Mediciones de PSI");
        
        
        $gdImgHandler = $graph->Stroke(_IMG_HANDLER);
         
        $fileName = "graph.png";
        $graph->img->Stream($fileName);
         
  
        }else{
          echo "No se han ingresado suficientes mediciones";
        }

        }
        
      

?>



<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Medidor</title>
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/css/bootstrap.min.css"
    />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"
    />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;700&display=swap"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="index.css" />

  </head>
  <body>
    <section>
      <nav class="navbar navbar-expand-md navbar-dark bg-dark">
        <a class="navbar-brand" href="#">MedidorApp</a>
        <button
          class="navbar-toggler"
          type="button"
          data-toggle="collapse"
          data-target="#navbarNav"
          aria-controls="navbarNav"
          aria-expanded="false"
          aria-label="Toggle navigation"
        >
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
          <ul class="navbar-nav justify-content-center">
            <li class="nav-item">
              <a class="nav-link" href="index.php">Inicio</a>
            </li>
            <li class="nav-item">
              <a
                class="nav-link"
                href="#"
                data-toggle="modal"
                data-target="#modal-historico"
                >Historico de registros</a
              >
            </li>
            <div class="nav-item" >
              <form action="index.php" method="POST">
              <input class="nav-link" id="buttonSave" onpointermove="this.style.color=' rgb(201, 201, 201)'"
            onMouseOut="this.style.color='rgb(163, 162, 162)'"type="submit" name="generate" value="Generar gráfica" style="background-color: transparent !important;
  color: rgb(163, 162, 162) !important;
  border: none;
  font-size: 20px;
  vertical-align: center;
  ">
              </form>
            
          </div>
          </ul>
        </div>
      </nav>
      <div class="jumbotron jumbotron-fluid">
        <div class="container">
          <h1 class="display-4">¡Registro de mediciones PSI de PDVSA!</h1>
          <p class="lead">
            Ingrese un nuevo registro, visualice los registros anteriores o
            genere una gráfica...
          </p>
        </div>
      </div>
    </section>

    <div class="form-contenedor">
      <form action="index.php" method="GET">
        <div class="form-group">
          <label for="exampleFormControlSelect1">Seleccione Cuenca</label>
          <select
            class="form-control"
            id="exampleFormControlSelect1"
            title="Seleccionar Cuenca"
            name="cuenca"
          >
            <option>CUENCA ORIENTAL</option>
            <option>CUENCA MARACAIBO - FALCÓN</option>
            <option>CUENCA BARINAS - APURE</option>
            <option>CUENCA TUY - CARIACO</option>
          </select>
        </div>
        <div class="form-group">
          <label>Pozo</label>
          <input
            type="text"
            class="form-control"
            pattern="[A-Za-z0-9\s]{1,50}"
            placeholder="Ingrese pozo petrolero"
            title="Solamente letras o números"
            name="pozo"
            required
          />
        </div>
        <div class="form-group">
          <label>PSI</label>
          <input
            type="number"
            class="form-control"
            placeholder="Ingrese medición de la válvula"
            title="Solamente números"
            name="psi"
            step="any"
            required
          />
        </div>
        <div class="form-group">
          <label>Fecha medición</label>
          <input
            type="date"
            class="form-control"
            id="date"
            placeholder="Ingrese pozo petrolero"
            name="fechaMedida"
            required
          />
        </div>
        <div class="form-group">
          <label for="appt">Ingrese la hora de la medición (Horario laboral: 6 AM - 10 PM): </label>
          <input
            type="time"
            id="appt"
            name="horaMedida"
            value="22:00"
            min="06:00"
            max="22:00"
            required
          />
        </div>
        <div class="button-container">
          <button type="submit" class="btn btn-primary" id="buttonRegistrar">Registrar</button>
        </div>
      </form>
    </div>

    <div
      class="modal fade"
      id="modal-historico"
      tabindex="-1"
      role="dialog"
      aria-labelledby="myModalLabel"
      aria-hidden="true"
    >
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Registros guardados:</h5>
            <button
              type="button"
              class="close"
              data-dismiss="modal"
              aria-label="Close"
            >
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
           <?php 
               foreach($mediciones as $medicion => $index){
                $mediciones[$medicion]->verDatos();
                echo "<br/>";
            } 
          ?> 
          </div>
          <div class="modal-footer">
            <form action="index.php" method="POST">
              <input type="hidden" name="borrarReg">
              <button type="submit" class="btn btn-primary" id="buttonBorrar">Borrar Registros</button>
            </form>
          </div>
        </div>
      </div>
    </div>
    
    <div>
      <?php 
        if(isset($_POST['generate'])){
          echo "<img src='graph.png' >";
        }
      
      ?>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.1/umd/popper.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/js/bootstrap.min.js"></script>
  </body>
</html>
