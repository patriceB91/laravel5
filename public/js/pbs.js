/* Global vars */
var headcolor = 'bg-blue-300'
var highlightcolor = 'bg-blue-200'
var kioskIDS = []

function getData(cb_func) {
    $.ajax({
      url: "/file-list-v2",
      success: cb_func
    });
}

function capitalizeFirstLetter(string) {
    return string.charAt(0).toUpperCase() + string.slice(1);
}

/**
 * Handle deletes from kiosk trazsh
 */
function deletes ()     // input[name='kioskradio0']
    {   
        // Get the del id
        var className = this.className.match(/del-\d+/);
        var rid =  className[0].split('-')[1]
        // Know if we trash file or url
        var classType = this.className.match(/deltype-\w+/);
        var fType = classType[0].split('-')[1]

        // Get the name to be erased & set the action & hihlight
        $('.fileid-'+rid).addClass('hover:'+highlightcolor)
        var fName = $('.fileid-'+rid).html()
        var ajUrl = (fType === 'url') ? "/url-delete" : "/file-delete"
        var usedMsg = ''
        kioskIDS.forEach(function(kid) {
            if($("#"+rid+"-"+kid).prop('checked')) {
                usedMsg += (usedMsg == '') ? "\n Utilisé par le(s) kiosks " + kid : ', ' + kid
            }
        })

        if(confirm("Confirmez vous l'effacement du fichier " + fName + usedMsg)) {
            console.log("fName " + fName + " URL " +ajUrl)
            $.ajax({
                url: ajUrl,
                type: 'POST',
                data: { actType: fType, fName: fName},
                // processData: false,
                // contentType: false,
                headers: {
                    'X-CSRF-Token': $('form.file-xfert [name="_token"]').val()
                },
                success: function(data) {
                    $('.fileid-'+rid).closest('tr').remove()
                    printMsg(data)
                }
            });

        } else {
            $('.fileid-'+rid).removeClass('hover:'+highlightcolor)
        }
        
    }

/**
 * Get the max ID value
 * @param {*} table 
 * @param {*} colSelector 
 */
function maxIntValue(table, colSelector) {
    var max = 0;
    table.column(colSelector).data().each(function( idx){
        max = idx > max ? idx : max;
    }) 
    return parseInt(max);
}



