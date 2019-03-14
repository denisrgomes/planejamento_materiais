@extends('layouts.app')

@section('title', '| Users')

@section('content') 
<link  href="https://cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/css/bootstrap-datetimepicker.min.css" />

<div class="col-lg-10 col-lg-offset-1">
    <h1><i class="fa fa-table"></i>Demanda de materiais</h1>
    <br />
    <div align="right">
      <button type="button" name="add" id="add_data" class="btn btn-default btn-sm">Novo</button>
      <a href="{{ route('materiais.demanda.export') }}" class="btn btn-default btn-sm">Exportar</a>
      <a href="{{ route('materiais.demanda.create') }}" class="btn btn-default btn-sm">Nova demanda upload</a>
    </div>
    <hr>
    <div class="table-responsive">
        <table id="demanda_materiais_table" class="table table-bordered table-striped" style="width:100%">

            <thead>
                <tr>
                    <th>Material</th>
                    <th>Descrição</th>
                    <th>Quantidade</th>
                    <th>Almoxarifado</th>
                    <th>Regional</th>
                    <th>Data programação</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tfoot>
                <tr>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>


<div id="demandaMaterialModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
      <div class="modal-content">
        <form method="post" id="demandaMaterialForm">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">Cadastrar material</h4>
          </div>
          <div class="modal-body">
            {{csrf_field()}}
            <span id="form_output"></span>
            <div class="form-group">
              <label>Codigo Material</label>
              
              <select class="form-control" name="codigo_material" id="codigo_material">
              </select>
            </div>
            <div class="form-group">
              <label>Descrição Material</label>
              <input type="text" name="descricao_material" id="descricao_material" class="form-control" readonly="true"/>
            </div>
            <div class="form-group">
              <label>Quantidade</label>
              <input type="number" name="quantidade_material" id="quantidade_material"  step=".001" class="form-control"/>
            </div>
            <div class="form-group">
              <label>Almoxarifado</label>
              <input type="text" name="centro_logistico" id="centro_logistico"  class="form-control"/>
            </div>
            <div class="form-group">
              <label>Regional</label>
              <input type="text" name="regional" id="regional"  class="form-control"/>
            </div>
            <div class="form-group">
              <label>Data programação</label>
              <input type="text" name="data_programacao" id="data_programacao"  class="form-control date"/>
            </div>
          </div>
          <div class="modal-footer">
            <input type="hidden" name="demanda_id" id="demanda_id" value="" />
            <input type="hidden" name="button_action" id="button_action" value="insert" />
            <input type="submit" name="submit" id="action" value="Cadastrar" class="btn btn-info" />
            <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
          </div>
        </form>
      </div>
  </div>
</div>

<div id="deleteMaterialModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
      <div class="modal-content">
        <form method="post" id="deleteMaterialForm">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">Confirmar</h4>
          </div>
          <div class="modal-body">
            {{csrf_field()}}
            <p>Deseja deletar o material selecionado?</p>
          </div>
          <div class="modal-footer">
            <input type="hidden" name="demanda_delete_id" id="demanda_delete_id" value="" />
            <input type="submit" name="submit" id="delete" value="Deletar" class="btn btn-danger" />
            <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
          </div>
        </form>
      </div>
    </div>
  </div>


<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
<script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js" defer></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.6/moment.min.js"></script>                       
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/js/bootstrap-datetimepicker.min.js"></script>
<script src="{{ asset('js/jquery.mask.js') }}"></script>

<script>;


$(document).ready(function($){
    $('.date').mask('00/00/0000');
});

