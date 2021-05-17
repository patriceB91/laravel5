<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
  
class FileUploadController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function fileUpload()
    {
        $kiosks = $this->readKioskParams();
        return view('fileUpload',['kiosk' => $kiosks]);
    }


    /**
     * Return a listing of the files, fo datatable.
     *
     * @return \Illuminate\Http\Response
     */
    public function getFileListV2() {
        $files= scandir(public_path('kiosks'));
        $urlList = $this->readUrls();
        $allowedFileExt = explode(',', env("PBS_MIMES"));

        $id = 1;
        $fileList = array();
        // Get Kiosk IDs from parameters
        $kparameters = $this->readKioskParamsIni();
        foreach ($kparameters as $k => $row) { $kioskList[] = $row['id']; }

        // Get active files from params files
        $selFiles = $this->readParamFiles($kioskList);

        // Build Titles Records
        $titres = array('ID', 'Noms de fichiers');
        $kioskCols = array();
        foreach($kioskList as $key => $kioskID) {
            array_push($titres, $kioskID); 
            array_push($kioskCols, $kioskID);
        }
        array_push($titres, 'Delete'); 

        // Build data records & give selected files 
        foreach($files as $key => $value) {
            $row = array();
            $fext = pathinfo($value, PATHINFO_EXTENSION);
            $isSelected = in_array($value, $selFiles) ? "checked" : "";
            if($value!='.' && $value != '..' && in_array($fext, $allowedFileExt)) {
                $row = array('id' => $id, 'filename' => $value, 'ftype' => 'file', 'selected' => $isSelected);
                foreach($kioskCols as $key => $colID) {
                    $row[$colID] = $colID;
                }
                // $row = array_merge($rowHead, $kioskCols);
                $row['action'] = "actions a faire for $id";
                $fileList[] = $row;
                $id++;
            }
        }

        /**
         * 
         */
        foreach($urlList as $key => $value) {
            $isSelected = in_array($value, $selFiles) ? "checked" : "";
            $row = array('id' => $id, 'filename' => $value, 'ftype' => 'url');
            foreach($kioskCols as $key => $colID) {
                $row[$colID] = $colID;
            }
            $row['action'] = "actions a faire for $id";
            $fileList[] = $row;
            $id++;
        }
          
        $retValues = array("status" => "OK", "msg" => 'OK', "data" => $fileList, "titres" => $titres, "kiosksIDS" => $kioskCols, "selected" => $selFiles);
        return response()->json($retValues);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function fileUploadPost(Request $request)
    {
        $filesList = array();
        if( $request->hasFile('file') )  {
            $request->validate([
                'file' => 'required|mimes:'.env('PBS_MIMES').'|max:2048',
            ]);

            // Clean file name
            $fileName = strtolower($request->file->getClientOriginalName());
            $fileName = str_replace(' ', '_', $fileName);
            $fileName = str_replace("\xE2\x80\x99", '_', $fileName);            //Remove apostrophe ("'" fails en utf-8..)
            $fileName = iconv( 'UTF-8', 'ASCII//TRANSLIT//IGNORE', $fileName );

            $request->file->move(public_path('kiosks'), $fileName);

           array_push($filesList, $fileName);
        }

        if($nUrl = $request->input('url')) {
            $this->updateUlrsList($nUrl);
            array_push($filesList, $nUrl);
        }

        $retValues = array("status" => 'OK', "data" => $filesList, "msg" => "Fichier transféré sous le nom : " . $fileName);

        return response()->json($retValues);
    }
    
    /**
     * Save the kiosk values
     */
    public function saveKiosks(Request $request) {

        foreach($request->data as $kid => $fileurl) {
            file_put_contents("kiosks/kiosk".$kid.".params", "filename=".$fileurl);
        }
        $retVal = array('status' => 'OK', 'msg' => "Modifications sauvegardées");

        // return json_encode($retVal);
        return response()->json($retVal);
    }


    /**
     * Search kiosks params files (kioskN.params) and return the ids available.
     *
     * @return array of available kiosks
     */    
    private function readKioskParams() {
        $res = array();
        foreach (glob("kiosks/*.params") as $filename) {
            $kiname = explode('.',$filename);
            $kid = $kiname[0];
            $res[] = str_replace('kiosks/kiosk', '', $kid);
        }
        return $res;
    }

    /**
     * Search kiosks params files (kioskN.params) and return the ids available.
     *
     * @return array of available kiosks
     */    
    private function readKioskParamsIni() {
        $content = Storage::get('parameters.ini');
        $params = parse_ini_string($content, true);
        return $params;
    }

    /**
     * Read files used in the param files (kioskN.params) and return .
     *
     * @return array 'kid' => 'used pres file' 
     */ 
    private function readParamFiles($karray) {
        $res = array();
        foreach($karray as $kid) {
            // $contents = Storage::disk('local')->get(public_path().'/'.env('PBS_KIOSK_DIR').'/kiosk'.$kid.'.params');
            //$fp = fopen(public_path().'/'.env('PBS_KIOSK_DIR').'/kiosk'.$kid.'.params','r');
            $contents = file_get_contents(public_path().'/'.env('PBS_KIOSK_DIR').'/kiosk'.$kid.'.params');
            $parsedLines = $lines = preg_split("/\r?\n|\r/", $contents);
            $cnt = count($parsedLines);

            $i=0;
            $line = '';
            // Loop untill max line or found the searched param
            while($i < $cnt && !str_starts_with(  $line , 'filename=' )) {
                $line = $parsedLines[$i];
                $i++;
            }
            if(str_starts_with (  $line, 'filename=' )) {
                $lArray = explode('=', $line);
                // echo "Kid " . $kid . " fn : " .$lArray[1]; 
                // $nv = array($kid => $lArray[1]);
                $res[$kid] = $lArray[1];
            } else {
                // $nv = array($kid => '');
                // $res = array_merge( $res, $nv );
                $res[$kid] = '$lArray[1]';
            }
        }

        return $res;
    }

    /**
     * Search kiosks params files (kioskN.params) and return the ids available.
     *
     * @return array of available kiosks
     */    
    private function writeKioskParams($kid, $presfile) {

        Storage::disk('local')->put(env('PBS_KIOSK_DIR').'/kiosk'.$kid.'.params', 'filename='.$presfile);

    }

    /**
     * Update url file list
     */
    function updateUlrsList($newUrl) {
        $urlFile = env('PBS_KIOSK_DIR').'/urls.list';
        $contents = file_get_contents($urlFile);
        $parsedLines = preg_split("/\r?\n|\r/", $contents);
        // Check if exists already
        $found = false;
        $rVal = 'notadded';
        foreach( $parsedLines as $rurl ) {
            if( trim($newUrl) == trim($rurl) ) $found = true;
        }
        if(!$found) {
            $contents .= "\r\n".$newUrl;
            if (file_put_contents($urlFile, $contents)) $rVal = 'added';
        }
        return response()->json($rVal);
    }

    /**
     * Read url file list 'urls.list'
     */
    private function readUrls() {
        $urlList = array();
        if(file_exists('kiosks/urls.list')) {
            $handle = @fopen('kiosks/urls.list', "r");

            if ($handle) {
                while (($line = fgets($handle, 4096)) !== false) {
                    if(trim($line) != '') {
                        array_push($urlList,$line);
                    }
                }
                if (!feof($handle)) {
                    echo "Erreur: fgets() a échoué\n";
                }
                fclose($handle);
            }
        }
        return $urlList;
    }

    /**
     * Delete url from file 
     * 
     * Return true if deleted
     */
    function urlDelete(Request $request) {
        $urlFile = env('PBS_KIOSK_DIR').'/urls.list';
        $newContent = '';
        $urlToDel = $request->fName;
        error_log("Url to del " . $urlToDel);
        $contents = file_get_contents($urlFile);
        $parsedLines = preg_split("/\r?\n|\r/", $contents);
        $found = false;
        $rVal = false;

        foreach( $parsedLines as $rurl ) {
            if( trim($urlToDel) != trim($rurl) ) { 
                $newContent .= $rurl."\r\n";
            } else {
                $rVal = true;
                $deleted = $rurl;
            }
        }
        
        var_dump($newContent);
        if (file_put_contents($urlFile, $newContent)) {
            echo("File written");
        }
        if($rVal) {
            $status = 'OK';
            $msg = "L'url à été supprimée.";
        } else {
            $status = 'OK';
            $msg = "Problème lors de la suppression de l'url";
        }
        $retVal = array('status' => $status, 'msg' => $msg, 'deleted' => $deleted);

        return response()->json($retVal);
    }

    /**
     * Delete  file
     */
    function fileDelete(Request $request) {

        if( unlink(public_path().'/'.env('PBS_KIOSK_DIR').'/'.$request->fName) ) {
            $retVal = json_encode(array("status" => "OK", "msg" => "Fichier supprimé"));
        } else {
            $retVal = json_encode(array("status" => "KO", "msg" => "Le fichier n'a pas pu être supprimé"));
        }
        return response()->json($retVal);
    }


    /**
     * Add url to url.list
     */    
     function writeUrl() {

    }


    function saveConfig() {

    }

}
