<?php

/**
 * Description of uploadFile
 *
 * @author juanma
 */
class UploadFile {    
    /**
     *
     * @var array Tipos de ficheros admitidos para web PODCAST
     */
    private $allowed = array(
        "jpg"=>0,
        "png"=>0
    );
    /**
     * Constantes de la clase directorios por defecto y politicas de sobreescritura
     */
    const IMG_DIR = 'img/';
    const SONG_DIR = '../canciones/';
    const CONSERVAR = 1, RENOMBRAR = 2, SOBREESCRIBIR = 3;
    
    /**
     * @var string Nombre completo del archivo original
     */
    private $full_name; 
    /**
     * @var string Tipo mime del archivo original
     */
    private $type;
    /**
     * @var string Ruta temporal del archivo subido
     */
    private $tmp_name;
    /**
     * @var int Cógido de error
     */
    private $error;
    /**
     * @var int Tamaño del archivo
     */
    private $size;
    /**
     * @var string Nombre del archivo validado para subir (sin extensión)
     */
    private $name;
    /**
     * @var string Extensión
     */
    private $ext;
    /**
     * @var int Tamaño máximo permitido por la clase
     */
    private $max_size = 20000000;
    /**
     * @var string Ruta definitiva donde se subirá el archivo.
     */
    private $destination;
    /**
     * @var string Mensage de error actual.
     */
    private $error_message;
    /**
     *
     * @var int Politica en caso de que exista el archivo 
     */
    private $politica = self::SOBREESCRIBIR;
    /**
     *
     * @var boolean Marca la canción como subida
     */
    private $subida = false;
    /**
     * Constructor de la clase
     * @param array $file Datos del archivo (requerido)
     * @param String $dest Ruta de subida (opcional)
     * @param int $max_size Tamaño máximo del archivo (opcional)
     */
    function __construct($file) {
        $this->full_name = $file['name'];
        $this->type = $file['type'];
        $this->tmp_name = $file['tmp_name'];
        $this->size = $file['size'];
        $this->name = $this->generateName($file['name']);
        $this->ext = $this->generateExt($file['name']);        
        $this->error = $this->generateErrorCode($file['error']);   
        $this->destination = $this->generateDestination();
        $this->error_message = $this->generateErrorMessage();        
    }
    /**
     * Genera un nombre válido para el archivo
     * @param String $name Nombre del archivo
     * @return string Nombre válido 
     */
    private function generateName($name) {
        $goodName = pathinfo($name, PATHINFO_FILENAME);
        return Utils::strNormailze($goodName);
    }
    /**
     * Obtiene la extensión del archivo
     * @return string Extensión del archivo
     */
    private function generateExt($name) {
        return Files::getFileExtension($name);
    }
    /**
     * Valida el archivo y genera el código de error correspondiente
     * @param int $error Código de error
     * @return int Código de error
     */
    private function generateErrorCode($error) {
        if ($error != 0) {
            return $error;
        }
        if ($this->size > $this->max_size) {
            return 101;
        }
        if (!isset($this->allowed[$this->ext])) {
            return 102;
        } else {
            return UPLOAD_ERR_OK;
        }
    }
    /**
     * Genera el mensage de error a partir del código
     * @return string Mensage de error
     */
    private function generateErrorMessage() {
        switch ($this->error) {
            case UPLOAD_ERR_OK: return "Todo ok";
            case UPLOAD_ERR_INI_SIZE: return " El fichero subido excede la directiva upload_max_filesize de php.ini";
            case UPLOAD_ERR_FORM_SIZE: return "El fichero subido excede la directiva MAX_FILE_SIZE especificada en el formulario HTML";
            case UPLOAD_ERR_PARTIAL: return " El fichero fue sólo parcialmente subido.";
            case UPLOAD_ERR_NO_FILE: return "No se subió ningún fichero.";
            case UPLOAD_ERR_NO_TMP_DIR: return "Falta la carpeta temporal.";
            case UPLOAD_ERR_CANT_WRITE: return "No se pudo escribir el fichero en el disco.";
            case UPLOAD_ERR_EXTENSION: return "Una extensión de PHP detuvo la subida de ficheros.";
            case 101: return "Ha superado el tamaño máximo establecido por la clase";
            case 102: return "Tipo de archivo no permitido";
            case 103: return "Archivo corrupto no ha sido subido por POST";
            case 104: return "El tipo de archivo no teiene ninguna ruta de subida";
            case 105: return "El directorio de subida no existe";
            default: return "Error de subida desconocido";
        }
    }
    /**
     * Genera la ruta de destino si entra un valor nulo o valida la ruta de ntrada en caso contrario
     * @param string $dest Ruta de destino
     * @return string Ruta de destino válida
     */
    private function generateDestination(){        
        if($this->ext==="jpg"){
            if(is_dir(self::IMG_DIR)){
                return self::IMG_DIR;
            }
        }
        if($this->ext==="mp3") {
            if(is_dir(self::IMG_DIR)){
                return self::SONG_DIR;
            }            
        }
        $this->error=$this->generateErrorCode(104);
        return null;
    }
    /**
     * Genera un nuevo nombre para el arvhivo si existe en destino
     * @param string $n nombre a verificar
     * @return string Ruta completa del archivo válido a subir
     */
    private function renameIfExists() {
        $i=1;
        $newName = $this->name; 
        while (file_exists($this->destination . $newName.$i. '.' . $this->ext)){
            $i++;
        }
        return $this->destination . $newName.$i . '.' . $this->ext;
    }
    /**
     * Sube el archivo de forma definitiva al servidor validando el origen del archivo y los errores existentes
     * @param boolean $overwrite True: Sobresscrive, false: asigna un nuevo nombre 
     * @param string $newName Nombre del archivo a subir
     * @param string $destination Ruta de subida del archivo
     * @return boolean true si correcto, false en caso contrario
     */
    public function upload() {
        if($this->subida){
            return false;
        }
        $url = $this->destination . $this->name . '.' . $this->ext;
        if(!$this->error === UPLOAD_ERR_OK){
            return FALSE;
        }
        if(!is_uploaded_file($this->tmp_name)){
            $this->error = 103;
            return FALSE;
        }        
        if(file_exists($url)){
            switch ($this->politica){
                case self::CONSERVAR : return FALSE;
                case self::SOBREESCRIBIR : return move_uploaded_file($this->tmp_name, $url);
                case self::RENOMBRAR : return move_uploaded_file($this->tmp_name, $this->renameIfExists());
                default : return FALSE;
            }
            $this->subida = true;
        }else {
            $this->subida = true;
            return move_uploaded_file($this->tmp_name, $url);
        }
    }
    