$(document).ready(function(){

  $('#add_data').click(function(){
    $('#demandaMaterialModal').modal('show');
    $('#demandaMaterialForm')[0].reset();
    $('#form_output').html('');
    $('#button_action').val('insert');
    $('#action').val('Cadastrar');
    
    populateCodigoMaterialSelect();         
  });

  $('#codigo_material').change(function(){

    var value = $(this).val();
    $.ajax({
      url:"{{route('materiais.demanda.get_descricao_material_select')}}",
      method:'get',
      data:{codigo_material:value},
      dataType:'json',
      success: function(data){
          $('#descricao_material').val(data.descricao_material);
        }
    });
  });

  var populateCodigoMaterialSelect = function(codigo_material){
    $.ajax({
      url:"{{route('materiais.demanda.get_codigo_material_select')}}",
      method:'get',
      //data:form_data,
      dataType:'json',
      success: function(data){
        var select = '<option value = ""></option>';
          for(var count = 0; count < data.length; count++){
            select +='<option value = "'+ data[count]+'">'+data[count]+'</option>';
          } 
        $('#codigo_material').html(select);
        if(codigo_material !=null){
          $("#codigo_material > [value=" + codigo_material + "]").attr("selected", "true");
        }
      }
    });
  }

  $('#demandaMaterialForm').on('submit', function(event){
    event.preventDefault();
    var form_data = $(this).serialize();
    $.ajax({
      url:"{{route('materiais.demanda.store')}}",
      method:'post',
      data:form_data,
      dataType:'json',
      success: function(data){
        if(data.error.length >0){
          var error_html ='';
          for(var count = 0; count < data.error.length; count++){
            error_html += '<div class="alert alert-danger">'+data.error[count]+ '</div>';
          } 
          $('#form_output').html(error_html);
        }else{

          if($('#button_action').val() == 'update'){
            $('#demandaMaterialForm')[0].reset();
            $('#form_output').html('');
            $('#demandaMaterialModal').modal('hide');
            $('#demanda_materiais_table').DataTable().ajax.reload();
          }else{
            $('#form_output').html(data).success;
            $('#demandaMaterialForm')[0].reset();
            $('#action').val('Cadastrar');
            $('#modal-header').text('Cadastrar demanda');
            $('#demanda_materiais_table').DataTable().ajax.reload();
          }
          
        }

      }
    });
  });

  $(document).on('click', '.edit', function(){
    var id = $(this).attr('id');
    $.ajax({
      url:"{{route('materiais.demanda.edit')}}",
      method:'get',
      data:{id:id},
      dataType:'json',
      success:function(data){

        populateCodigoMaterialSelect(data.codigo_material);
        $('#descricao_material').val(data.descricao_material);
        $('#quantidade_material').val(data.quantidade_material);
        $('#centro_logistico').val(data.centro_logistico);
        $('#regional').val(data.regional);
        $('#data_programacao').val(data.data_programacao);
        $('#demanda_id').val(id);
        $('#action').val('Editar');
        $('.modal-title').text('Editar demanda');
        $('#button_action').val('update');
        $('#demandaMaterialModal').modal('show');
        
      } 
    });
  });

  $(document).on('click', '.delete', function(){

    var id = $(this).attr('id');
    $('#demanda_delete_id').val(id);
    $('#deleteMaterialModal').modal('show');
    $('#delete').attr("disabled", false);
    
  });

  $('#deleteMaterialForm').on('submit', function(event){
    $('#delete').attr("disabled", true);
    event.preventDefault();
    var form_data = $(this).serialize();
    $.ajax({
        url: "{{route('materiais.demanda.delete')}}",
        type: 'DELETE',
        dataType: 'json',
        data:form_data,
        method: 'post',
        success:function (data) {
          $('#deleteMaterialModal').modal('hide');
          $('#demanda_materiais_table').DataTable().draw(false);
        }
    });
  });


  var table = $('#demanda_materiais_table').DataTable({
                scrollY:        "300px",
                scrollX:        true,
                scrollCollapse: true,
                columnDefs: [
                    { width: '20%', targets: 0 }
                ],
                fixedColumns: false,
                "resposive":true,
                "orderCellsTop": true,
                "fixedHeader": true,
               "processing": true,
               "serverSide": true,
               "ajax": '{{ route("materiais.demanda.getData") }}',
               "columns": [
                        { data: 'codigo_material', name: 'codigo_material' },
                        { data: 'descricao_material', name: 'descricao_material' },
                        { data: 'quantidade_material', name: 'quantidade_material' },
                        { data: 'centro_logistico', name: 'centro_logistico' },
                        { data: 'regional', name: 'regional' },
                        { data: 'data_programacao', name: 'data_programacao' },
                        { data: 'action', name: 'action', orderable:false, searchable:false},
                       
                     ],
                initComplete: function (settings, json) {
                this.api().columns([0,1,2,3,4]).every( function () {
                    var column = this;
                    var select = $('<select><option value=""></option></select>')
                        .appendTo( $(column.footer()).empty() )
                        .on( 'change', function () {
                            var val = $.fn.dataTable.util.escapeRegex(
                                $(this).val()
                            );
     
                            column
                                .search( val ? '^'+val+'$' : '', true, false )
                                .draw();
                        } );
                    
                     // Capture the data from the JSON to populate the select boxes with all the options
                      var extraData = (function(i) {
                        switch(i) {
                        case 0:
                          return json.allCodigoMaterial;
                        case 1:
                          return json.allDescricaoMaterial;
                        case 2:
                          return json.allQuantidade;
                        case 3:
                          return json.allCentroLogistico;
                        case 4:
                          return json.allRegional;
                        }

                      })(column.index());
                         // Draw select options
                        extraData.sort().forEach( function ( d ) {
                          if(column.search() === d){
                            select.append( '<option value="'+d+'" selected="selected">'+d+'</option>' )
                          } else {
                            select.append( '<option value="'+d+'">'+d+'</option>' )
                          }
                        } );
                } );
            }
               
    });

});
</script>
@endsection