@extends('layouts.app')

@section('title', '| Nova Demanda')

@section('content')

<div class='col-lg-4 col-lg-offset-4'>

    <h1><i class='fa fa-key'></i> Nova demanda</h1>
    <hr>
    {{-- @include ('errors.list') --}}

    <form method="POST" action="{{ route('materiais.demanda.store_file') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <input name="file" id="file" type="file" accept=".xlsx, .txt, .csv" class="form-control"><br/>
                        <input type="submit"  value="Novo" class="btn btn-success">
                    </div>
        </form>
    {{ Form::close() }}

                 <div class="progress">
                            <div class="bar"></div >
                            <div class="percent">0%</div >
                        </div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
<script src="http://malsup.github.com/jquery.form.js" defer></script>
<script type="text/javascript">
$(document).ready(function(){
    var $head = $("head");
    var $headlinklast = $head.find("link[rel='stylesheet']:last");
    var linkElement = "<link rel='stylesheet' "+
    'href="'+
    "{{ asset('css/progress_bar.css') }}"+
    '" '+
    " type='text/css' media='screen'>";
    if ($headlinklast.length){
       $headlinklast.after(linkElement);
    }
    else {
       $head.append(linkElement);
    }

    function validate(formData, jqForm, options) {
        var form = jqForm[0];
        if (!form.file.value) {
            alert('File not found');
            return false;
        }
    }
 
    (function() {
 
    var bar = $('.bar');
    var percent = $('.percent');
    var status = $('#status');
 
    $('form').ajaxForm({
        beforeSubmit: validate,
        beforeSend: function() {
            status.empty();
            var percentVal = '0%';
            var posterValue = $('input[name=file]').fieldValue();
            bar.width(percentVal)
            percent.html(percentVal);
        },
        uploadProgress: function(event, position, total, percentComplete) {
            var percentVal = percentComplete + '%';
            bar.width(percentVal)
            percent.html(percentVal);
        },
        success: function() {
            var percentVal = 'Wait, Saving';
            bar.width(percentVal)
            percent.html(percentVal);
        },
        complete: function(xhr) {
            status.html(xhr.responseText);
            //alert(xhr.responseText + ' Uploaded Successfully');
            window.location.href = "/materiais/demanda";
        }
    });
     
    })();
});
</script>
</div>

@endsection