    /*
     *  getters y setters
     */
    function getFull_name() {
        return $this->full_name;
    }

    function getType() {
        return $this->type;
    }

    function getTmp_name() {
        return $this->tmp_name;
    }

    function getError() {
        return $this->error;
    }

    function getSize() {
        return $this->size;
    }

    function getName() {
        return $this->name;
    }

    function getExt() {
        return $this->ext;
    }

    function getMax_size() {
        return $this->max_size;
    }

    function getDestination() {
        return $this->destination;
    }

    function getError_message() {
        return $this->error_message;
    }

    function setMax_size($max_size) {
        $this->max_size = $max_size;
    }    
    function setDestination($dest){
        if(is_dir($dest)===true){
            $this->destination = $dest;
        }else{
            $this->error = 104;
            $this->destination = self::UPLOAD_GEN_DIR;
        } 
    }
    function setName($name) {
        $this->name = $name;
    }
    /*
    * toString
    */
    public function __toString() {
        $str = "<h5>Archivo:</h5><br/>";
        foreach ($this as $key => $value) {
            $str.=$key . " => " . $value . "<br/>";
        }
        return $str;
    }
    
    static function isMultiFile($files){
        if(isset($files['name']) && count($files['name']>1)){
            return true;
        }
        return false;
    }
    
    static function normalizeArray($files){
        $result = array();
        foreach ($files as $key => $array) {
            foreach ($array as $id => $value) {
                $result[$id][$key]=$value;
            }            
        }
        return $result;
    }

    static function multiUpload($files){
        if(self::isMultiFile($files)){
            $arrayFiles = self::normalizeArray($files);
            foreach ($arrayFiles as $value) {
                $tmp = new UploadFile($value);
                $tmp->upload();
            }
        }
        return null;
    }
}