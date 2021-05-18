@extends('app' )

@section('title')
    File upload example
@endsection
   

@section('content')

    <div class="panel rounded-3xl panel-primary bg-blue-100 p-8">
      <div class="panel-heading"><h2>Gestion des fichiers Kiosk</h2></div>
      <div class="panel-body">
   
        @if ($message = Session::get('success'))
        <div class="alert alert-success alert-block">
            <button type="button" class="close" data-dismiss="alert">×</button>
                <strong>{{ $message }}</strong>
        </div>
        @endif
  
        @if (count($errors) > 0)
            <div class="alert alert-danger">
                <strong>Whoops!</strong> There were some problems with your input.
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif


        <form enctype="multipart/form-data" class="file-xfert" class="w-full max-w-lg">
            @csrf
            <div class="row flex">
  
                <div class="col-md-8 pl-4">
                    <input id="file" type="file" name="file" class="outline-none form-control rounded-lg" placeholder="Choisir un fichier">
                    <button id="clearFileButton" class="button-clr uppercase px-8 py-2 rounded-full bg-yellow-500 text-blue-50 max-w-max shadow-sm hover:shadow-lg"><i class="fas fa-trash"></i></button>
                </div>

                <div class="col-md-8 pl-4">
                    <input id="url" type="text" name="url" class="form-control rounded-lg" placeholder="Or an url  (http://...)">
                </div>
   
                <div class="col-md-3">
                    <button class="uppercase px-8 py-2 rounded-full bg-green-600 text-blue-50 max-w-max shadow-sm hover:shadow-lg upload-file">Envoyer</button>
                </div>

                <div class="validate-butt col-md-12 content-center ">
                    <button class="uppercase px-8 py-2 rounded-full bg-green-200 text-blue-50 max-w-max shadow-sm hover:shadow-lg save-changes pbs-center">Valider la nouvelle selection du tableau</button>
                </div>
   
            </div>
        </form>

        <div class="row content-center">
            <div class='col-md-12'>
                <div id="msgarea" class="msgarea rounded-lg m-4 p-2"></div>
            </div>
        </div>
      </div>

      <div id='table-results'></div>
      <table id='filetable' class="table table-bordered data-table">

            <tbody>
            </tbody>
       </table>

        <?php //var_dump($params); ?>

        <div>
            <p>Remaining tasks :</p>
            <ul class="list-disc">
                <li> [x] Save data in params files </li>
                <li> [x] Save urls ins the urls file</li>
                <li> [x] Check file extensions & url format </li>
                <li> [x] Clear file field </li>
                <li> [-] Group Kiosks ID </li>
                <li> [x] Set the radio btns to reflect actuals</li>
                <li> [x] Normalize file names</li>
                <li> [x] Warning @ save if no button is activated for one kiosk</li>
                <li> [x] Warning when erasing an active file</li>
                <li> Authentication </li>
                <li> Download files </li>
                <li> [x] Manage kiosk list (in INI file ?)</li>
                <li> Remote force updates for one kiosk</li>
                <li> GUI kiosks params management (add / remove / name kiosks) </li>
                <li> Log kiosk updates & other actions </li>
                <li> Cosmetic </li>
            </ul>
        </div>
  

    </div>

@endsection