$(document).ready(function() {
    // alert('Doc loaded OK')

    // getFiles()

    // Save changes initaily disabled 
    $(".save-changes").prop("disabled",true);

    getData(function(data){ 
        var columns = [];
        // data = JSON.parse(data.data);

        columnNames = data.titres    // Object.keys(data.titres);
        kioskIDS = data.kiosksIDS
        // columnNames = Object.keys(data.data[0]);

        columns.push({data: 'id', name: 'id', title: columnNames[0], class: "idNum"})
        columns.push({data: 'filename', 
                      name: 'name', 
                      title: columnNames[1],
                      createdCell: function (td, cellData, rowData, row, col) {
                          $(td).addClass(`fileid-${rowData.id}`);
                      } })
        for (const key in kioskIDS) {
            columns.push({data: '',
                    render: (data,type,row) => {
                        retVal = `<input type='radio' id='${row.id}-${kioskIDS[key]}' name='kioskradio${key} ' value='${kioskIDS[key]}' class='k-radio kioskradio-${kioskIDS[key]}'>`
                        return retVal;
                    },
                title: "Kiosk " + columnNames[parseInt(key, 10)+2],
                name: kioskIDS[key]})
        }
        columns.push({data: '', 
                      render: (data,type,row) => {
                               return `<i class="fas fa-trash del-${row.id} deltype-${row.ftype}" ></i>`;
                              },
                      name:'actions',
                      title: columnNames[4]
        })

        // Initialize Datatable
        var fileselTbl = $('#filetable').DataTable( {
            data: data.data,
            columns: columns,
            paging:   false,
            ordering: false,
            info:     false,
            searching: false
        } );

        /**
         * Preset actual selection
         */
        var dataSet = data.selected
        var allDataRows = data.data
        for (const [selkey, selvalue] of Object.entries(dataSet)) {
            for (const [rowkey, rowvalue] of Object.entries(allDataRows)) {
                if(selvalue == rowvalue.filename) {
                    $("#"+rowvalue.id+"-"+selkey).prop('checked', true);
                }
            }
        }

        /**
         * Add some styles
         */
        $('tbody tr').addClass('hover:'+highlightcolor)
        $('thead tr').addClass(headcolor)

        /*
         * Track radio actions 
         */
        $(".k-radio").on("change", function() 
        {
            // alert("Triggered by " + this.id )
            $(".save-changes").prop("disabled",false);
            $(".save-changes").addClass('animate-pulse bg-green-600')
        });

        /**
         * Handle trash buttons - Erase current file / url & refresh table
         */
        $(".fa-trash").on("click", deletes);

        /**
         * Handle the clear file input 
         */
        $('#clearFileButton').on('click', function(e) {
            e.preventDefault
            $("#file").val(null);
        })


        /*
         * Save the changes to param files.
         */
        $(".save-changes").on('click', function (e) {
            e.preventDefault();
            var iz_checked = false         // Assume no ckeck

            var msg = ''
            var kiosksFilesArray = {}       // To store the pairs id / files-urls

            // Check if eack Kiosk has one check (to warn)
            kioskIDS.forEach(function(krid) {
                $('.kioskradio-'+krid).each(function(){
                    if($(this).is(':checked')) {
                        var fid = this.id.split('-')[0]             // 1st part of the ID is the row id
                        kiosksFilesArray[krid] = $('.fileid-'+fid).html()
                        iz_checked = true
                    }
                });

                // Create msg or append..
                if(!iz_checked) {
                    if(msg == '') {
                        msg = 'Pas de présentation choisie pour le(s) kiosk(s) ' + krid
                    } else {
                        msg += ',' + krid
                    }
                }
                iz_checked = false      // Reset for next kiosk
            })

            if(confirm(msg + "\n Confirmez vous la selection ?")) {
                $.ajax({
                    url: "/save-kiosks",
                    type:'POST',
                    data: {data: kiosksFilesArray},
                    headers: {
                        'X-CSRF-Token': $('form.file-xfert [name="_token"]').val()
                    },
                    success: function(data) {
                        printMsg(data)
                    }
                })

            }
        
        });
        
    });

    /**
     * Upload the file or url
     * @Depends on global var allowedFilesTypes 
     */
    $(".upload-file").click(function(e){

        e.preventDefault();

        var fileselTbl = $('#filetable').DataTable()
        var fd = new FormData();

        var files = $('#file')[0].files;
        // var fileName = $('#file').val()
        var fSplit = $('#file').val().split('.')
        var extension = fSplit[fSplit.length - 1].toLowerCase()

        var errMsg = ''
        // Check if only one of the 2 fields is set
        if( $('#url').val() && $('#file').val() ||
             (!$('#url').val() && !$('#file').val()) ) 
        {
            errMsg += "Veuillez choisir un fichier OU saisir un url \n"
        } else {
            // Check if URL saisi et invalide
            if ($('#url').val() && !($('#url').val().indexOf('http') == 0)) {
                console.log($('#url').val())
                errMsg += "L'URL doit commencer par http\n"
            }

            if(!allowedFilesTypes.includes(extension)) { 
                errMsg += 'Attention, le type de fichier est invalide.\n'
            } 
        }

        // @Todo code this
        var fType = 'file'

        if( errMsg != '' ) {
            alert ('Attention : \n\n' + errMsg)
        } 
        else 
        {
            // if(files.length > 0 ){
            fd.append('file',files[0])
            fd.append('url', $('#url').val())
            $.ajax({
                url: "/file-upload",
                type:'POST',
                data: fd,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-Token': $('form.file-xfert [name="_token"]').val()
                },
                success: function(data) {
                    var nextSeqNum = maxIntValue(fileselTbl,'.idNum')+1
                    filename = data.data[0]
                    // console.log("next ID " + nextSeqNum)
                    var cols = {}   
 
                    cols.id = nextSeqNum;
                    cols.filename = filename;
                    for (const key in kioskIDS) {
                        cols['kiosk'+kioskIDS[key]] = `<input type='radio' id='${nextSeqNum}-${kioskIDS[key]} ' name='kioskradio${key} ' value='${kioskIDS[key]}' class='k-radio kioskradio-${kioskIDS[key]}'>`
                    }
                    cols.actions = `<i class="fas fa-trash del-${nextSeqNum} deltype-`+ fType + `" ></i>`

                    // console.log(cols)

                    const newRow = fileselTbl.row.add(cols).draw(false).node()

                    $(".k-radio").unbind()
                    $(".k-radio").on("change", function()     // input[name='kioskradio0']
                    {
                        // alert("Triggered by " + this.id )
                        $(".save-changes").prop("disabled",false);
                    });

                    // Reattach actions to new elements (radios & trash)
                    $(`.del-${nextSeqNum}`).on("click", deletes)

                    printMsg(data);
                }
            });
        }
    }); 

    /**
     * Display the message in the area
     * Color is set regarding the message importance : 
     * @param data 
     */
    function printMsg (data) {
      $('#msgarea').empty()
      if(!$.isEmptyObject(data.msg)) {
          console.log(data.msg);
          $('#msgarea').css('display','block').append('<strong>'+data.msg+'</strong>')

          if(!$.isEmptyObject(data.status)) {
            if(data.status == 'OK') {
                $('#msgarea').addClass('bg-green-400') 
            } else {
                $('#msgarea').addClass('bg-red-500')
            }
          } else {
            $('#msgarea').removeClass('bg-red-500 bg-green-400')
          }
      }

    }  

});